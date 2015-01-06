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

namespace Core\Engine
{

    /**
     * Class of Base
     *
     * @author Luca Limardo
     */
    class Base
    {

        private $_inspector;

        public function __construct( $options = array() )
        {
            $this->_inspector = new \Core\Engine\Inspector( $this );

            if ( is_array( $options ) || is_object( $options ) )
            {
                foreach ( $options as $key => $value )
                {
                    $this->$key = $value;
                }
            }
        }

        public function __call( $name, $arguments )
        {
            unset( $arguments );
            trigger_error( "<b>{$name}</b> method doesn't exists!", E_USER_NOTICE );
        }

        public function __get( $name )
        {
            $var = '_' . $name;
            if ( property_exists( $this, $var ) && !$this->_inspector->is_property_private( $var ) )
            {
                if ( isset( $this->$var ) )
                {
                    return $this->$var;
                }
            }
        }

        public function __set( $name, $value )
        {
            $var = '_' . $name;
            if ( property_exists( $this, $var ) && !$this->_inspector->is_property_private( $var ) )
            {
                $this->$var = $value;
            }
        }

    }

}