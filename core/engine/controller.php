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
     * Class Controller
     *
     * @author Luca Limardo
     */
    class Controller
    {

        protected $_data;
        protected $_view;

        public function __construct()
        {
            $model = str_replace( 'Controller', 'Model', get_called_class() );
            $this->_data = array();
            $this->view = new \Core\Engine\View();

            $template = is_null( Registry::get( 'router' )->get_action() ) ? 'index' : Registry::get( 'router' )->get_action();
            $dirname = is_null( Registry::get( 'router' )->get_controller() ) ? DEFAULT_CONTROLLER : Registry::get( 'router' )->get_controller();

            $inspector = new \Core\Engine\Inspector( $this );
            $methodMeta = $inspector->get_method_meta( $template );

            $view_default = array(
                        'content_type' => 'html',
                        'template'     => $template,
                        'dirname'      => $dirname,
                        'extension'    => '.html'
            );

            $view_parse = function( $meta ) use ( $view_default )
            {
                foreach ( $view_default as $var => $default )
                {
                    $value = $default;

                    if ( !empty( $meta[ '@' . $var ] ) )
                    {
                        $value = current( $meta[ '@' . $var ] );
                    }

                    $this->view->$var = $value;
                }
            };

            if ( class_exists( $model ) )
            {
                class_alias( $model, ucwords( $inspector->get_namespace() ) );
            }

            $view_parse( $methodMeta );
        }

        public function __get( $name )
        {
            return Registry::get( $name );
        }

        public function __set( $name, $value )
        {
            Registry::set( $name, $value );
        }

        public function get_data( $key = null )
        {
            if ( is_null( $key ) )
            {
                return $this->_data;
            }

            return isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : null;
        }

        public function set_data( $key, $data = '' )
        {
            $this->_data[ $key ] = $data;
        }

    }

}