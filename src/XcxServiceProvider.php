<?php

namespace JLWx\Xcx;

use Illuminate\Support\ServiceProvider;

class XcxServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__ . '/config/xcx.php' => config_path('xcx.php'),
        ]);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('xcx', function ($app){
            return new Xcx();
        });
    }

    public function provides()
    {
        return [
            'xcx',
        ];
    }
}
