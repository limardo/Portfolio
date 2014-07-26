<?php

namespace Core\Engine {

    class Model
    {

        private $_connector;

        public function __construct()
        {
            $inspector = new \Core\Engine\Inspector( $this );
            $meta = $inspector->get_class_meta();
            if ( !empty( $meta[ '@table' ] ) )
            {
                $this->_connector = new \Core\Model\Connector( $this );
            }
        }

        public function __clone()
        {
            
        }

        public function __set( $name, $value )
        {
            \Core\Engine\Registry::set( $name, $value );
        }

        public function __get( $name )
        {
            return \Core\Engine\Registry::get( $name );
        }

        public function free( $method = 'object' )
        {
            if ( $this->_connector->is_connected() )
            {
                return $this->_connector->free( $method );
            }

            return null;
        }

        public function rows()
        {
            if ( $this->_connector->is_connected() )
            {
                return $this->_connector->rows();
            }

            return null;
        }

        public function row( $index = null )
        {
            if ( $this->_connector->is_connected() )
            {
                return $this->_connector->row( $index );
            }

            return null;
        }

    }

}