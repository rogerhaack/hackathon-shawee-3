<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message, $data = "") {
            return Response::json([
                "erros" => false,
                "message" => $message,
                "data" => $data
            ]);
        });

        Response::macro('error', function ($message, $status = 400) {
            return Response::json([
                "erros" => true,
                "message" => $message
            ], $status);
        });
    }
}
