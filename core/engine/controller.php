<?php

namespace Core\Engine
{

    class Controller
    {

        public function __get( $name )
        {
            return \Core\Engine\Registry::get( $name );
        }

        public function __set( $name, $value )
        {
            \Core\Engine\Registry::set( $name, $value );
        }

    }

}
?>