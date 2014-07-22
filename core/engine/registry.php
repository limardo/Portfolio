<?php

namespace Core\Engine
{

    class Registry
    {

        private static $_data = array();

        public function __construct()
        {
            
        }

        public static function get( $key )
        {
            return self::$_data[ $key ];
        }

        public static function set( $key, $value )
        {
            self::$_data[ $key ] = $value;
        }

    }

}
?>