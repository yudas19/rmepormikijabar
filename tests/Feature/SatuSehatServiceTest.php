<?php

use App\Models\MasterSatusehatConfig;
use App\Models\SatusehatToken;
use App\Services\SatuSehatService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Create an active SatuSehat config for sandbox
    MasterSatusehatConfig::create([
        'environment' => 'sandbox',
        'client_id' => 'test-client-id',
        'client_secret' => 'test-client-secret',
        'organization_id' => 'test-org-id-123',
        'is_active' => true,
    ]);
});

test('getAccessToken returns token on successful auth', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-access-token-12345',
            'expires_in' => 3600,
            'token_type' => 'BearerToken',
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->getAccessToken();

    expect($result['success'])->toBeTrue();
    expect($result['token'])->toBe('fake-access-token-12345');

    // Token should be cached in database
    expect(SatusehatToken::count())->toBe(1);
    expect(SatusehatToken::first()->access_token)->toBe('fake-access-token-12345');
});

test('getAccessToken returns error on invalid credentials', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'error' => 'invalid_client',
            'error_description' => 'Client credentials are invalid',
        ], 401),
    ]);

    $service = new SatuSehatService;
    $result = $service->getAccessToken();

    expect($result['success'])->toBeFalse();
    expect($result['error'])->toContain('Client credentials are invalid');
});

test('getAccessToken reuses cached non-expired token', function () {
    // Pre-populate a valid cached token
    SatusehatToken::create([
        'access_token' => 'cached-token-abc',
        'expires_at' => now()->addMinutes(30),
    ]);

    // No HTTP call should be made
    Http::fake();

    $service = new SatuSehatService;
    $result = $service->getAccessToken();

    expect($result['success'])->toBeTrue();
    expect($result['token'])->toBe('cached-token-abc');

    Http::assertNothingSent();
});

test('lookupPatientByNik returns patient data when found', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Patient*' => Http::response([
            'resourceType' => 'Bundle',
            'total' => 1,
            'entry' => [
                [
                    'resource' => [
                        'resourceType' => 'Patient',
                        'id' => 'P02478375538',
                        'name' => [
                            [
                                'text' => 'Budi Santoso',
                                'use' => 'official',
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->lookupPatientByNik('3273101621900001');

    expect($result['success'])->toBeTrue();
    expect($result['found'])->toBeTrue();
    expect($result['ihs_number'])->toBe('P02478375538');
    expect($result['name'])->toBe('Budi Santoso');
});

test('lookupPatientByNik returns not found when NIK not registered', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Patient*' => Http::response([
            'resourceType' => 'Bundle',
            'total' => 0,
            'entry' => [],
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->lookupPatientByNik('9999999999999999');

    expect($result['success'])->toBeTrue();
    expect($result['found'])->toBeFalse();
});

test('lookupPractitionerByNik returns practitioner data when found', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Practitioner*' => Http::response([
            'resourceType' => 'Bundle',
            'total' => 1,
            'entry' => [
                [
                    'resource' => [
                        'resourceType' => 'Practitioner',
                        'id' => 'N10000001',
                        'name' => [
                            [
                                'text' => 'dr. Andi Wijaya',
                                'use' => 'official',
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->lookupPractitionerByNik('3273101621900002');

    expect($result['success'])->toBeTrue();
    expect($result['found'])->toBeTrue();
    expect($result['ihs_number'])->toBe('N10000001');
    expect($result['name'])->toBe('dr. Andi Wijaya');
});

test('lookupPractitionerByNik returns not found when NIK not registered', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Practitioner*' => Http::response([
            'resourceType' => 'Bundle',
            'total' => 0,
            'entry' => [],
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->lookupPractitionerByNik('9999999999999999');

    expect($result['success'])->toBeTrue();
    expect($result['found'])->toBeFalse();
});

test('getLocationsByOrganization returns list of locations', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Location*' => Http::response([
            'resourceType' => 'Bundle',
            'total' => 2,
            'entry' => [
                [
                    'resource' => [
                        'resourceType' => 'Location',
                        'id' => 'loc-uuid-001',
                        'name' => 'Poli Umum',
                    ],
                ],
                [
                    'resource' => [
                        'resourceType' => 'Location',
                        'id' => 'loc-uuid-002',
                        'name' => 'Poli Gigi',
                    ],
                ],
            ],
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->getLocationsByOrganization();

    expect($result['success'])->toBeTrue();
    expect($result['locations'])->toHaveCount(2);
    expect($result['locations'][0]['id'])->toBe('loc-uuid-001');
    expect($result['locations'][0]['name'])->toBe('Poli Umum');
    expect($result['locations'][1]['id'])->toBe('loc-uuid-002');
    expect($result['locations'][1]['name'])->toBe('Poli Gigi');
});

test('service throws exception when no active config exists', function () {
    // Deactivate all configs
    MasterSatusehatConfig::query()->update(['is_active' => false]);

    expect(fn () => new SatuSehatService)
        ->toThrow(RuntimeException::class, 'Konfigurasi SatuSehat belum diatur');
});

test('service uses production base url for production environment', function () {
    // Switch config to production
    MasterSatusehatConfig::query()->update(['environment' => 'production']);

    Http::fake([
        'api-satusehat.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'prod-token',
            'expires_in' => 3600,
        ]),
    ]);

    $service = new SatuSehatService;
    $result = $service->getAccessToken();

    expect($result['success'])->toBeTrue();
    expect($result['token'])->toBe('prod-token');

    // Verify request went to production URL
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api-satusehat.kemkes.go.id');
    });
});

test('lookupPatientByNik handles HTTP error gracefully', function () {
    Http::fake([
        'api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/accesstoken*' => Http::response([
            'access_token' => 'fake-token',
            'expires_in' => 3600,
        ]),
        'api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1/Patient*' => Http::response([
            'resourceType' => 'OperationOutcome',
            'issue' => [['severity' => 'error', 'code' => 'invalid']],
        ], 500),
    ]);

    $service = new SatuSehatService;
    $result = $service->lookupPatientByNik('1234567890123456');

    expect($result['success'])->toBeFalse();
    expect($result['found'])->toBeFalse();
    expect($result['error'])->toContain('HTTP 500');
});
