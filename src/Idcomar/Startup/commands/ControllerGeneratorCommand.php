<?php namespace Idcomar\Startup\Commands;

use Idcomar\Startup\Generators\ControllerGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerGeneratorCommand extends BaseGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'startup:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new controller.';

    /**
     * Model generator instance.
     *
     * @var Way\Generators\Generators\ControllerGenerator
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ControllerGenerator $generator)
    {
        parent::__construct();

        $this->generator = $generator;

        
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
        $module = $this->option('module');
        if (is_null($module))
        {
            return $this->option('path') . '/' . ucwords($this->argument('name')) . '.php';     
        }
        else
        {
            $this->generator->getCache()->moduleName($module);
            $aux = '{Modulename}/Controllers/';
            $aux = str_replace('{Modulename}',ucwords($module),$aux);            
            return $this->getModuleTargetPath() . $aux . ucwords($this->argument('name')) . '.php';     
        }
       
    }

    protected function getTemplate()
    {
       
        //if (is_null($this->option('module')))
        //{
            return $this->option('template') ;     
        //}
        //else
        //{
        //    return $this->getModuleControllerTemplatePath();     
        //}
       
    }    

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Name of the controller to generate.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
           array('path', null, InputOption::VALUE_OPTIONAL, 'Path to controllers directory.', app_path() . '/controllers'),
           array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.', __DIR__.'/../Generators/templates/controller.txt'),
           array('module', null, InputOption::VALUE_OPTIONAL, 'Module name.'),

        );
    }

}
