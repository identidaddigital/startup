<?php

namespace Idcomar\Startup\Generators;

use Illuminate\Support\Pluralizer;

class ViewGenerator extends Generator {

    /**
     * Fetch the compiled template for a view
     *
     * @param  string $template Path to template
     * @param  string $name
     * @return string Compiled template
     */
    protected function getTemplate($template, $name)
    {
        $this->template = $this->file->get($template);

        if ($this->needsModule($template))
        {
            return $this->template = $this->getModuledTemplate($name);
        }
        else
        {
            if ($this->needsScaffolding($template))
            {
                return $this->template = $this->getScaffoldedTemplate($name);
            }
        }  


        // Otherwise, just set the file
        // contents to the file name
        return $name;
    }

    /**
     * Get the scaffolded template for a view
     *
     * @param  string $name
     * @return string Compiled template
     */
    protected function getScaffoldedTemplate($name)
    {
        $model = $this->cache->getModelName();  // post
        $models = Pluralizer::plural($model);   // posts
        $Models = ucwords($models);             // Posts
        $Model = Pluralizer::singular($Models); // Post

        // Create and Edit views require form elements
        if ($name === 'create.blade' or $name === 'edit.blade')
        {
            $formElements = $this->makeFormElements();

            $this->template = str_replace('{{formElements}}', $formElements, $this->template);
        }

        // Replace template vars in view
        foreach(array('model', 'models', 'Models', 'Model') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        // And finally create the table rows
        list($headings, $fields, $editAndDeleteLinks) = $this->makeTableRows($model);
        $this->template = str_replace('{{headings}}', implode(PHP_EOL."\t\t\t\t", $headings), $this->template);
        $this->template = str_replace('{{fields}}', implode(PHP_EOL."\t\t\t\t\t", $fields) . PHP_EOL . $editAndDeleteLinks, $this->template);

        return $this->template;
    }

    protected function getModuledTemplate($name)
    {
        $model = strtolower($this->cache->getModelName());  // post
        $Model = ucwords($model); // Post
        $models = strtolower($this->cache->getPluralModelName());   // posts
        $Models = ucwords($models);             // Posts
        $moduleName = strtolower($this->cache->getModuleName());
        $ModuleName = ucwords($moduleName);        
        // Create and Edit views require form elements
        if ($name === 'create.blade' or $name === 'edit.blade')
        {
            $formElements = $this->makeFormElements();

            $this->template = str_replace('{{formElements}}', $formElements, $this->template);
        }

        // Replace template vars in view
        foreach(array('model', 'models', 'Models', 'Model', 'ModuleName') as $var)
        {
            $this->template = str_replace('{{'.$var.'}}', $$var, $this->template);
        }

        // And finally create the table rows
        list($headings, $fields, $editAndDeleteLinks) = $this->makeTableRows($model);
        //$this->template = str_replace('{{headings}}', implode(PHP_EOL."\t\t\t\t", $headings), $this->template);
        //$this->template = str_replace('{{fields}}', implode(PHP_EOL."\t\t\t\t\t", $fields) . PHP_EOL . $editAndDeleteLinks, $this->template);
        $this->template = str_replace('{{fields}}', implode(PHP_EOL."\t\t\t\t\t", $fields), $this->template);

        return $this->template;
    }


    /**
     * Create the table rows
     *
     * @param  string $model
     * @return Array
     */
    protected function makeTableRows($model)
    {
        $models = Pluralizer::plural($model); // posts

        $fields = $this->cache->getFields();

        // First, we build the table headings
        $headings = array_map(function($field) {
            return '<th>' . ucwords($field) . '</th>';
        }, array_keys($fields));

        // And then the rows, themselves
        $fields = array_map(function($field) use ($model) {
            //return "<td>{{{ \$$model->$field }}}</td>";
            return "'{{{ $field }}}',";
        }, array_keys($fields));

        // Now, we'll add the edit and delete buttons.
        $editAndDelete = <<<EOT
                    <td>
                        {{ Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'route' => array('admin.{$models}.destroy', \${$model}->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                        {{ link_to_route('admin.{$models}.edit', 'Edit', array(\${$model}->id), array('class' => 'btn btn-info')) }}
                    </td>
EOT;

        return array($headings, $fields, $editAndDelete);
    }

    /**
     * Add Laravel methods, as string,
     * for the fields
     *
     * @return string
     */
    public function makeFormElements()
    {
        $formMethods = array();

        foreach($this->cache->getFields() as $name => $type)
        {
            $formalName = ucwords($name);

            // TODO: add remaining types
            switch($type)
            {
                case 'integer':
                   $element = "{{ Form::input('number', '$name', Input::old('$name'), array('class'=>'form-control')) }}";
                    break;

                case 'text':
                    $element = "{{ Form::textarea('$name', Input::old('$name'), array('class'=>'form-control', 'placeholder'=>'$formalName')) }}";
                    break;

                case 'boolean':
                    $element = "{{ Form::checkbox('$name') }}";
                    break;

                default:
                    $element = "{{ Form::text('$name', Input::old('$name'), array('class'=>'form-control', 'placeholder'=>'$formalName')) }}";
                    break;
            }

            // Now that we have the correct $element,
            // We can build up the HTML fragment
            $frag = <<<EOT
        <div class="form-group">
            {{ Form::label('$name', '$formalName:', array('class'=>'col-md-2 control-label')) }}
            <div class="col-sm-10">
              $element
            </div>
        </div>

EOT;

            $formMethods[] = $frag;
        }

        return implode(PHP_EOL, $formMethods);
    }

}
