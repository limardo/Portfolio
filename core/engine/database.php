<?php

namespace Core\Engine
{

    class Database extends Base
    {

        /**
         *
         * @readwrite
         */
        protected $_type;

        /**
         *
         * @readwrite
         */
        protected $_parameters;

        /**
         *
         * @readwrite
         */
        protected $_driver;

        /**
         *
         * @readwrite
         */
        protected $_connector;

        public function __construct( $options = array() )
        {
            parent::__construct( $options );

            $driver = '\\Core\Database\\' . ucfirst( $this->_type ) . '\\Driver';
            $query = '\\Core\Database\\' . ucfirst( $this->_type ) . '\\Query';

            if ( $this->_driver_exists( $driver ) && $this->_driver_exists( $query ) )
            {
                $this->_driver = new $driver( $this->_parameters );
                $this->_connector = new $query( array( 'connector' => $this->_driver ) );
                $this->_driver->connect();
            }
            else
            {
                trigger_error( 'Database Driver not found!', E_USER_ERROR );
            }
        }

        private function _driver_exists( $driver )
        {
            $driver = strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, $driver ) );

            if ( defined( 'APP_PATH' ) )
            {
                $driver = APP_PATH . $driver . '.php';
            }
            else
            {
                $driver = '.' . $driver . '.php';
            }

            return file_exists( $driver );
        }

    }

}

?>