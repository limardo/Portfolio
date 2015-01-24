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
     * Class Database
     *
     * @author Luca Limardo
     */
    class Database extends Base
    {

        protected $_driver;
        protected $_parameters;
        protected $_connector;

        public function __construct( $options = array() )
        {
            parent::__construct( $options );

            $driver = '\\Core\Database\\' . ucfirst( $this->_driver ) . '\\Driver';
            $query = '\\Core\Database\\' . ucfirst( $this->_driver ) . '\\Query';

            if ( $this->_driver_exists( $driver ) && $this->_driver_exists( $query ) )
            {
                $db = new $driver( $this->_parameters );
                $this->_connector = new $query( array( 'connector' => $db ) );
                $db->connect();
            }
        }

        public function get_connector()
        {
            return $this->_connector;
        }

        private function _driver_exists( $driver )
        {
            $filename = strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, $driver ) );
            return file_exists( \Core\Helper\PathHelper::root() . $filename . '.php' );
        }

    }

}