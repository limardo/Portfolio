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

namespace Core\Cache\Memcache
{

    /**
     * Memcached Driver
     *
     * @author Luca Limardo
     */
    class Driver implements \Core\Cache\Driver
    {

        protected $_service;
        protected $_host = '127.0.0.1';
        protected $_port = '11211';
        protected $_is_connected = false;

        public function connect()
        {
            try
            {
                $this->_service = new \Memcache();
                $this->_service->connect( $this->_host, $this->_port );
                $this->_is_connected = true;
            }
            catch ( Exception $ex )
            {
                trigger_error( 'Unable to connect to service', E_USER_ERROR );
            }

            return $this;
        }

        public function disconnect()
        {
            if ( $this->_is_valid() )
            {
                $this->_service->close();
                $this->_is_connected = false;
            }

            return $this;
        }

        public function get( $key, $default = null )
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( 'Not connected to a valid service' );
            }

            $value = $this->_service->get( $key, MEMCACHE_COMPRESSED );

            if ( $value )
            {
                return $value;
            }

            return $default;
        }

        public function set( $key, $value, $duration = 120 )
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( 'Not connected to a valid service' );
            }

            $this->_service->set( $key, $value, MEMCACHE_COMPRESSED, $duration );
            return $this;
        }

        public function erase( $key )
        {
            if ( !$this->_is_valid() )
            {
                trigger_error( 'Not connected to a valid service' );
            }

            $this->_service->delete( $key );
            return $this;
        }

        protected function _is_valid()
        {
            return ($this->_is_connected && $this->_service instanceof \Memcache && !empty( $this->_service ));
        }

    }

}