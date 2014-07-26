<?php

namespace Core\Model {



    class Connector
    {

        private $_class;
        private $_inspector;
        private $_connection;
        private $_table;
        private $_primary_field;
        private $_primary_value = '';
        private $_result;

        public function __construct( $class )
        {
            $this->_class = $class;
            $this->_inspector = new \Core\Engine\Inspector( $class );
            $this->_connection = \Core\Engine\Registry::get( 'db' );
            $this->_set_table()->_set_primary_key();
            if ( $this->is_connected() )
            {
                $this->_result = $this->_connection->get( $this->_table );
            }
        }

        public function __set( $name, $value )
        {
            if ( $this->is_connected() )
            {
                $this->_connection->where( array( $this->_primary_field => $this->_primary_value ) )->set( $this->_table, array( $name => $value ) );
            }
        }

        public function __get( $name )
        {
            if ( $this->is_connected() )
            {
                $result = $this->_connection->get_where( $this->_table, array( $this->_primary_field => $this->_primary_value ) );
                return $result->row()->$name;
            }

            return null;
        }

        public function is_connected()
        {
            return !empty( $this->_table ) && !empty( $this->_primary_field ) ? true : false;
        }

        public function free( $method = 'object' )
        {
            if ( $this->is_connected() )
            {
                $result = $this->_connection->get( $this->_table );
                switch ( $method )
                {
                    case 'array':
                        return $result->rows_array();
                        break;
                    case 'object':
                    default:
                        return $result->rows();
                        break;
                }
            }

            return false;
        }

        public function row( $index = null )
        {
            if ( $this->is_connected() )
            {
                if ( !is_null( $index ) )
                {
                    $this->_primary_value = $index;
                }

                return $this;
            }

            return false;
        }

        public function rows()
        {
            if ( $this->is_connected() )
            {
                $result = $this->_result->next_row();

                if ( isset( $result->id ) )
                {
                    return $this->row( $result->id );
                }
            }

            return false;
        }

        public function delete()
        {
            if ( $this->is_connected() )
            {
                $this->_connection->delete( $this->_table, array( $this->_primary_field => $this->_primary_value ) );
            }
        }

        private function _set_table()
        {
            $meta = $this->_inspector->get_class_meta();
            if ( !empty( $meta[ '@table' ] ) )
            {
                $this->_table = $meta[ '@table' ];
                return $this;
            }

            trigger_error( 'Table not found!', E_USER_ERROR );
        }

        private function _set_primary_key()
        {
            $properties = $this->_inspector->get_class_properties();

            if ( !empty( $properties ) )
            {
                foreach ( $properties as $property )
                {
                    $meta = $this->_inspector->get_property_meta( $property );
                    if ( !empty( $meta[ '@primary_key' ] ) )
                    {
                        $this->_primary_field = $property;
                        return $this;
                    }
                }
            }

            trigger_error( 'Primary Key not found!', E_USER_ERROR );
        }

    }

}
