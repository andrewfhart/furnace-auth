<?php
/**
 ** Auth
 ** =========================================================================
 **
 ** A multi-modal authentication module for Furnace applications
 **
 **
 */

use furnace\core\Config;
use furnace\connections\Connections;
use furnace\routing\Router;

/* --------------------------------------------------------------------------
 * Authentication Settings
 * -------------------------------------------------------------------------*/

 // Password-based authentication settings
 Config::Set('Auth.password.identity'         , 'username');
 Config::Set('Auth.password.credential'       , 'password');
 Config::Set('Auth.password.email'            , 'mail');
 Config::Set('Auth.password.connection'       , 'default');
 Config::Set('Auth.password.table'            , 'user');
 Config::Set('Auth.password.hash'             , 'MD5');
 Config::Set('Auth.password.salt'             , '8c$3ds8nel3FAd7TzU31b%63F@');

 Config::Set('Auth.registration.use'          , 'password');
 



/* --------------------------------------------------------------------------
 * Module Settings (These only need editing in unusual circumstances)
 * -------------------------------------------------------------------------*/
 Config::Set('Auth.module.path'               , dirname(__FILE__));
 Config::Set('Auth.module.name'               , basename(dirname(__FILE__)));
 Config::Set('Auth.module.controllers.default','default'); 
 
/* --------------------------------------------------------------------------
 * Module URL Settings
 * -------------------------------------------------------------------------*/
 Config::Set('Auth.module.url'                , '/auth');
 Config::Set('Auth.module.url.loginPostTarget', '/auth/login');

/* -------------------------------------------------------------------------
 * Database Settings
 * -------------------------------------------------------------------------*/
 Config::Set('Auth.database.conn'             , 'default');
 
/* -------------------------------------------------------------------------
 * Route Settings
 * -------------------------------------------------------------------------*/
  
 // The routes below apply the default Furnace routing behavior to this
 // module. You should only need to directly modify these routes if you
 // require customized routing behavior. In all other cases, simply
 // adjusting the configuration settings above will update
 // these routes correctly.
 //
 Router::ModuleConnect(  Config::Get('Auth.module.url') . "/:controller/:handler",
 	array("module"     => Config::Get('Auth.module.name')));

 Router::ModuleConnect(  Config::Get('Auth.module.url') . "/:handler", 
 	array("module"     => Config::Get('Auth.module.name'), 
 	      "controller" => Config::Get('Auth.module.controllers.default')));

 // Required for module config files
 Router::ApplyModuleRoutes();

 
