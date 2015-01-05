<?php

error_reporting( E_ALL );

define( 'VERSION', '0.0.1' );

if ( file_exists( 'config.php' ) )
{
    require('config.php');
}
else
{
    die( "File config.php is not found!" );
}

if ( file_exists( 'core/engine/loader.php' ) )
{
    require('core/engine/loader.php');
}
else
{
    die( "Class Loader is not found!" );
}

//Loader
$loader = new \Core\Engine\Loader();
$loader->initialize();
\Core\Engine\Registry::set( 'load', $loader );

//Log
\Core\Engine\Registry::set( 'log', new \Core\Engine\Log( array(
            'dirname' => 'core/logs/'
) ) );

//Error
\Core\Engine\Error::initialize( true );

//Database
$database = new \Core\Engine\Database( array(
            'type' => DB_DRIVER,
            'parameters' => array(
                        'hostname' => DB_HOSTNAME,
                        'username' => DB_USERNAME,
                        'password' => DB_PASSWORD,
                        'schema' => DB_DATABASE,
                        'prefix' => DB_PREFIX,
                        'port' => DB_PORT,
                        'charset' => DB_CHARSET
            )
        ) );
\Core\Engine\Registry::set( 'db', $database->connector );

//Router
$router = new \Core\Engine\Router( array(
            'base' => isset( $_GET['base'] ) ? $_GET['base'] : BASE_DEFAULT,
            'url' => isset( $_GET['url'] ) ? $_GET['url'] : PAGE_DEFAULT
        ) );
\Core\Engine\Registry::set( 'route', $router );

unset( $loader );
unset( $database );
unset( $router );
// Init
Core\Engine\Registry::get( "route" )->dispatch();
?>
