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

        public function __construct()
        {
            $this->_data = array();
            $model = str_replace( 'Controller', 'Model', get_called_class() );

            $inspector = new Inspector( $this );

            if ( class_exists( $model ) )
            {
                class_alias( $model, ucwords( $inspector->get_namespace() ) );
            }
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