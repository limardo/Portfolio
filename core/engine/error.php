<?php

namespace Core\Engine
{

    class Error
    {

        private static $_is_error = false;

        private static $_error_exit = false;

        private static $_error_output = null;

        public static function initialize( $log = false )
        {
            set_error_handler( '\Core\Engine\Error::error_handler' );
            set_exception_handler( '\Core\Engine\Error::exception_handler' );
            register_shutdown_function( '\Core\Engine\Error::shutdown' );
            if ( $log !== false )
            {
                if ( !ini_get( 'log_errors' ) )
                {
                    ini_set( 'log_errors', true );
                }

                if ( !ini_get( 'error_log' ) )
                {
                    ini_set( 'error_log', $log );
                }
            }
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

            if ( ini_get( 'log_errors' ) )
            {
                $log = $exception[ 'type' ] . ": " . $exception[ 'text' ] . "\n" . $exception[ 'file' ] . "(" . $exception[ 'line' ] . ")" . "\r\n";
                error_log( $log, 0 );
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

    }

}
?>
