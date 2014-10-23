<?php

namespace Idcomar\Startup\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Pluralizer;

class ControllerGenerator extends Generator {

    /**
     * Fetch the compiled template for a controller
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $className)
    {
        $this->template = $this->file->get($template);
        $resource = strtolower(
            str_ireplace('Controller', '', $className)
        );

        if ($this->needsModule($template))
        {
            $this->template = $this->getModuledController($template, $className);
        }
        else
        {
            if ($this->needsScaffolding($template))
            {
                $this->template = $this->getScaffoldedController($template, $className);
            }
        }


        $template = str_replace('{{className}}', $className, $this->template);

        return str_replace('{{collection}}', $resource, $template);
    }

    /**
     * Get template for a scaffold
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string
     */
    protected function getScaffoldedController($template, $className)
    {
        $model = $this->cache->getModelName();  // post
        $models = Pluralizer::plural($model);   // posts
        $Models = ucwords($models);             // Posts
        $Model = Pluralizer::singular($Models); // Post

        foreach(array('model', 'models', 'Models', 'Model', 'className') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        return $this->template;
 
    }

    protected function getModuledController($template, $className)
    {
        $model = lcfirst($this->cache->getModelName());  // post
        $Model = ucwords($model); // Post

        $models = lcfirst($this->cache->getPluralModelName());   // posts
        $Models = ucwords($models);             // Posts
        
        $moduleName = lcfirst($this->cache->getModuleName());
        $ModuleName = ucwords($moduleName);



        foreach(array( 'models','model', 'Model' , 'Models', 'className','ModuleName') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }



        if (! $fields = $this->cache->getFields())
        {
            $this->template =  str_replace('{{create_fields}}', '', $this->template);
            $this->template =  str_replace('{{filter_fields}}', '', $this->template);
            $this->template =  str_replace('{{grid_fields}}', '', $this->template);
            return $this->template;
        }


        //$na = $this->convertFieldsToArray($fields);

        $fields_names = array_keys($fields);
        $fields_values = array_values($fields);

        $create_fields = array_map(function($field,$type) {

            return $this->parseCreateField($field,$type);

        }, $fields_names,$fields_values);
        
        $filter_fields = array_map(function($field,$type) {
            //return "\$filter->add('$field','$field', 'text')";
            return $this->parseFilterField($field,$type);
        }, $fields_names,$fields_values);
        
        $grid_fields = array_map(function($field,$type) {
            //return "\$grid->add('$field','$field', 'text')";
            return $this->parseGridField($field,$type);
        }, $fields_names,$fields_values);        

        $str_grid = "";
        foreach($grid_fields as $k=>$v){
            $str_grid.= PHP_EOL."\t\t".$v.';';
        }
        $str_filter = "";
        foreach($filter_fields as $k=>$v){
            $str_filter.= PHP_EOL."\t\t".$v.';';
        }
        $str_create = "";
        foreach($create_fields as $k=>$v){
            $str_create.= PHP_EOL."\t\t".$v.';';
        }


        $this->template = str_replace('{{create_fields}}', $str_create, $this->template);        
        $this->template = str_replace('{{filter_fields}}', $str_filter, $this->template);        
        $this->template = str_replace('{{grid_fields}}', $str_grid, $this->template);        

        foreach(array( 'models','model', 'Model' , 'Models', 'className','ModuleName') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }        
        
        return $this->template;
    }    


    protected  function parseCreateField($field,$type)
    {
            $aType = explode(':',$type);
            $Field = ucfirst($field);
            $sType = "text";
            $sRules = "required";
            $sAttr = "";
            $sFormat = "";
            $sAdd = "";
            switch ($aType[0]) {
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    $sType = "textarea";
                    $sAttr = "array('rows'=>2)";
                    break;       
                case 'boolean':
                    $sType = "checkbox";
                    $sRules = "";
                    break;        
                case 'date':
                    $sType = "date";
                    $sFormat = "'d/m/Y', 'es'";                
                    break;
                case 'datetime':
                    $sType = "date";
                    $sFormat = "'d/m/Y H:i:s', 'es'";
                    break;                                             
                default:
                    $sType = "text";
                    break;
            }


            $sAdd = "\$edit->add('$field',trans('{{ModuleName}}::{{model}}.fields.$Field'), '$sType')";
            if($sRules !== "")
                $sAdd.="->rule('$sRules')";
            if($sFormat !== "")
                $sAdd.="->format($sFormat)";
            if($sAttr !== "")
                $sAdd.="->attributes($sAttr)";

            
            return $sAdd;
    }    
    protected  function parseFilterField($field,$type)
    {
            $aType = explode(':',$type);
            $Field = ucfirst($field);
            $sType = "text";
            $sFormat = "";
            $sAdd = "";
            $sOptions = "";
            switch ($aType[0]) {
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    return NULL;
                    break;       
                case 'boolean':
                    $sType = "select";
                    $sOptions = "array(''=>'$field','0'=>trans('admin.content.no'),'1'=>trans('admin.content.yes'))";
                    break;        
                case 'date':
                    $sType = "daterange";
                    $sFormat = "'d/m/Y', 'es'";                
                    break;
                case 'datetime':
                    $sType = "daterange";
                    $sFormat = "'d/m/Y H:i:s', 'es'";
                    break;                                             
                default:
                    $sType = "text";
                    break;
            }

            //return "\$filter->add('$field','$field', 'text')";

            $sAdd = "\$this->filter->add('$field',trans('{{ModuleName}}::{{model}}.fields.$Field'), '$sType')";
            
            if($sFormat !== "")
                $sAdd.="->format($sFormat)";
            if($sOptions !== "")
                $sAdd.="->options($sOptions)";            

            
            return $sAdd;
    }    

    protected  function parseGridField($field,$type)
    {
            $aType = explode(':',$type);
            
            $Field = ucfirst($field);
            
            $Field_name = "trans('{{ModuleName}}::{{model}}.fields.$Field')";
            
            
            $sType = "text";
            $sFormat = "";
            $sAdd = "";
            switch ($aType[0]) {
                case 'text':
                case 'longtext':
                case 'mediumtext':
                    $sAdd = "\$this->grid->add('{{ substr(\$$field,0,20) }}...',trans('{{ModuleName}}::{{model}}.fields.$Field'))->filter('strip_tags')";
                    break;       
                case 'boolean':
                    $sAdd = "\$this->grid->add('{{booleanToGrid(\$$field)}}',trans('{{ModuleName}}::{{model}}.fields.$Field'), false)";
                    break;        
                case 'date':
                    $sAdd = "\$this->grid->add('$field',trans('{{ModuleName}}::{{model}}.fields.$Field'), true)->filter('strtotime|date[d/m/Y]')";                
                    break;
                case 'datetime':
                    $sAdd = "\$this->grid->add('$field',trans('{{ModuleName}}::{{model}}.fields.$Field'), true)->filter('strtotime|date[d/m/Y H:i:s]')"; 
                    break;                                             
                default:
                    $sAdd = "\$this->grid->add('$field',trans('{{ModuleName}}::{{model}}.fields.$Field'), true)";
                    break;
            }


            
            return $sAdd;
    }   
}
