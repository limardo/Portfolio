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
            $curret_base = \Core\Engine\Registry::get( 'route' )->base;
            $key = str_replace( '/', '_', preg_replace( '/^(' . strtolower( $curret_base ) . '\D)/', '', strtolower( $model ) ) );
            $model = preg_replace( '/(\w+\/)?(\w+)$/', 'model/${1}${2}', $model );
            $matches = \Core\Helper\StringHelper::match( $model, '/(\/+)/' );
            $model = count( $matches ) < 3 ? $curret_base . '/' . $model : $model;
            $class = explode( '/', $model );
            $class = \Core\Helper\ArrayHelper::capitalize( $class );
            $class = '\\' . implode( '\\', $class );
            $file = $model . '.php';

            if ( file_exists( $file ) )
            {
                $modelClass = new $class();
                \Core\Engine\Registry::set( 'model_' . $key, $modelClass );
                unset( $modelClass );
            }
            else
            {
                trigger_error( 'Class: <b>' . $class . '</b> not found!', E_USER_NOTICE );
            }
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

        public static function custom_var_dump( $expression )
        {
            return 'ok;';
        }

    }

}
?>