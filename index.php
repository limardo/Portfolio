<?php

error_reporting( E_ALL );

define( 'VERSION', '0.0.2' );

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
                        'port' => DB_PORT,
                        'charset' => DB_CHARSET
            )
        ) );

//Router
$router = new \Core\Engine\Router( array(
            'base' => 'front',
            'url' => isset( $_GET[ 'p' ] ) ? $_GET[ 'p' ] : 'home'
        ) );

\Core\Engine\Registry::set( 'load', $loader );
\Core\Engine\Registry::set( 'db', $database->connector );
\Core\Engine\Registry::set( 'route', $router );

unset( $loader );
unset( $database );
unset( $router );

// Init
Core\Engine\Registry::get( "route" )->dispatch();
?>