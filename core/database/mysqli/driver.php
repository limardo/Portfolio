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
     * Class Driver
     *
     * @author Luca Limardo
     */
    class Driver extends \Core\Engine\Base implements \Core\Database\Driver
    {

        protected $_service;
        protected $_hostname;
        protected $_username;
        protected $_password;
        protected $_schema;
        protected $_prefix;
        protected $_port = "3306";
        protected $_charset = "utf8";
        protected $_engine = "InnoDB";
        protected $_is_connected = false;

        public function connect()
        {
            if ( !$this->_is_valid() )
            {
                $this->_service = new \mysqli( $this->_hostname, $this->_username, $this->_password, $this->_schema, $this->_port );
                if ( $this->_service->connect_error )
                {
                    trigger_error( "Unable to connect to service", E_USER_ERROR );
                }
                $this->_is_connected = true;
            }
            return $this;
        }

        public function disconnect()
        {
            if ( $this->_is_valid() )
            {
                $this->_is_connected = false;
                $this->_service->close();
                $this->_service = null;
            }
        }

        public function execute( $sql )
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( "Not connected to valid service", E_USER_ERROR );
            }
            return $this->_service->query( $sql );
        }

        public function escape( $value )
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( "Not connected to valid service", E_USER_ERROR );
            }
            return $this->_service->real_escape_string( $value );
        }

        public function get_last_insert_ID()
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( "Not connected to valid service", E_USER_ERROR );
            }
            return $this->_service->insert_id;
        }

        public function get_affected_rows()
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( "Not connected to valid service", E_USER_ERROR );
            }
            return $this->_service->affected_rows;
        }

        public function get_last_error()
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( "Not connected to valid service", E_USER_ERROR );
            }
            return $this->_service->error;
        }

        protected function _is_valid()
        {
            return ($this->_is_connected && $this->_service instanceof \mysqli && !empty( $this->_service ));
        }

    }

}