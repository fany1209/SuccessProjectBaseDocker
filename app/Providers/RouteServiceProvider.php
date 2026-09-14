<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // 1. Portal de Clientes: Autenticación (Previene fuerza bruta y credential stuffing)
        RateLimiter::for('portal-login', function (Request $request) {
            $email = (string) $request->input('email', '');
            $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Demasiados intentos de inicio de sesión. Por favor, espere {$retryAfter} segundos.",
                    ], 429, $headers);
                }

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => "Demasiados intentos fallidos. Por seguridad, espere {$retryAfter} segundos antes de volver a intentar.",
                    ])
                    ->withHeaders($headers)
                    ->setStatusCode(429);
            });
        });

        // 2. Portal de Clientes: Restablecimiento de contraseña
        RateLimiter::for('portal-reset-password', function (Request $request) {
            $email = (string) $request->input('email', '');
            $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Demasiadas solicitudes de recuperación. Por favor, espere {$retryAfter} segundos.",
                    ], 429, $headers);
                }

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => "Demasiadas solicitudes de restablecimiento. Por favor, espere {$retryAfter} segundos.",
                    ])
                    ->withHeaders($headers)
                    ->setStatusCode(429);
            });
        });

        // 3. Formulario público de contacto (Previene spam bot y saturación de correo/BD)
        RateLimiter::for('contact-form', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Ha enviado demasiados mensajes. Por favor, espere {$retryAfter} segundos.",
                    ], 429, $headers);
                }

                return back()
                    ->withInput()
                    ->withErrors([
                        'message' => "Ha alcanzado el límite de envíos de mensajes. Por favor, espere {$retryAfter} segundos.",
                    ])
                    ->withHeaders($headers)
                    ->setStatusCode(429);
            });
        });

        // 4. Reportes pesados y PDFs (Protección contra agotamiento de CPU/RAM por DomPDF y Excel)
        RateLimiter::for('pdf-reports', function (Request $request) {
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(30)->by($identifier)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Límite de generación de reportes alcanzado. Por favor, espere {$retryAfter} segundos.",
                    ], 429, $headers);
                }

                return response()->view('errors.429', [
                    'message' => "Ha alcanzado el límite temporal de generación de documentos. Por favor, espere {$retryAfter} segundos antes de solicitar otro reporte.",
                    'retryAfter' => $retryAfter,
                ], 429, $headers);
            });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('admin')
                    ->name('admin.') 
                ->group(base_path('routes/admin.php'));
        });
    }
}
