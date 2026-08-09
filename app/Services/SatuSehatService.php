<?php

namespace App\Services;

use App\Models\MasterSatusehatConfig;
use App\Models\SatusehatToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SatuSehatService
{
    protected MasterSatusehatConfig $config;

    protected string $baseUrl;

    protected string $fhirBaseUrl;

    /**
     * Base URLs per environment.
     *
     * @var array<string, string>
     */
    protected const BASE_URLS = [
        'sandbox' => 'https://api-satusehat-stg.dto.kemkes.go.id',
        'production' => 'https://api-satusehat.kemkes.go.id',
    ];

    /**
     * FHIR identifier system for NIK lookups.
     */
    protected const NIK_SYSTEM = 'https://fhir.kemkes.go.id/id/nik';

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Load active SatuSehat configuration from database.
     *
     * @throws \RuntimeException
     */
    protected function loadConfig(): void
    {
        $config = MasterSatusehatConfig::where('is_active', true)->first();

        if (! $config) {
            throw new \RuntimeException(
                'Konfigurasi SatuSehat belum diatur atau tidak aktif. '
                .'Silakan atur di menu Master → SatuSehat.'
            );
        }

        $this->config = $config;
        $this->baseUrl = self::BASE_URLS[$config->environment] ?? self::BASE_URLS['sandbox'];
        $this->fhirBaseUrl = $this->baseUrl.'/fhir-r4/v1';
    }

    /**
     * Get a valid OAuth2 access token, using cached token if still valid.
     *
     * @return array{success: bool, token?: string, error?: string}
     */
    public function getAccessToken(): array
    {
        // Check for cached non-expired token
        $cached = SatusehatToken::where('expires_at', '>', now())->latest()->first();

        if ($cached) {
            return ['success' => true, 'token' => $cached->access_token];
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post(
                    $this->baseUrl.'/oauth2/v1/accesstoken?grant_type=client_credentials',
                    [
                        'client_id' => $this->config->client_id,
                        'client_secret' => $this->config->client_secret,
                    ]
                );

            if (! $response->successful()) {
                $body = $response->json();
                $errorMsg = $body['error_description'] ?? $body['error'] ?? 'Unknown auth error';
                Log::error('SatuSehat OAuth failed', ['status' => $response->status(), 'body' => $body]);

                return ['success' => false, 'error' => 'Gagal autentikasi SatuSehat: '.$errorMsg];
            }

            $data = $response->json();
            $accessToken = $data['access_token'] ?? null;
            $expiresIn = $data['expires_in'] ?? 3600;

            if (! $accessToken) {
                return ['success' => false, 'error' => 'Response SatuSehat tidak mengandung access_token.'];
            }

            // Cache the token with a 5-minute safety margin
            SatusehatToken::create([
                'access_token' => $accessToken,
                'expires_at' => now()->addSeconds($expiresIn - 300),
            ]);

            return ['success' => true, 'token' => $accessToken];
        } catch (ConnectionException $e) {
            Log::error('SatuSehat connection error', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Tidak dapat terhubung ke server SatuSehat. Periksa koneksi internet.'];
        }
    }

    /**
     * Lookup a patient by NIK via FHIR Patient resource.
     *
     * @return array{success: bool, found: bool, ihs_number?: string, name?: string, error?: string}
     */
    public function lookupPatientByNik(string $nik): array
    {
        $tokenResult = $this->getAccessToken();

        if (! $tokenResult['success']) {
            return ['success' => false, 'found' => false, 'error' => $tokenResult['error']];
        }

        try {
            $response = Http::withToken($tokenResult['token'])
                ->timeout(15)
                ->get($this->fhirBaseUrl.'/Patient', [
                    'identifier' => self::NIK_SYSTEM.'|'.$nik,
                ]);

            if (! $response->successful()) {
                Log::error('SatuSehat Patient lookup failed', [
                    'status' => $response->status(),
                    'nik' => $nik,
                    'body' => $response->json(),
                ]);

                return [
                    'success' => false,
                    'found' => false,
                    'error' => 'Gagal mencari data pasien di SatuSehat (HTTP '.$response->status().').',
                ];
            }

            $bundle = $response->json();
            $total = $bundle['total'] ?? 0;

            if ($total === 0 || empty($bundle['entry'])) {
                return ['success' => true, 'found' => false];
            }

            $resource = $bundle['entry'][0]['resource'] ?? [];
            $ihsNumber = $resource['id'] ?? null;

            // Extract patient name from FHIR resource
            $name = null;
            if (! empty($resource['name'])) {
                $nameObj = $resource['name'][0] ?? [];
                $name = $nameObj['text'] ?? trim(($nameObj['given'][0] ?? '').' '.($nameObj['family'] ?? ''));
            }

            return [
                'success' => true,
                'found' => true,
                'ihs_number' => $ihsNumber,
                'name' => $name,
            ];
        } catch (ConnectionException $e) {
            Log::error('SatuSehat Patient lookup connection error', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'found' => false,
                'error' => 'Tidak dapat terhubung ke server SatuSehat.',
            ];
        }
    }

    /**
     * Lookup a practitioner by NIK via FHIR Practitioner resource.
     *
     * @return array{success: bool, found: bool, ihs_number?: string, name?: string, error?: string}
     */
    public function lookupPractitionerByNik(string $nik): array
    {
        $tokenResult = $this->getAccessToken();

        if (! $tokenResult['success']) {
            return ['success' => false, 'found' => false, 'error' => $tokenResult['error']];
        }

        try {
            $response = Http::withToken($tokenResult['token'])
                ->timeout(15)
                ->get($this->fhirBaseUrl.'/Practitioner', [
                    'identifier' => self::NIK_SYSTEM.'|'.$nik,
                ]);

            if (! $response->successful()) {
                Log::error('SatuSehat Practitioner lookup failed', [
                    'status' => $response->status(),
                    'nik' => $nik,
                    'body' => $response->json(),
                ]);

                return [
                    'success' => false,
                    'found' => false,
                    'error' => 'Gagal mencari data praktisi di SatuSehat (HTTP '.$response->status().').',
                ];
            }

            $bundle = $response->json();
            $total = $bundle['total'] ?? 0;

            if ($total === 0 || empty($bundle['entry'])) {
                return ['success' => true, 'found' => false];
            }

            $resource = $bundle['entry'][0]['resource'] ?? [];
            $ihsNumber = $resource['id'] ?? null;

            // Extract practitioner name
            $name = null;
            if (! empty($resource['name'])) {
                $nameObj = $resource['name'][0] ?? [];
                $name = $nameObj['text'] ?? trim(($nameObj['given'][0] ?? '').' '.($nameObj['family'] ?? ''));
            }

            return [
                'success' => true,
                'found' => true,
                'ihs_number' => $ihsNumber,
                'name' => $name,
            ];
        } catch (ConnectionException $e) {
            Log::error('SatuSehat Practitioner lookup connection error', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'found' => false,
                'error' => 'Tidak dapat terhubung ke server SatuSehat.',
            ];
        }
    }

    /**
     * Get all locations belonging to the configured organization.
     *
     * @return array{success: bool, locations?: array<int, array{id: string, name: string}>, error?: string}
     */
    public function getLocationsByOrganization(): array
    {
        $tokenResult = $this->getAccessToken();

        if (! $tokenResult['success']) {
            return ['success' => false, 'error' => $tokenResult['error']];
        }

        try {
            $response = Http::withToken($tokenResult['token'])
                ->timeout(15)
                ->get($this->fhirBaseUrl.'/Location', [
                    'organization' => $this->config->organization_id,
                ]);

            if (! $response->successful()) {
                Log::error('SatuSehat Location lookup failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Gagal mengambil data lokasi dari SatuSehat (HTTP '.$response->status().').',
                ];
            }

            $bundle = $response->json();
            $locations = [];

            foreach ($bundle['entry'] ?? [] as $entry) {
                $resource = $entry['resource'] ?? [];
                $locations[] = [
                    'id' => $resource['id'] ?? '',
                    'name' => $resource['name'] ?? 'Unnamed Location',
                ];
            }

            return ['success' => true, 'locations' => $locations];
        } catch (ConnectionException $e) {
            Log::error('SatuSehat Location lookup connection error', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Tidak dapat terhubung ke server SatuSehat.',
            ];
        }
    }

    /**
     * Get the active Organization ID from config.
     */
    public function getOrganizationId(): string
    {
        return $this->config->organization_id;
    }

    /**
     * Get the active environment (sandbox/production).
     */
    public function getEnvironment(): string
    {
        return $this->config->environment;
    }
}
