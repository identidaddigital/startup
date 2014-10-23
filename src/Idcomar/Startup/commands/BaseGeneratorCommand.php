<?php namespace Idcomar\Startup\Commands;

use Idcomar\Startup\Generators\ViewGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BaseGeneratorCommand extends Command {
        
    protected $module_name;
    protected $module_name_pluralized;
    protected $add;
    protected $add_pluralized;
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $path = $this->getPath();
        $template = $this->getTemplate();
        $this->info($template);
        $this->printResult($this->generator->make($path, $template), $path);
    }

    /**
     * Provide user feedback, based on success or not.
     *
     * @param  boolean $successful
     * @param  string $path
     * @return void
     */
    protected function printResult($successful, $path)
    {
        if ($successful)
        {
            return $this->info("Created {$path}");
        }

        $this->error("Could not create {$path}");
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
       return $this->option('path') . '/' . strtolower($this->argument('name')) . '.blade.php';
    }

    /**
     * Get the path to the template for a module.
     *
     * @return string
     */
    protected function getModuleTargetPath()
    {
        return \Config::get('startup::path'). '/';
    }

    /**
     * Get the path to the template for a module.
     *
     * @return string
     */
    protected function getModuleTemplatePath()
    {
        return __DIR__.'/../Generators/templates/modules/structure/';
    }
    /**
     * Get the path to the template for a model.
     *
     * @return string
     */
    protected function getModuleModelTemplatePath()
    {
        return __DIR__.'/../Generators/templates/modules/model.txt';
    }

    /**
     * Get the path to the template for a controller.
     *
     * @return string
     */
    protected function getModuleControllerTemplatePath($admin=FALSE)
    {
        return __DIR__.'/../Generators/templates/modules/'.($admin === TRUE ? "admin_" : "").'controller.txt';
    }


    /**
     * Get the path to the template for a controller.
     *
     * @return string
     */
    protected function getModuleTestTemplatePath()
    {
        return __DIR__.'/../Generators/templates/modules/controller-test.txt';
    }

    /**
     * Get the path to the template for a view.
     *
     * @return string
     */
    protected function getModuleViewTemplatePath($view = 'view',$admin=FALSE)
    {
        return __DIR__."/../Generators/templates/modules/views/".($admin === TRUE ? "admin_" : "")."{$view}.txt";
    }
    /**
     * Get the path to the template for a controller.
     *
     * @return string
     */

    protected function getModuleMigrationTemplatePath()
    {
        return __DIR__.'/../Generators/templates/modules/migration.txt';
    }

    protected function getModuleLangTemplatePath()
    {
        return __DIR__.'/../Generators/templates/modules/lang.php';
    }    
    
    protected function setModuleNames($aux_name){
        if (strpos($aux_name, '/') !== FALSE){

            $aux_name = explode('/',$aux_name);

            $this->module_name = $aux_name[1];
            $this->module_name_pluralized = $aux_name[0];

        }else{
            $this->module_name = $aux_name;
            $this->module_name_pluralized = $aux_name;
        }        
    }
    protected function setAddNames($aux_add){
        if (strpos($aux_add, '/') !== FALSE){

            $aux_add = explode('/',$aux_add);

            $this->add = $aux_add[1];
            $this->add_pluralized = $aux_add[0];

        }else{
            $this->add = $aux_add;
            $this->add_pluralized = $aux_add;
        }      
    }    
}