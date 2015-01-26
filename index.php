<?php

/*
 * The MIT License
 *
 * Copyright 2015 Luca Limardo.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

error_reporting( E_ALL );

define( 'VERSION', '0.0.2' );
define( 'APP_PATH', dirname( __FILE__ ) );

if ( file_exists( 'core/bootstrap.php' ) )
{
    require('core/bootstrap.php');
}
else
{
    die( "File bootstrap.php is not found!" );
}

/**
 * Loader
 */
$loader = new \Core\Engine\Loader();
\Core\Engine\Registry::set( 'load', $loader );

/**
 * Log
 */
$log = new \Core\Engine\Log();
\Core\Engine\Registry::set( 'log', $log );

/**
 * Error
 */
$error = \Core\Engine\Error::initialize( true );
\Core\Engine\Registry::set( 'error', $error );

/**
 * Cache
 */
$cache = new \Core\Engine\Cache( array(
            'driver' => 'memcache'
        ) );
\Core\Engine\Registry::set( 'cache', $cache->get_service() );

/**
 * Database
 */
$database = new \Core\Engine\Database( array(
            'driver'     => DB_DRIVER,
            'parameters' => array(
                        'hostname' => DB_HOSTNAME,
                        'username' => DB_USERNAME,
                        'password' => DB_PASSWORD,
                        'schema'   => DB_DATABASE,
                        'prefix'   => DB_PREFIX,
                        'port'     => DB_PORT,
                        'charset'  => DB_CHARSET
            )
        ) );
\Core\Engine\Registry::set( 'db', $database->get_connector() );

/**
 * Ruoter
 */
$router = new \Core\Engine\Router();
\Core\Engine\Registry::set( 'router', $router );

/**
 * Test
 */
include 'test.php';

/**
 * Init
 */
\Core\Engine\Registry::get( 'router' )->dispatch();

/**
 * Unset all
 */
unset( $loader );
unset( $log );
unset( $error );
unset( $cache );
unset( $database );
unset( $router );
