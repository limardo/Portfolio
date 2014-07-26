<?php

namespace Core\Database\Mysqli
{

    class Driver extends \Core\Engine\Base implements \Core\Database\DB_Driver
    {

        protected $_service;

        /**
         * @readwrite
         */
        protected $_hostname;

        /**
         * @readwrite
         */
        protected $_username;

        /**
         * @readwrite
         */
        protected $_password;

        /**
         * @readwrite
         */
        protected $_schema;
       
        /**
         * @readwrite
         */
        protected $_prefix;

        /**
         * @readwrite
         */
        protected $_port = "3306";

        /**
         * @readwrite
         */
        protected $_charset = "utf8";

        /**
         * @readwrite
         */
        protected $_engine = "InnoDB";

        /**
         * @readwrite
         */
        protected $_is_connected = false;

        protected function _is_valid()
        {

            $is_empty = empty( $this->_service );
            $is_instance = $this->_service instanceof \mysqli;

            return ($this->_is_connected && $is_instance && !$is_empty);
        }

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

        public function get_result_array( $result )
        {
            $array = array();
            while ( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                $array[] = $row;
            }
            return $array;
        }

    }

}
?>
