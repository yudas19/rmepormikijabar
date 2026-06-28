<?php

namespace App\Services;

use App\Models\MasterPcare;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BpjsBridgeService
{
    protected ?string $bpjsEnv = null;

    protected ?string $consId = null;

    protected ?string $secretKey = null;

    protected ?string $userKey = null;

    protected ?string $pcareUsername = null;

    protected ?string $pcarePassword = null;

    protected ?string $userMjkn = null;

    protected ?string $baseUrl = null;

    /**
     * Create a new BPJS Bridge Service instance.
     *
     * @param  int|null  $clinicId  Option to load specific clinic connection profile.
     */
    public function __construct(?int $clinicId = null)
    {
        $this->loadCredentials($clinicId);
    }

    /**
     * Load connection profile credentials from database or fallback config.
     */
    protected function loadCredentials(?int $clinicId = null): void
    {
        $profile = null;

        if ($clinicId) {
            $profile = MasterPcare::find($clinicId);
        } else {
            // Dynamically detect active tenant profile via faskes_profiles kemenkes code
            if (Schema::hasTable('faskes_profiles')) {
                $faskesProfile = DB::table('faskes_profiles')->first();
                if ($faskesProfile && ! empty($faskesProfile->kode_faskes_kemenkes)) {
                    $profile = MasterPcare::where('kode_faskes', $faskesProfile->kode_faskes_kemenkes)
                        ->orWhere('kode_pcare', $faskesProfile->kode_faskes_kemenkes)
                        ->first();
                }
            }

            // Secondary fallback: retrieve first active PCare profile
            if (! $profile) {
                $profile = MasterPcare::where('is_active', true)->first();
            }
        }

        // Apply dynamic credentials if present in database
        if ($profile && ! empty($profile->bpjs_cons_id) && ! empty($profile->bpjs_secret_key)) {
            $this->bpjsEnv = $profile->bpjs_env ?? config('bpjs.env');
            $this->consId = $this->decryptValue($profile->bpjs_cons_id);
            $this->secretKey = $this->decryptValue($profile->bpjs_secret_key);
            $this->userKey = $this->decryptValue($profile->bpjs_user_key) ?? config('bpjs.user_key');
            $this->pcareUsername = $profile->pcare_username ?? config('bpjs.pcare_username');
            $this->pcarePassword = $this->decryptValue($profile->pcare_password) ?? config('bpjs.pcare_password');
            $this->userMjkn = $profile->user_mjkn ?? config('bpjs.user_mjkn');
            $this->baseUrl = config('bpjs.base_url');
        } else {
            // Apply config fallback
            $this->bpjsEnv = config('bpjs.env');
            $this->consId = config('bpjs.cons_id');
            $this->secretKey = config('bpjs.secret_key');
            $this->userKey = config('bpjs.user_key');
            $this->pcareUsername = config('bpjs.pcare_username');
            $this->pcarePassword = config('bpjs.pcare_password');
            $this->userMjkn = config('bpjs.user_mjkn');
            $this->baseUrl = config('bpjs.base_url');
        }
    }

    /**
     * Helper to decrypt sensitive credentials.
     */
    protected function decryptValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }

    /**
     * Generate BPJS dynamic mathematical headers for P-Care, VClaim, or Antrol request calls.
     *
     * @param  string  $kdAplikasi  Application code suffix. Defaults to 'RME'.
     * @return array<string, string|int>
     */
    public function getSecurityHeaders(string $kdAplikasi = 'RME'): array
    {
        $timestamp = time();
        $signature = base64_encode(hash_hmac('sha256', $this->consId.'&'.$timestamp, $this->secretKey, true));
        $authorization = base64_encode($this->pcareUsername.':'.$this->pcarePassword.':'.$kdAplikasi);

        return [
            'X-cons-id' => $this->consId ?? '',
            'X-timestamp' => $timestamp,
            'X-signature' => $signature,
            'X-authorization' => 'Basic '.$authorization,
            'user-key' => $this->userKey ?? '',
        ];
    }

    /**
     * Get active Consumer ID.
     */
    public function getConsId(): ?string
    {
        return $this->consId;
    }

    /**
     * Get active Secret Key.
     */
    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    /**
     * Get active User Key.
     */
    public function getUserKey(): ?string
    {
        return $this->userKey;
    }

    /**
     * Get active PCare Username.
     */
    public function getPcareUsername(): ?string
    {
        return $this->pcareUsername;
    }

    /**
     * Get active PCare Password.
     */
    public function getPcarePassword(): ?string
    {
        return $this->pcarePassword;
    }

    /**
     * Get active MJKN/Antrol Username.
     */
    public function getUserMjkn(): ?string
    {
        return $this->userMjkn;
    }

    /**
     * Get active Base Endpoint URL.
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * Get active BPJS Environment.
     */
    public function getBpjsEnv(): ?string
    {
        return $this->bpjsEnv;
    }
}
