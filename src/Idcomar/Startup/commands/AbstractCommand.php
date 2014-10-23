<?php 
namespace Idcomar\Startup\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Modules console commands
* @author Boris Strahija <bstrahija@gmail.com>
*/
abstract class AbstractCommand extends Command {

	/**
	 * List of all available modules
	 *
	 * @var array
	 */
	protected $modules;

	/**
	 * IoC
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * DI
	 *
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$this->app = $app;
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}


	protected function createPublicStructure($assetsPath){
		$packageAssetsPath = $this->getPackagePath();
		$idcomarAssetsPath = $assetsPath. '/idcomar';
		$bootstrapAssetsPath = $assetsPath . '/bootstrap';		

		if ( ! $this->app['files']->exists($assetsPath))
		{
			$this->app['files']->makeDirectory($assetsPath, 0755);
		}

		if ( ! $this->app['files']->exists($idcomarAssetsPath))
		{
			$this->app['files']->makeDirectory($idcomarAssetsPath, 0755);
			$this->info('## Publishing idcomar assets');
			$this->app['files']->copyDirectory($packageAssetsPath.'/idcomar/',$idcomarAssetsPath);
			$this->info('## Published!');			
		}
		if ( ! $this->app['files']->exists($bootstrapAssetsPath))
		{
			$this->app['files']->makeDirectory($bootstrapAssetsPath, 0755);
			$this->info('## Publishing bootstrap assets');
			$this->app['files']->copyDirectory($packageAssetsPath.'/bootstrap/',$bootstrapAssetsPath);
			$this->info('## Published!');			
		}		
		$this->call('config:publish', array('package' => 'zofe/rapyd' ) );
		$this->call('asset:publish', array('package' => 'zofe/rapyd' ) );	
	}

	protected function copyViews($viewsPath){
		$packageViewsPath = $this->getPackagePath('views');

		$adminViewsPath = $viewsPath.'/admin';
		$frontViewsPath	= $viewsPath.'/front';

		if ( ! $this->app['files']->exists($adminViewsPath))
		{
			$this->app['files']->makeDirectory($adminViewsPath, 0755);
			$this->info('## Copying admin template');
			$this->app['files']->copyDirectory($packageViewsPath.'/admin/',$adminViewsPath);
			$this->info('## Copied!');			
		}
		if ( ! $this->app['files']->exists($frontViewsPath))
		{
			$this->app['files']->makeDirectory($frontViewsPath, 0755);
			$this->info('## Copying front template');
			$this->app['files']->copyDirectory($packageViewsPath.'/front/',$frontViewsPath);
			$this->info('## Copied!');			
		}

	}

	protected function copyControllers($controllersPath){
		$packageControllersPath = $this->getPackagePath('controllers');

		$controllersFiles = $this->app['files']->files($packageControllersPath);
		foreach ($controllersFiles as $file) {
				
			
			if ( ! $this->app['files']->exists($controllersPath.basename($file))){
				$this->app['files']->copy($file,$controllersPath.basename($file));
			}
		}		

	}	

	protected function installSyntara(){
		$this->call('syntara:install');
		$this->call('create:user', array('email' => 'admin@admin.com','username'=>'admin','password'=>'admin123','group'=>'Admin' ));

	}

	protected function copyConfigFiles($configPath){
		$this->info('## Changing Config file options');
		$packageConfigPath = $this->getPackagePath('config');
		$replaceConfig = $this->app['config']->get('startup::replace_config');
		foreach ($replaceConfig as $path => $options) {
			$this->info('## Config file: '.$configPath.$path);
			$fileContent = file_get_contents($configPath.$path);
			foreach ($options as $key => $option) {
				$this->info('## Option: '.$option['search'].'=>'.$option['replace']);
				$fileContent = str_replace($option['search'], $option['replace'], $fileContent);	
			}
			file_put_contents($configPath.$path, $fileContent);
		}
		$this->info('## Changed!');		


		if ( ! $this->app['files']->exists($configPath.'packages/idcomar/startup'))
		{
			//$this->app['files']->makeDirectory($configPath.'packages/idcomar', 0755);
			$this->app['files']->makeDirectory($configPath.'packages/idcomar/startup', 0755,true);

			$this->info('## Copying config files');
			$this->app['files']->copyDirectory($packageConfigPath.'/pkg',$configPath.'packages/idcomar/startup');
			$this->info('## Copied!');			
		}

	}

	protected function copyHelpers($helpersPath){
		$packageHelpersPath = $this->getPackagePath('helpers');
		if ( ! $this->app['files']->exists($helpersPath))
		{
			//$this->app['files']->makeDirectory($configPath.'packages/idcomar', 0755);
			$this->app['files']->makeDirectory($helpersPath, 0755);
		}
		$startPath = app_path().'/start/';
		$helpersFiles = $this->app['files']->files($packageHelpersPath);
		foreach ($helpersFiles as $file) {
				
			
			if ( ! $this->app['files']->exists($helpersPath.basename($file))){
				$this->app['files']->copy($file,$helpersPath.basename($file));
				$this->app['files']->append($startPath.'global.php',"\n\rrequire app_path().'/helpers/".basename($file)."';");
			}
		}

	}

	protected function copyLangs($langsPath){
		$packageLangsPath = $this->getPackagePath('lang');
		if ( ! $this->app['files']->exists($langsPath.'es'))
		{
			//$this->app['files']->makeDirectory($configPath.'packages/idcomar', 0755);
			$this->app['files']->makeDirectory($langsPath.'es', 0755);
		}
		if ( ! $this->app['files']->exists($langsPath.'en'))
		{
			//$this->app['files']->makeDirectory($configPath.'packages/idcomar', 0755);
			$this->app['files']->makeDirectory($langsPath.'en', 0755);
		}
		$langsFiles = $this->app['files']->files($packageLangsPath.'es');
		foreach ($langsFiles as $file) {
			
			if ( ! $this->app['files']->exists($langsPath.'es/'.basename($file))){
				$this->app['files']->copy($file,$langsPath.'es/'.basename($file));
			}
		}		
		$langsFiles = $this->app['files']->files($packageLangsPath.'en');
		foreach ($langsFiles as $file) {
			
			if ( ! $this->app['files']->exists($langsPath.'en/'.basename($file))){
				$this->app['files']->copy($file,$langsPath.'en/'.basename($file));
			}
		}		
	}

	/**
	 * Dump autoload classes
	 *
	 * @return void
	 */
	public function dumpAutoload()
	{
		// Also run composer dump-autoload
		$composer = new Composer($this->app['files']);
		$this->info('Generating optimized class loader');
		$composer->dumpOptimized();
		$this->line('');
	}

    protected function getPackagePath($dir="public")
    {
    	switch ($dir) {
    		case 'public':
    			return __DIR__.'/../../../../public/';
    		case 'views':
    			return __DIR__.'/../../../views/';    			
    		case 'config':
    			return __DIR__.'/../../../config/';      			
    		case 'helpers':
    			return __DIR__.'/../../../helpers/';   
    		case 'lang':
    			return __DIR__.'/../../../lang/';   
    		case 'controllers':
    			return __DIR__.'/../../../controllers/';       			
    		default:
				return __DIR__.'/../../../../public/';
    	}
        
    }	
}
