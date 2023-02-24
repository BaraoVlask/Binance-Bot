<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ('fpm-fcgi' === PHP_SAPI) {
            VarDumper::setHandler(function ($var) {
                $cloner = new VarCloner();
                $dumper = new HtmlDumper();
                $dumper->setDisplayOptions(['maxDepth' => 0]);
                $dumper->dump($cloner->cloneVar($var));
            });
        }
    }
}
