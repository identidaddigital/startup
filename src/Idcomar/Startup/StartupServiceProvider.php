<?php namespace Idcomar\Startup;

use Illuminate\Support\ServiceProvider;
use Idcomar\Startup\Generators;
use Idcomar\Startup\Cache;
class StartupServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('idcomar/startup','startup',__DIR__);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app['config']->package('idcomar/startup', __DIR__.'/../../config');
		
        $this->app['startup:install'] = $this->app->share(function($app)
        {
            return new Commands\InstallCommand($app);
        });	

        
        $this->registerModuleGenerator();
        $this->registerModelGenerator();
        $this->registerControllerGenerator();
		$this->registerViewGenerator();
		$this->registerMigrationGenerator();

        $this->commands(
        	'startup.model',
        	'startup.controller',
        	'startup.view',
        	'startup.migration',
        	'startup.module',
        	'startup:install'
    	);
	}

	protected function registerModelGenerator()
	{
		$this->app['startup.model'] = $this->app->share(function($app)
		{
			$cache = new Cache($app['files']);
			$generator = new Generators\ModelGenerator($app['files'], $cache);

			return new Commands\ModelGeneratorCommand($generator);
		});
	}

	protected function registerControllerGenerator()
	{
		$this->app['startup.controller'] = $this->app->share(function($app)
		{
			$cache = new Cache($app['files']);
			$generator = new Generators\ControllerGenerator($app['files'], $cache);

			return new Commands\ControllerGeneratorCommand($generator);
		});
	}	

	protected function registerModuleGenerator()
	{
		$this->app['startup.module'] = $this->app->share(function($app)
		{
			$cache = new Cache($app['files']);
			$generator = new Generators\ModuleGenerator($app['files'], $cache);

			return new Commands\ModuleGeneratorCommand($generator,$cache);
		});
	}	

	protected function registerViewGenerator()
	{
		$this->app['startup.view'] = $this->app->share(function($app)
		{
			$cache = new Cache($app['files']);
			$generator = new Generators\ViewGenerator($app['files'], $cache);

			return new Commands\ViewGeneratorCommand($generator);
		});
	}

	protected function registerMigrationGenerator()
	{
		$this->app['startup.migration'] = $this->app->share(function($app)
		{
			$cache = new Cache($app['files']);
			$generator = new Generators\MigrationGenerator($app['files'], $cache);

			return new Commands\MigrationGeneratorCommand($generator);
		});
	}	

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
