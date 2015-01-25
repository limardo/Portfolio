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
     * Class Model
     *
     * @author Luca Limardo
     * 
     * Method Meta Accepted
     * * @data_type
     * * - int(n)
     * * - varchar(n)
     * * - datetime
     * * - ...
     * * @primary_key - not supported
     * * @not_null
     * * @unique
     * * @is_bynary
     * * @auto_increment
     * * @default
     * * - any
     */
    class Model
    {

        private $_id;
        private $_table;
        private $_connector;
        private $_inspector;
        private static $_instance;

        public function __construct()
        {
            $this->_connector = Registry::get( 'db' );
            $this->_inspector = new \Core\Engine\Inspector( $this );

            $this->_id = uniqid();
            $this->_table = strtolower( \Core\Helper\StringHelper::pluralize( $this->_inspector->get_namespace() ) );
        }

        public function __clone()
        {
            //Do nothing
        }

        public function __set( $name, $value )
        {
            var_dump( $name );
        }

        public static function all()
        {
            self::_instance();
            return self::$_instance->_connector->get( self::$_instance->_table )->rows();
        }

        public static function create( $options )
        {
            self::_instance();
            self::$_instance->_fill( $options );
            self::$_instance->_connector->insert( self::$_instance->_table, self::$_instance );
            return self::$_instance;
        }

        public static function update( $id, $options )
        {
            self::_instance();
            self::$_instance->_fill( $options );
            self::$_instance->_connector->where( array( 'id' => $id ) )->update( self::$_instance->_table, self::$_instance );
            return $this;
        }

        public static function delete( $id )
        {
            self::_instance();
            self::$_instance->_fill( $options );
            self::$_instance->_connector->delete( self::$_instance->_table, array( 'id' => $id ) );
            return $this;
        }

        private static function _instance()
        {
            if ( is_null( self::$_instance ) )
            {
                $class = get_called_class();
                self::$_instance = new $class();
            }
        }

        private function _fill( $data )
        {
            if ( is_array( $data ) || is_object( $data ) )
            {
                foreach ( $data as $key => $value )
                {
                    if ( $this->_inspector->has_property( $key ) )
                    {
                        $this->$key = $value;
                    }
                }
            }
        }

    }

}