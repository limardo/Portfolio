<?php

namespace Core\Engine
{

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
            trigger_error( "<b>{$name}</b> method doesn't exists!", E_USER_NOTICE );
        }

        public function __get( $name )
        {
            $var = '_' . $name;

            if ( property_exists( $this, $var ) )
            {
                $meta = $this->_inspector->get_property_meta( $var );
                
                if ( empty( $meta[ '@readwrite' ] ) && empty( $meta[ '@read' ] ) )
                {
                    trigger_error( "<b>{$name}</b> is write only!", E_USER_NOTICE );
                }

                if ( isset( $this->$var ) )
                {
                    return $this->$var;
                }
            }

            return null;
        }

        public function __set( $name, $value )
        {
            $var = '_' . $name;
            if ( property_exists( $this, $var ) )
            {
                $meta = $this->_inspector->get_property_meta( $var );

                if ( empty( $meta[ '@readwrite' ] ) && empty( $meta[ '@write' ] ) )
                {
                    trigger_error( "<b>{$name}</b> is read only!", E_USER_NOTICE );
                }

                $this->$var = $value;
            }

            return $this;
        }

    }

}
?>