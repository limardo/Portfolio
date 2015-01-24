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

namespace Core\Database\Mysqli
{

    /**
     * Class Result
     *
     * @author Luca Limardo
     */
    class Result extends \Core\Engine\Base
    {

        protected $_current = null;
        protected $_result;
        private $_rows = array();

        public function __construct( $result, $options = array() )
        {
            parent::__construct( $options );

            if ( $result instanceof \mysqli_result )
            {
                $this->_result = $result;
                $this->_initialize();
            }
            else
            {
                trigger_error( 'Result is wrong!', E_USER_ERROR );
            }
        }

        public function rows_array()
        {
            return $this->_rows;
        }

        public function rows()
        {
            $output = array();

            foreach ( $this->_rows as $row )
            {
                array_push( $output, \Core\Helper\ArrayHelper::to_object( $row ) );
            }

            return $output;
        }

        public function row_array( $current = 0 )
        {
            $this->_current = $current;

            if ( $this->_num_rows_array() >= $current )
            {
                return $this->_rows[ $this->_current ];
            }

            return false;
        }

        public function row( $current = 0 )
        {
            $this->_current = $current;

            if ( $this->_num_rows_array() >= $current )
            {
                return \Core\Helper\ArrayHelper::to_object( $this->_rows[ $this->_current ] );
            }

            return false;
        }

        public function first_row( $method = 'object' )
        {
            $value = $this->_rows[ 0 ];
            return $this->_addictional_method( $value, 'object' );
        }

        public function last_row( $method = 'object' )
        {
            $value = $this->_rows[ $this->_num_rows_array() ];
            return $this->_addictional_method( $value, $method );
        }

        public function next_row( $method = 'object' )
        {
            $this->_current++;

            if ( $this->_num_rows_array() >= $this->_current )
            {
                $value = $this->_rows[ $this->_current ];
                return $this->_addictional_method( $value, $method );
            }

            return false;
        }

        public function previous_row( $method = 'object' )
        {
            $this->_current--;
            if ( 0 <= $this->_current )
            {
                $value = $this->_rows[ $this->_current ];
                return $this->_addictional_method( $value, $method );
            }
            return false;
        }

        public function num_rows()
        {
            return count( $this->_rows );
        }

        public function num_fields()
        {
            $row = $this->first_row( 'array' );
            return count( $row );
        }

        private function _initialize()
        {
            while ( $row = $this->_result->fetch_array( MYSQLI_ASSOC ) )
            {
                $this->_rows[] = $row;
            }
        }

        private function _addictional_method( $value, $method )
        {
            switch ( $method )
            {
                case 'array':
                    return $value;
                    break;
                default :
                case'object':
                    return \Core\Helper\ArrayHelper::to_object( $value );
                    break;
            }
            return false;
        }

        private function _num_rows_array()
        {
            return $this->num_rows() - 1;
        }

    }

}