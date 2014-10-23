<?php namespace Idcomar\Startup\Commands;

use Idcomar\Startup\Generators\ModuleGenerator;
use Idcomar\Startup\Cache;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Pluralizer;
use Idcomar\Startup\Filesystem;
use Idcomar\Startup\Filesystem\FileNotFound;
use Idcomar\Startup\Filesystem\ModuleAlreadyExists;
use Illuminate\Support\ClassLoader;

//use Idcomar\Startup\BaseGeneratorCommand;

//class MissingFieldsException extends \Exception {}

class ModuleGeneratorCommand extends BaseGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'startup:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a module.';

    /**
     * Model generator instance.
     *
     * @var Way\Generators\Generators\ResourceGenerator
     */
    protected $generator;

    /**
     * File cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $full_module_name;
    protected $model_pluralized;

    public function __construct(ModuleGenerator $generator, Cache $cache)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->cache = $cache;

    }
    


    public function fire()
    {

        $this->full_module_name = $this->argument('name');
        //$this->module_name = $this->argument('name');

        $this->setModuleNames($this->full_module_name);


        $aux_add = $this->option('add');

        $this->setAddNames($aux_add);
        
        //$this->add = $this->option('add');

        $this->filesystem = new \Idcomar\Startup\Filesystem\Filesystem;


        $remove = $this->option('remove');
        if (!is_null($remove)){
            $this->removeModule();
        }else{

        // Scaffolding should always begin with the singular
        // form of the now.
        //$this->model = Pluralizer::singular(is_null($this->add) ? $this->module_name : $this->add);
        $this->model = is_null($this->add) ? $this->module_name : $this->add;
        $this->model_pluralized = is_null($this->add) ? $this->module_name_pluralized : $this->add_pluralized;
        $this->fields = $this->option('fields');


        if (is_null($this->fields))
        {
            throw new MissingFieldsException('You must specify the fields option.');
        }

        // We're going to need access to these values
        // within future commands. I'll save them
        // to temporary files to allow for that.
        $this->cache->fields($this->fields);
        $this->cache->modelName($this->model);
        $this->cache->pluralModelName($this->model_pluralized);
        $this->cache->pluralModuleName($this->module_name_pluralized);
        //$this->cache->moduleName($module);

        $this->createModule();


        }
        
        //$this->callModel($module);
        //$this->callView($module);
        //$this->callController($module);
        //$this->callMigration($module);
        //$this->callSeeder($module);
        //$this->callMigrate();

        



        // We're all finished, so we
        // can delete the cache.
        //$this->call('modules:scan');
        //$this->cache->destroyAll();




        // All done!
        /*
        $this->info(sprintf(
            "All done! Don't forget to add `%s` to %s." . PHP_EOL,
            "Route::resource('{$this->getTableName($module)}', '{$this->getControllerName($module)}');",
            "app/routes.php"
        ));*/

    }



    protected function createModule()
    {

        $module_name = $this->module_name;
        $module_folder_path = $this->getModuleTargetPath();
        

        if(!$module_name){
            $this->info("Creating new module for your laravel application (app/NamespaceRoot/[Somefolder/]Somemodule).");
            if(!$module_name) $module_name = $this->ask("Module name (for example Somemodule): ");
        }

        $module_name_lower = strtolower($module_name);
        $module_name_capitalized = ucwords($module_name);
        $module_name_pluralized = strtolower($this->module_name_pluralized);
        $module_name_pluralized_capitalized = ucwords($this->module_name_pluralized);

        $module_folder_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $module_folder_path);
        $this->info("module_folder_path = $module_folder_path");
        $module_base_namespace = str_replace(DIRECTORY_SEPARATOR, "\\",
                                str_replace("app".DIRECTORY_SEPARATOR, "", $module_folder_path.DIRECTORY_SEPARATOR.$module_name_pluralized_capitalized)
                            );
        $this->info("module_base_namespace = $module_base_namespace");


        //$templates_path = __DIR__.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR;
        //$templates_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $this->laravel['path.base'].DIRECTORY_SEPARATOR.\Config::get("generators::config.module_template_path"));
        
        $templates_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $this->getModuleTemplatePath());
        $this->info("templates_path = $templates_path");

        $destination_path = $this->laravel['path.base'].DIRECTORY_SEPARATOR.$module_folder_path.DIRECTORY_SEPARATOR.$module_name_pluralized_capitalized.DIRECTORY_SEPARATOR;
        $this->info("destination_path = $destination_path");

        
        //$this->callLang($templates_path,$destination_path,$module_base_namespace);        
        
        
        if (!is_null($this->add))
        {
            //Only create module
            if (!$this->filesystem->exists($destination_path)){
                if (!$this->confirm("$module_name_pluralized_capitalized module is not created. Do you want me to create it? [yes|no]"))
                {
                    throw new FileNotFound;
                }
                $this->createStructure($templates_path,$destination_path,$module_base_namespace);
            }            
        }
        else
        {
            //Add resorce. If module is not created, ask the user if he wants to create
            if ($this->filesystem->exists($destination_path)){
                throw new ModuleAlreadyExists;
            }            
            $this->createStructure($templates_path,$destination_path,$module_base_namespace);

        }



        $this->callLang($templates_path,$destination_path,$module_base_namespace);
        
        $this->callController(FALSE);
        $this->callController(TRUE);
        $this->callModel();
        $this->callViews(FALSE);
        $this->callViews(TRUE);
        
        $this->callMigration();
        //$this->callSeed();
        
        
    }

    protected function createStructure($templates_path,$destination_path,$module_base_namespace){
            $module_name_lower = strtolower($this->module_name);
            $module_name_capitalized = ucwords($this->module_name);
            $module_name_pluralized = strtolower($this->module_name_pluralized); 
            $module_names_capitalized = ucwords($module_name_pluralized);            

            $this->filesystem->copyDirectory($templates_path, $destination_path);
            $this->info("directory copied.");


            $directories = array();
            foreach (Finder::create()->in($destination_path)->directories()->depth(" < 100") as $dir)
            {
                $directories[] = $dir->getPathname();
            }
            foreach($directories as $directory_name){
                $new_directory_name = str_replace("modulename", $module_name_lower, $directory_name);
                if($new_directory_name != $directory_name){
                    $this->filesystem->copyDirectory($directory_name, $new_directory_name);
                    $this->filesystem->deleteDirectory($directory_name);
                    $this->info("directory rename $directory_name -> $new_directory_name");
                }
            }

            foreach($this->filesystem->allFiles($destination_path) as $filename){

                $content = $this->filesystem->get($filename);
                $this->info("get $filename");
                $content = str_replace("{{Modulename}}", $module_name_capitalized, $content);
                $content = str_replace("{{modulename}}", $module_name_lower, $content);
                $content = str_replace("{{modulenames}}", $module_name_pluralized, $content);
                $content = str_replace("{{Modulenames}}", $module_names_capitalized, $content);
                $content = str_replace("{{namespace}}", ucwords($module_base_namespace), $content);
                $this->filesystem->put($filename, $content);
                $this->info("put new content to $filename");

                $new_filename = str_replace(".txt", ".php", $filename);
                $new_filename = str_replace("modulename", $module_name_lower, $new_filename);
                $new_filename = str_replace("modulenames", $module_name_pluralized, $new_filename);
                $new_filename = str_replace("Modulename", $module_name_capitalized, $new_filename);
                $new_filename = str_replace("Modulenames", $module_names_capitalized, $new_filename);
                rename($filename, $new_filename);
                if(true) { $this->info("rename to $new_filename"); $this->info("--"); }
            }        
    }


    protected function removeModule()
    {
        $module_names_capitalized = ucwords($this->module_name_pluralized);   

        $module_folder_path = $this->getModuleTargetPath();
        $module_folder_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $module_folder_path);
        $destination_path = $this->laravel['path.base'].DIRECTORY_SEPARATOR.$module_folder_path.DIRECTORY_SEPARATOR.$module_names_capitalized.DIRECTORY_SEPARATOR;
        
        if (!$this->filesystem->exists($destination_path)){
            $this->info($module_names_capitalized." module doesn´t exists");
        }else{

            if ($this->filesystem->exists($destination_path."migrations")){

                $files = $this->filesystem->files($destination_path."migrations");
                foreach ($files as $file) {
                    
                    $file_name = basename($file);
                    if (strripos($file_name, "create")){

                        $table_name = strtolower($this->module_name_pluralized);

                            \DB::table('permissions')->where('value', 'LIKE', ucwords($this->module_name_pluralized).'.%')->delete();

                            
                            \DB::statement('drop table IF EXISTS '.$table_name);

                        
                    }

                      
                    
                }
                $this->filesystem->deleteDirectory($destination_path);


            }


        }

        


    }

    protected function callLang($templates_path,$destination_path,$module_base_namespace){
        
        $module_name_lower = strtolower($this->module_name);
        $module_name_capitalized = ucwords($this->module_name);
        $module_name_pluralized = strtolower($this->module_name_pluralized); 
        $module_names_capitalized = ucwords($module_name_pluralized);           
        
        $module_folder_path = $this->getModuleTargetPath();
        $module_folder_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $module_folder_path);
        $destination_path_es = $this->laravel['path.base'].DIRECTORY_SEPARATOR.$module_folder_path.DIRECTORY_SEPARATOR.$module_names_capitalized.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'es'.DIRECTORY_SEPARATOR;
        $destination_path_en = $this->laravel['path.base'].DIRECTORY_SEPARATOR.$module_folder_path.DIRECTORY_SEPARATOR.$module_names_capitalized.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR;

        $this->info($destination_path_en);
        
        $langFile = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $this->getModuleLangTemplatePath());
        //$this->info("$langFile,$destination_path,$module_base_namespace");
        
