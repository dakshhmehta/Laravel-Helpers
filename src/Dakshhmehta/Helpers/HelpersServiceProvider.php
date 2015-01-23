<?php namespace Dakshhmehta\Helpers;

use Illuminate\Support\ServiceProvider;
use Config;

class HelpersServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('dakshhmehta/helpers');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app['dax-template'] = $this->app->share(function($app){
			return new Template(Config::get('helpers::handler'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('dax-template');
	}

}
