<?php

    function has_permissions($array=array()) {

        $return = count($array) > 0 ? FALSE : TRUE;

        if (Sentry::check()){


            foreach($array as $perm){

                if (Sentry::getUser()->hasAccess($perm)){
                    $return = TRUE;
                    return $return;
                }
            }
        }
        return $return;

    }

    function current_user_name(){
        
        return Sentry::getUser()->getAttributes()['username'];

    }
    function current_user_profile(){
        return URL::route('showUser', Sentry::getUser()->getAttributes()['id']);
    }

    function page_content_title(){
        $title = "";
        if (Request::is(Config::get('syntara::config.uri').'/users')){
            $title = trans('syntara::users.titles.list');
        }elseif (Request::is(Config::get('syntara::config.uri').'/groups')){
            $title = trans('syntara::groups.titles.list');
        }elseif (Request::is(Config::get('syntara::config.uri').'/permissions')){
            $title = trans('syntara::permissions.titles.list');
        }elseif (Request::is(Config::get('syntara::config.uri'))){
            $title = trans('syntara::breadcrumbs.dashboard');
        }elseif (Request::is(Config::get('syntara::config.uri').'/user/*')){

            $segment = Request::segment(3);

            switch ($segment) {
                case 'new':
                    $title =  trans('syntara::users.titles.new');       
                    break;
                default:
                    if (is_numeric($segment)){
                        $title = trans('syntara::all.update');
                    }
                    break;
            }
        }elseif (Request::is(Config::get('syntara::config.uri').'/group/*')){

            $segment = Request::segment(3);

            switch ($segment) {
                case 'new':
                    $title =  trans('syntara::groups.titles.new');       
                    break;
                default:
                    if (is_numeric($segment)){
                        $title = trans('syntara::all.update');
                    }
                    break;
            }
        }elseif (Request::is(Config::get('syntara::config.uri').'/permission/*')){

            $segment = Request::segment(3);

            switch ($segment) {
                case 'new':
                    $title =  trans('syntara::permissions.titles.new');       
                    break;
                default:
                    if (is_numeric($segment)){
                        $title = trans('syntara::all.update');
                    }
                    break;
            }
        }
        return $title;
    }

function displayAlert()
{
      if (Session::has('message'))
      {
         list($type, $message) = explode('|', Session::get('message'));
         $close_button = '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
         switch ($type) {
             case 'error':
             case 'danger':
             case 'message':
             case 'info':
             case 'success':

                 return sprintf('<div class="alert alert-%s">%s%s</div>', $type,$close_button, $message);
                 break;
             
             default:
                 return sprintf('<div class="alert alert-message">%s%s</div>', $close_button, $message);
                 break;
         }
         
      }
      return '';
}    

function booleanToGrid($value){
  return "<div class='bool'><span class='label label-".($value == '1' ? 'success' : 'danger')."'>".($value == '1' ? trans('admin.content.yes'):trans('admin.content.no'))."</span></div>";
}