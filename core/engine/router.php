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
     * Class Router
     *
     * @author Luca Limardo
     */
    class Router
    {

        private $_url;

        public function __construct()
        {
            $filtered = DEFAULT_CONTROLLER;

            if ( isset( $_SERVER[ 'PATH_INFO' ] ) )
            {
                $filtered = filter_input( INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_URL );
            }

            $this->_url = $filtered;
        }

        public function get_controller()
        {
            $url = explode( '/', trim( $this->_url, '/' ) );
            $controller = null;

            if ( count( $url ) )
            {
                $controller = $url[ 0 ];
            }

            return $controller;
        }

        public function get_action()
        {
            $url = explode( '/', trim( $this->_url, '/' ) );
            $action = null;

            if ( count( $url ) )
            {
                if ( count( $url ) > 1 )
                {
                    $action = $url[ 1 ];
                }
            }

            return $action;
        }

        public function dispatch()
        {
            $url = explode( '/', trim( $this->_url, '/' ) );
            $parameters = array();
            $controller = is_null( $this->get_controller() ) ? DEFAULT_CONTROLLER : $this->get_controller();
            $action = is_null( $this->get_action() ) ? 'index' : $this->get_action();

            if ( count( $url ) )
            {
                if ( count( $url ) > 1 )
                {
                    $parameters = array_splice( $url, 2 );
                }
            }

            $this->_pass( $controller, $action, $parameters );
        }

        private function _pass( $controller, $action, $parameters )
        {
            $parameters = is_array( $parameters ) ? $parameters : array();
            $controller = ucfirst( $controller );

            $class = '\\' . $controller . '\Controller';
            $instance = new $class( array(
                        'parameters' => $parameters,
                    ) );
            \Core\Engine\Registry::set( 'controller', $instance );

            if ( !method_exists( $instance, $action ) )
            {
                trigger_error( "Action <b>{$action}</b> not found in <b>\\{$controller}\Controller</b> class!", E_USER_ERROR );
            }

            $inspector = new \Core\Engine\Inspector( $instance );
            $methodMeta = $inspector->get_method_meta( $action );

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

            $view_default = array(
                        'content_type' => 'html',
                        'template'     => $action,
                        'dirname'      => strtolower( $controller ),
                        'extension'    => '.html'
            );

            $view_parse = function($meta)use($instance, $view_default)
            {
                foreach ( $view_default as $var => $default )
                {
                    $value = $default;

                    if ( !empty( $meta[ '@' . $var ] ) )
                    {
                        $value = current( $meta[ '@' . $var ] );
                    }

                    $instance->view->$var = $value;
                }
            };

            $view_parse( $methodMeta );
            $instance->view->render();
        }

    }

}