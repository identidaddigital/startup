<?php namespace Idcomar\Startup\Commands;

use Idcomar\Startup\Generators\MigrationGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationGeneratorCommand extends BaseGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'startup:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new migration.';

    /**
     * Model generator instance.
     *
     * @var Way\Generators\Generators\MigrationGenerator
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MigrationGenerator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $name = $this->argument('name');
        $path = $this->getPath();
        $fields = $this->option('fields');
        $template = $this->getTemplate();

        $created = $this->generator
                        ->parse($name, $fields)
                        ->make($path, $template);

        $this->call('dump-autoload');

        $this->printResult($created, $path);
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
       return $this->option('path') . '/' . ucwords($this->argument('name')) . '.php';
    }
    protected function getTemplate()
    {
       
        //if (is_null($this->option('module')))
        //{
            return $this->option('template') ;     
        //}
        //else
        //{
        //    return $this->getModuleViewTemplatePath($this->argument('name'));     
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
            array('name', InputArgument::REQUIRED, 'Name of the migration to generate.'),
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
            array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations folder', app_path() . '/database/migrations'),
            array('fields', null, InputOption::VALUE_OPTIONAL, 'Table fields', null),
            array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.', __DIR__.'/../Generators/templates/migration.txt'),            
        );
    }

}
