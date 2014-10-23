<?php
return array(

	'menu_admin' => array(
            "users" => array(
                "title" => trans('syntara::breadcrumbs.users'),
                "icon"  => "user",
                "level" => 1,
                "perms"	=> array("view-users-list"),
                "sub"=> array(
                    "users/list" => array(
                        "title" => trans('syntara::users.titles.list'),
                        "url"   => URL::route('listUsers'),
                        "level" => 2,
                        "perms"	=> array("view-users-list"),
                    ),
                    "users/create" => array(
                        "title" => trans('syntara::users.titles.new'),
                        "url"   => URL::route('newUser'),
                        "level" => 2,
                        "perms"	=> array("create-user"),

                    ),		                                
            	)    
            ),
			"roles" => array(
                "title" => trans('syntara::breadcrumbs.groups'),
                "icon"  => "group",
                "level" => 1,
                "perms"	=> array("groups-management"),
                "sub"=> array(
                	"roles/list" => array(
                        "title" => trans('syntara::groups.titles.list'),
                        "url"   => URL::route('listGroups'),
                        "level" => 2,	
                        "perms"	=> array("groups-management"),	                	
            		),
                	"roles/create" => array(
                        "title" => trans('syntara::groups.titles.new'),
                        "url"   => URL::route('newGroup'),
                        "level" => 2,
                        "perms"	=> array("groups-management"),
            		)	                		
                ) 
            ),            
			"permissions" => array(
                "title" => trans('syntara::breadcrumbs.permissions'),
                "icon"  => "check-circle-o",
                "level" => 1,
                "perms"	=> array("permissions-management"),
                "sub"=> array(
                	"permissions/list" => array(
                        "title" => trans('syntara::permissions.titles.list'),
                        "url"   => URL::route('listPermissions'),
                        "level" => 2,
                        "perms"	=> null
                        
            		),
                	"permissions/create" => array(
                        "title" => trans('syntara::permissions.titles.new'),
                        "url"   => URL::route('newPermission'),
                        "level" => 2,
                        "perms"	=> null
            		)	                		
                ) 
            ),
    ),

	

);
