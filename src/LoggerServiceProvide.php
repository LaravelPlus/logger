<?php


namespace LaravelX\Logger;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->singleton('app-log', function ($app) {
            return Logger::newAppLogger();
        });
        if (config('log.handler') == 'graylog') {
            $this->setGrayLog();
        }
    }

    public function setGrayLog()
    {
        app()->configureMonologUsing(function ($monolog) {
            $monolog->pushHandler(Logger::newGelfHandler());
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}