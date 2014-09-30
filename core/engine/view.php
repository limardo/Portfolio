<?php

namespace Core\Engine
{

    class View
    {

        /**
         * @readwrite
         */
        protected static $_data;

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
            $this->_init();
            \Core\Engine\Registry::get( 'load' )->add_shutdown( array( $this, 'render' ) );
        }

        public function bind( $data = array() )
        {
            self::$_data = array_merge( self::$_data, $data );
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

        public static function block( $name )
        {
            $file = self::$_dir . 'block' . DIRECTORY_SEPARATOR . $name . '.php';
            $func = 'block_' . strtolower( $name );
            $data = array();

            if ( method_exists( \Core\Engine\Registry::get( 'controller' ), $func ) )
            {
                $data = \Core\Engine\Registry::get( 'controller' )->$func();
            }

            ob_start();
            extract( $data );
            require_once $file;
            ob_end_flush();
        }

        public function template( $template )
        {
            $this->_set_filename( $template );
        }

        public function render()
        {
            ob_start();
            extract( self::$_data );
            require_once self::$_dir . self::$_filename;
            ob_end_flush();

            $this->_init();
        }

        private function _init()
        {
            $this->_set_filename();
            self::$_dir = \Core\Engine\Registry::get( 'route' )->base . '/view/';
            self::$_head = new \DOMDocument();
            self::$_foot = new \DOMDocument();
            self::$_data = array();
        }

        private function _set_filename( $filename = null )
        {
            self::$_filename = 'index.php';

            if ( !is_null( $filename ) && !empty( $filename ) && is_string( $filename ) )
            {
                self::$_filename = $filename . '.php';
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

    }

}