//        $this->filesystem->copyDirectory($templates_path, $destination_path);
//        rename($filename, $new_filename);
//        
        
        $lang_name = strtolower($this->model);
        
        if (!$this->filesystem->exists($destination_path_en.$lang_name.'.php')){
            $this->filesystem->copy($langFile,$destination_path_en.$lang_name.'.php');
            $this->filesystem->copy($langFile,$destination_path_es.$lang_name.'.php');
            $this->info($destination_path_es.$lang_name.'.php-------------------------->');
        }
        
        $content = $this->filesystem->get($destination_path_es.$lang_name.'.php');
        //$content_en = $this->filesystem->get($destination_path_en.$this->model.'.php');
        
        $content = str_replace("{{Modulename}}", $module_name_capitalized, $content);
        $content = str_replace("{{modulename}}", $module_name_lower, $content);
        $content = str_replace("{{modulenames}}", $module_name_pluralized, $content);
        $content = str_replace("{{Modulenames}}", $module_names_capitalized, $content);
        $content = str_replace("{{Model}}", ucwords($lang_name), $content);
        $content = str_replace("{{model}}", $lang_name, $content);

        if (! $fields = $this->cache->getFields())
        {
            $content = str_replace('{{fields}}', '', $content);
        }else{
            $rules = array_map(function($field) {
                return "'$field' => '$field',".PHP_EOL."\t\t'".ucwords($field)."' => '".ucwords($field)."'";
            }, array_keys($fields));        

            $content = str_replace('\'{{fields}}\'', PHP_EOL."\t\t".implode(','.PHP_EOL."\t\t", $rules) . PHP_EOL."\t", $content);
            
        }

        
        $this->filesystem->put($destination_path_es.$lang_name.'.php', $content);
        $this->filesystem->put($destination_path_en.$lang_name.'.php', $content);
        
    }
    
    
    /**
     * Call model generator if user confirms
     *
     * @param $resource
     */
    protected function callModel()
    {
        $this->call(
            'startup:model',
            array(
                'name' => $this->model,
                //'--template' => $this->getControllerTemplatePath(),
                '--module' => $this->module_name_pluralized
            )
        );



    }

    /**
     * Call startup:views
     *
     * @return void
     */
    protected function callViews($admin=FALSE)
    {
        $viewsDir = $this->getModuleTargetPath().str_replace('{Modulename}',ucwords($this->module_name_pluralized),'{Modulename}/Views/');
        $container = $viewsDir . strtolower($this->model_pluralized).($admin === TRUE ? "/admin":"");
        $layouts = app_path() . '/views/front';
        $layouts_admin = app_path() . '/views/admin';
        $views = array('index', 'show', 'create', 'edit');

        $this->generator->folders(
            array($container)
        );


        // Let's filter through all of our needed views
        // and create each one.
        foreach($views as $view)
        {
            switch ($view) {
                default:
                    $path = $container;
                    break;
            }

            //$path = $view === 'template' ? $layouts : $container;
            $this->callView($view, $path,$admin);
        }
    }

    /**
     * Generate a view
     *
     * @param  string $view
     * @param  string $path
     * @return void
     */
    protected function callView($view, $path,$admin=FALSE)
    {
        $this->call(
            'startup:view',
            array(
                'name'       => $view,
                '--path'     => $path,
                '--template' => $this->getModuleViewTemplatePath($view,$admin),
                '--module' => $this->module_name_pluralized
            )
        );
    }

    /**
     * Call controller generator if user confirms
     *
     * @param $resource
     */
    protected function callController($admin=false)
    {
        $name = (is_null($this->add) ? $this->module_name_pluralized : $this->add_pluralized).($admin === TRUE ? "Admin":"");

        $this->call(
            'startup:controller',
            array(
                'name' => "{$name}Controller",
                '--template' => $this->getModuleControllerTemplatePath($admin),
                '--module' => $this->module_name_pluralized
            )
        );


    }

    /**
     * Call startup:migration
     *
     * @return void
     */
    protected function callMigration()
    {
        //$name = 'create_' . Pluralizer::plural($this->model) . '_table';
        $name = 'create_' .(is_null($this->add) ? $this->module_name_pluralized : $this->add_pluralized). '_table';


        $path = $this->getModuleTargetPath().str_replace('{Modulename}',ucwords($this->module_name_pluralized),'{Modulename}/migrations/');
        $this->call(
            'startup:migration',
            array(
                'name'      => $name,
                '--fields'  => $this->option('fields'),
                '--path'    => $path,
                '--template' => $this->getModuleMigrationTemplatePath()
            )
        );
    }

    /**
     * Call seeder generator if user confirms
     *
     * @param $resource
     */
    protected function callSeed()
    {
        $path = $this->getModuleTargetPath().str_replace('{Modulename}',ucwords($this->module_name),'{Modulename}/seeds/');
        $this->call(
            'startup:seed',
            array(
                'name' => Pluralizer::plural(strtolower($this->model)),
                '--path'    => $path
            )
        );
    }



    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Plural/Singular module name']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            /*['path', null, InputOption::VALUE_REQUIRED, 'Where should the file be created?'],*/
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration'],
            ['add', null, InputOption::VALUE_OPTIONAL, 'Singular resource name to add'],
            ['remove', null, InputOption::VALUE_OPTIONAL, 'Singular module name to remove']
        ];
    }

    /**
     * Call model generator if user confirms
     *
     * @param $resource
     */
    /*
    protected function callModel($resource)
    {
        
        $modelName = $this->getModelName($resource);

        if ($this->confirm("Do you want me to create a $modelName model? [yes|no]"))
        {
            $this->call('startup:model', [
                'modelName' => $modelName,
                '--templatePath' => Config::get("generators::config.modules_model_template_path")
            ]);
        }
    }*/

    /**
     * Call controller generator if user confirms
     *
     * @param $resource
     */
    /*
    protected function callController($resource)
    {
        $controllerName = $this->getControllerName($resource);

        if ($this->confirm("Do you want me to create a $controllerName controller? [yes|no]"))
        {
            $this->call('startup:controller', [
                'controllerName' => $controllerName,|
                '--templatePath' => Config::get("generators::config.modules_controller_template_path")
            ]);
        }
    }*/

}
