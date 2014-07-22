<?php

namespace Core\Helper
{

    Class ArrayHelper
    {

        public static function clean( $array )
        {
            return array_filter( $array, function($item)
            {
                return !empty( $item );
            } );
        }

        public static function trim( $array )
        {
            return array_map( function($item)
            {
                return trim( $item );
            }, $array );
        }

        public static function to_object( $array )
        {
            $result = new \stdClass();

            foreach ( $array as $key => $value )
            {
                if ( is_array( $value ) )
                {
                    $result->$key = self::to_object( $value );
                }
                else
                {
                    $result->$key = $value;
                }
            }

            return $result;
        }

        public static function from_object( $object )
        {
            $result = array();

            foreach ( $object as $key => $value )
            {
                if ( is_object( $value ) )
                {
                    $result[ $key ] = self::from_object( $value );
                }
                else
                {
                    $result[ $key ] = $value;
                }
            }

            return $result;
        }

        public static function flatten( $array, $return = array() )
        {
            foreach ( $array as $key => $value )
            {
                if ( is_array( $value ) || is_object( $value ) )
                {
                    $return = self::flatten( $value, $return );
                }
                else
                {
                    $return[] = $value;
                }
            }

            return $return;
        }

        public static function fist( $array )
        {
            return array_splice( $array, 0, 1 );
        }

        public static function capitalize( $array )
        {
            foreach ( $array as $key => $value )
            {
                $return[ $key ] = ucfirst( $value );
            }

            return $return;
        }

    }

}
?>