<?php

namespace Core\Engine
{

    class Router extends Base
    {

        /**
         * @readwrite
         */
        protected $_base;

        /**
         * @readwrite
         */
        protected $_url;

        private function _pass( $controller, $action, $parameters )
        {
            $parameters = is_array( $parameters ) ? $parameters : array();

            try
            {
                $class = '\\' . ucfirst( $this->_base ) . '\Controller\\' . ucfirst( $controller );
                $instance = new $class( array(
                    'parameters' => $parameters,
                        ) );
                \Core\Engine\Registry::set( 'controller', $instance );
            }
            catch ( Exception $e )
            {
                trigger_error( "Controller <b>{$name}</b> not found!", E_USER_ERROR );
            }

            if ( !method_exists( $instance, $action ) )
            {
                trigger_error( "Action <b>{$action}</b> not found!", E_USER_ERROR );
            }

            $inspector = new \Core\Engine\Inspector( $instance );
            $methodMeta = $inspector->get_method_meta( $action );

            if ( !empty( $methodMeta[ '@protected' ] ) || !empty( $methodMeta[ '@private' ] ) )
            {
                trigger_error( "Action <b>{$action}</b> is protected or private!", E_USER_ERROR );
            }

            $run = array();
            $hooks = function($meta, $type) use ($inspector, $instance, &$run)
            {
                if ( isset( $meta[ $type ] ) )
                {
                    foreach ( $meta[ $type ] as $method )
                    {
                        $hookMeta = $inspector->get_method_meta( $method );
                        if ( in_array( $method, $run ) && !empty( $hookMeta[ '@once' ] ) )
                        {
                            continue;
                        }

                        $instance->$method();
                        $run[] = $method;
                    }
                }
            };

            $hooks( $methodMeta, '@before' );
            call_user_func_array( array( $instance, $action ), $parameters );
            $hooks( $methodMeta, '@after' );
        }

        public function dispatch()
        {
            $url = explode( '/', trim( $this->_url, '/' ) );
            $parameters = array();
            $controller = 'index';
            $action = 'index';

            if ( count( $url ) )
            {
                $controller = $url[ 0 ];

                if ( $url > 1 )
                {
                    $action = $url[ 1 ];
                    $parameters = array_splice( $url, 2 );
                }
            }

            $this->_pass( $controller, $action, $parameters );
        }

    }

}
?>