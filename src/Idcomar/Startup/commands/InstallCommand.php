<?php 
namespace Idcomar\Startup\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class InstallCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'startup:install';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Kick off for a new project';

	public function fire()
	{
		// Name of new module
		
		$this->info('## Identidad Digital / Startup Install ##');
		

		$assetsPath = app()->make('path.public').'/assets';
		$viewsPath = app_path().'/views';
		$configPath = app_path().'/config/';
		$helpersPath = app_path().'/helpers/';
		$startPath = app_path().'/start/';
		$langsPath = app_path().'/lang/';
		$controllersPath = app_path().'/controllers/';


		// Get path to modules
		$modulePath = $this->app['config']->get('startup::path');



		// Create the directory
		if ( ! $this->app['files']->exists($modulePath))
		{
			$this->app['files']->makeDirectory($modulePath, 0755);
		}		


		/*Create public structure*/
		$this->createPublicStructure($assetsPath);
		
		/*Copy views*/
		$this->copyViews($viewsPath);

		/*Copy controllers*/
		$this->copyControllers($controllersPath);

		/*Install Syntara*/
		$this->installSyntara();
		
		/*config files*/
		$this->copyConfigFiles($configPath);

		/*HELPERS*/
		
		$this->copyHelpers($helpersPath);

		/*Traducciones*/
		$this->copyLangs($langsPath);


		//$this->info('2) Publish package assets');
		// Autoload classes
		$this->dumpAutoload();

	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	
}
