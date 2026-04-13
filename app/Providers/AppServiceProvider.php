<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Respuesta exitosa
        Response::macro('success', function (string $message = '', $data = null, int $status = 200, array $extra = []) {

            $response = [
                'success' => true,
                'message' => $message,
                'data' => $data,
            ];

            if (!empty($extra)) {
                $response = array_merge($response, $extra);
            }

            return response()->json($response, $status);
        });

        // Respuesta de error
        Response::macro('error', function (string $message = '', int $status = 400, $error = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $error,
            ], $status);
        });
    }
}
