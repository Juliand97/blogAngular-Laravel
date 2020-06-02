<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JWTprovider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        #llamamos el archivo de la carpeta helpers
        require_once app_path()."/Helpers/JWTAuth.php";
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
