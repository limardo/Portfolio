<?php

namespace Core\Engine
{

    class View extends Base
    {

        /**
         * @readwrite
         */
        protected static $_dir;

        /**
         * @readwrite
         */
        protected static $_filename;

        /**
         * @write
         */
        protected static $_head;

        /**
         * @write
         */
        protected static $_foot;

        public function __construct()
        {
            parent::__construct();

            self::$_head = new \DOMDocument();
            self::$_foot = new \DOMDocument();
        }

        public function set_dir( $dir )
        {
            self::$_dir = $dir;
        }

        public function set_filename( $filename = null )
        {
            if ( is_null( $filename ) )
            {
                self::$_filename = '/index.php';
                $action = Registry::get( 'route' )->get_action();
                if ( file_exists( self::$_dir . DIRECTORY_SEPARATOR . $action . '.php' ) )
                {
                    self::$_filename = DIRECTORY_SEPARATOR . $action . '.php';
                }
            }
            else
            {
                self::$_filename = $filename;
            }
        }

        private static function _get_path( $name, $extension = 'php' )
        {
            if ( count( explode( '/', $name ) ) > 1 )
            {
                return preg_replace( '/(\/\w+)$/', DIRECTORY_SEPARATOR . $name . '.' . $extension, self::$_dir );
            }
            else
            {
                return self::$_dir . DIRECTORY_SEPARATOR . $name . '.' . $extension;
            }
        }

        public function css( $path )
        {
            $script = self::$_head->createElement( 'link' );
            $script->setAttribute( 'href', self::_get_path( $path, 'css' ) );
            $script->setAttribute( 'rel', 'stylesheet' );
            self::$_head->appendChild( $script );
        }

        public function javascript( $path, $footer = false )
        {
            $scope = $footer ? self::$_foot : self::$_head;
            $script = $scope->createElement( 'script' );
            $script->setAttribute( 'src', self::_get_path( $path, 'js' ) );
            $scope->appendChild( $script );
        }

        public static function head()
        {
            echo self::$_head->saveHTML();
        }

        public static function footer()
        {
            echo self::$_foot->saveHTML();
        }

        public static function block( $name, $data = array() )
        {
            $file = self::_get_path( $name );
            ob_start();
            extract( $data );
            require_once $file;
            ob_end_flush();
        }

        public function render( $data = array() )
        {
            ob_start();
            extract( $data );
            require_once self::$_dir . self::$_filename;
            ob_end_flush();
        }

    }

}