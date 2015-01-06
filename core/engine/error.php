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

namespace Core\Engine;
{

    /**
     * Class Error
     *
     * @author Luca Limardo
     */
    class Error
    {

        private static $_instance;
        private static $_is_error = false;
        private static $_error_exit = false;
        private static $_error_output = null;
        private static $_error_log = false;

        public static function initialize( $log = false )
        {
            self::_get_instance();
            set_error_handler( '\Core\Engine\Error::error_handler' );
            set_exception_handler( '\Core\Engine\Error::exception_handler' );
            register_shutdown_function( '\Core\Engine\Error::shutdown' );
            self::$_error_log = $log;
            return self::$_instance;
        }

        public static function error_handler( $errno, $errstr, $errfile, $errline, $errcontext )
        {
            $level = error_reporting();
            if ( $level && $errno )
            {
                switch ( $errno )
                {
                    case E_CORE_ERROR:
                    case E_USER_ERROR:
                        $type = 'Fatal Error';
                        self::$_error_exit = true;
                        break;
                    case E_USER_WARNING:
                    case E_WARNING:
                        $type = 'Warning';
                        break;
                    case E_USER_NOTICE:
                    case E_NOTICE:
                    case @E_STRICT:
                        $type = 'Notice';
                        break;
                    case @E_RECOVERABLE_ERROR:
                        $type = 'Catchable';
                        break;
                    default:
                        $type = 'Unknown Error';
                        self::$_error_exit = true;
                        break;
                }

                $exception = array(
                            'type' => $type,
                            'text' => $errstr,
                            'file' => $errfile,
                            'line' => $errline
                );

                self::$_is_error = true;
                self::exception_handler( $exception, $type );

                if ( self::$_error_exit )
                {
                    exit();
                }
            }
        }

        public static function exception_handler( $exception, $type )
        {
            $class = strtolower( str_replace( ' ', '-', $type ) );
            $template = "<table class='table_error_handler " . $class . "'><tr><td>Type</td><td>%s</td></tr><tr><td>Message</td><td>%s</td></tr><tr><td>File</td><td>%s</td></tr><tr><td>Line</td><td>%s</td></tr></table>";
            self::$_error_output .= sprintf( $template, $exception[ 'type' ], $exception[ 'text' ], $exception[ 'file' ], $exception[ 'line' ] );

            if ( self::$_error_log )
            {
                $log = $exception[ 'type' ] . ": " . $exception[ 'text' ] . " - " . $exception[ 'file' ] . "(" . $exception[ 'line' ] . ")";
                if ( !is_null( \Core\Engine\Registry::get( 'log' ) ) )
                {
                    \Core\Engine\Registry::get( 'log' )->system( $log );
                }
            }
        }

        public static function shutdown()
        {
            if ( self::$_is_error )
            {
                if ( self::$_error_exit )
                {
                    $output = 'page';
                }
                else
                {
                    $output = 'inline';
                }

                ob_start();
                include APP_PATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . $output . '.php';
                ob_end_flush();
            }
        }

        private static function _get_instance()
        {
            if ( is_null( self::$_instance ) )
            {
                $class = get_class();
                self::$_instance = new $class;
            }

            return self::$_instance;
        }

    }

}