<?php
return array(
    /**
     * Default dashboard uri
     */
    'path' => 'app/modules',
    
    'replace_config' => array(
    	'packages/cartalyst/sentry/config.php' => array(
    		array('search'=>"'login_attribute' => 'email'",'replace'=>"'login_attribute' => 'username'")
		),
    	'packages/mrjuliuss/syntara/config.php' => array(
    		array('search'=>"'uri' => 'dashboard'",'replace'=>"'uri' => 'admin'")
		),
    	'packages/mrjuliuss/syntara/views.php' => array(
    		array('search'=>"'master' => 'syntara::layouts.dashboard.master'",'replace'=>"'master' => 'admin.template'")
		),

   	)
);