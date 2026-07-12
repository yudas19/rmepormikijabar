<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Models\MedicalRecord;
use App\Models\Poli;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        file_put_contents(storage_path('logs/boot.log'), 'booted'.PHP_EOL, FILE_APPEND);
        $this->configureDefaults();

        // Implicitly grant "admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // View Composer for Sidebar Patient Waiting Count Badges
        view()->composer('*', function ($view) {
            if (! str_ends_with($view->getName(), 'app.sidebar')) {
                return;
            }

            $waitingCounts = [
                'umum' => 0,
                'gigi' => 0,
                'kia' => 0,
            ];

            try {
                $rawCounts = MedicalRecord::where('status', 'waiting')
                    ->whereDate('tanggal_kunjungan', date('Y-m-d'))
                    ->groupBy('poli_id')
                    ->selectRaw('poli_id, count(*) as count')
                    ->pluck('count', 'poli_id')
                    ->toArray();

                $polis = Poli::all();

                foreach ($polis as $poli) {
                    $nama = strtolower($poli->nama_poli ?? '');
                    $count = $rawCounts[$poli->id] ?? 0;

                    if (strpos($nama, 'gigi') !== false) {
                        $waitingCounts['gigi'] += $count;
                    } elseif (strpos($nama, 'kia') !== false || strpos($nama, 'anak') !== false || strpos($nama, 'ibu') !== false) {
                        $waitingCounts['kia'] += $count;
                    } else {
                        $waitingCounts['umum'] += $count;
                    }
                }
            } catch (\Exception $e) {
                if (app()->environment('testing')) {
                    throw $e;
                }
            }

            $view->with('waitingCounts', $waitingCounts);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
