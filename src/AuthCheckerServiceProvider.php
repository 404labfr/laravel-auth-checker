<?php

namespace Lab404\AuthChecker;

use Illuminate\Events\Dispatcher;
use Jenssegers\Agent\AgentServiceProvider;
use Lab404\AuthChecker\Services\AuthChecker;
use Lab404\AuthChecker\Subscribers\AuthCheckerSubscriber;

/**
 * Class AuthCheckerServiceProvider
 *
 * @package Lab404\AuthChecker
 */
class AuthCheckerServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /** @var bool */
    protected $defer = false;

    /** @var string */
    protected $name = 'auth-checker';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthChecker::class, AuthChecker::class);

        $this->app->singleton(AuthChecker::class, function ($app) {
            return new AuthChecker($app, $app['request']);
        });

        $this->app->alias(AuthChecker::class, 'authchecker');

        $this->registerDependencies();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfig()
            ->mergeLang()
            ->mergeMigrations()
            ->registerEvents();
    }

    /**
     * @param   void
     * @return  self
     */
    protected function registerDependencies()
    {
        $this->app->register(AgentServiceProvider::class);

        return $this;
    }

    /**
     * Merge migration files.
     *
     * @param   void
     * @return  self
     */
    protected function mergeMigrations()
    {
        $path = __DIR__ . '/../migrations';

        $this->publishes([
                $path => database_path('migrations'),
        ], 'migrations');

        return $this;
    }

    /**
     * Merge config file.
     *
     * @param   void
     * @return  self
     */
    protected function mergeConfig()
    {
        $configPath = __DIR__ . '/../config/' . $this->name . '.php';

        $this->mergeConfigFrom($configPath, $this->name);

        $this->publishes([$configPath => config_path($this->name . '.php')], 'auth-checker');

        return $this;
    }

    /**
     * Publish lang files.
     *
     * @param   void
     * @return  self
     */
    protected function mergeLang()
    {
        $langPath = __DIR__ . '/../lang/';

        $this->loadTranslationsFrom($langPath, $this->name);

        return $this;
    }

    /**
     * Registers library events.
     *
     * @param   void
     * @return  self
     */
    protected function registerEvents()
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app['events'];
        $dispatcher->subscribe(AuthCheckerSubscriber::class);

        return $this;
    }
}