<?php

namespace Core\Engine
{

    class Loader
    {

        public function initialize()
        {
            spl_autoload_register( array( $this, 'autoload' ) );
            //\rename_function( 'var_dump', 'original_var_dump' );
            //override_function( 'var_dump', '$expression', 'return \Core\Engine\Loader::custom_var_dump($expression);' );
        }

        public function model( $model )
        {
            $file = $this->_get_file( $model, 'model/' );
            $class = $this->_get_class( $model, 'model/' );
            $key = $this->_get_key( $model, 'model_' );

            if ( file_exists( $file ) )
            {
                $modelClass = new $class();
                \Core\Engine\Registry::set( $key, $modelClass );
                unset( $modelClass );
            }
            else
            {
                trigger_error( 'Class: <b>' . $class . '</b> not found!', E_USER_NOTICE );
            }
        }

        public function view( $view, $data = array() )
        {
            $file = $this->_get_file( $view, 'view/' );
            $key = $this->_get_key( $view, 'view_' );

            if ( file_exists( $file ) )
            {
                $viewClass = new \Core\Engine\View( array(
                            'data' => $data,
                            'filename' => $file
                        ) );
                \Core\Engine\Registry::set( $key, $viewClass );
                unset( $viewClass );
            }
            else
            {
                trigger_error( 'Class: <b>' . $class . '</b> not found!', E_USER_NOTICE );
            }
        }

        public function template( $view )
        {
            
        }

        public function addons( $name, $postfix = '' )
        {
            $file = 'addons' . DIRECTORY_SEPARATOR . strtolower( $name ) . DIRECTORY_SEPARATOR . ucfirst( $name ) . $postfix . 'php';
        
            var_dump($file);
        }

        public function autoload( $class )
        {
            if ( defined( 'APP_PATH' ) )
            {
                $file = strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, trim( $class, ' \\' ) ) ) . '.php';
                $combined = APP_PATH . DIRECTORY_SEPARATOR . $file;
                if ( file_exists( $combined ) )
                {
                    include($combined);
                    return;
                }
            }

            trigger_error( 'Class: <b>' . $class . '</b> not found!', E_USER_ERROR );
        }

        private function _get_file( $pattern, $directory = '' )
        {
            $curret_base = \Core\Engine\Registry::get( 'route' )->base;
            $pattern = preg_replace( '/(\w+\/)?(\w+)$/', $directory . '${1}${2}', $pattern );
            $matches = \Core\Helper\StringHelper::match( $pattern, '/(\/+)/' );
            $pattern = count( $matches ) < 3 ? $curret_base . '/' . $pattern : $pattern;
            $file = $pattern . '.php';
            return $file;
        }

        private function _get_class( $pattern, $directory = '' )
        {
            $pattern = $this->_get_file( $pattern, $directory );
            $pattern = str_replace( '.php', '', $pattern );
            $class = explode( '/', $pattern );
            $class = \Core\Helper\ArrayHelper::capitalize( $class );
            $class = '\\' . implode( '\\', $class );
            return $class;
        }

        private function _get_key( $pattern, $prefix = '' )
        {
            $curret_base = \Core\Engine\Registry::get( 'route' )->base;
            $key = str_replace( '/', '_', preg_replace( '/^(' . strtolower( $curret_base ) . '\D)/', '', strtolower( $pattern ) ) );
            return $prefix . $key;
        }

        public static function custom_var_dump( $expression )
        {
            return 'ok;';
        }

    }

}
?>