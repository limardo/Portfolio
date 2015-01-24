<?php

namespace Core\Helper
{

    class StringHelper
    {

        private static $_delimiter = "/";

        public static function get_delimiter()
        {
            return self::$_delimiter;
        }

        public static function set_delimiter( $delimiter )
        {
            self::$_delimiter = $delimiter;
        }

        private static function normalize( $pattern )
        {
            return self::$_delimiter . trim( $pattern, self::$_delimiter ) . self::$_delimiter;
        }

        public static function match( $string, $pattern )
        {
            preg_match_all( self::normalize( $pattern ), $string, $matches, PREG_PATTERN_ORDER );

            if ( !empty( $matches[ 1 ] ) )
            {
                return $matches[ 1 ];
            }

            if ( !empty( $matches[ 0 ] ) )
            {
                return $matches[ 0 ];
            }

            return null;
        }

        public static function split( $string, $pattern, $limit = null )
        {
            $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE;
            return preg_split( self::normalize( $pattern ), $string, $limit, $flags );
        }

        public static function pluralize( $word )
        {
            $plural = array(
                        '/(quiz)$/i'               => '\1zes',
                        '/^(ox)$/i'                => '\1en',
                        '/([m|l])ouse$/i'          => '\1ice',
                        '/(matr|vert|ind)ix|ex$/i' => '\1ices',
                        '/(x|ch|ss|sh)$/i'         => '\1es',
                        '/([^aeiouy]|qu)ies$/i'    => '\1y',
                        '/([^aeiouy]|qu)y$/i'      => '\1ies',
                        '/(hive)$/i'               => '\1s',
                        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
                        '/sis$/i'                  => 'ses',
                        '/([ti])um$/i'             => '\1a',
                        '/(buffal|tomat)o$/i'      => '\1oes',
                        '/(bu)s$/i'                => '\1ses',
                        '/(alias|status)/i'        => '\1es',
                        '/(octop|vir)us$/i'        => '\1i',
                        '/(ax|test)is$/i'          => '\1es',
                        '/s$/i'                    => 's',
                        '/$/'                      => 's' );

            $uncountables = array(
                        'equipment',
                        'information',
                        'rice',
                        'money',
                        'species',
                        'series',
                        'fish',
                        'sheep'
            );

            $irregulars = array(
                        'person' => 'people',
                        'man'    => 'men',
                        'child'  => 'children',
                        'sex'    => 'sexes',
                        'move'   => 'moves'
            );

            foreach ( $uncountables as $uncountable )
            {
                if ( substr( strtolower( $word ), (-1 * strlen( $uncountable ) ) ) == $uncountable )
                {
                    return $word;
                }
            }

            foreach ( $irregulars as $plural_word => $singular_word )
            {
                if ( preg_match( '/(' . $plural_word . ')$/i', $word, $arr ) )
                {
                    return preg_replace( '/(' . $plural_word . ')$/i', substr( $arr[ 0 ], 0, 1 ) . substr( $singular_word, 1 ), $word );
                }
            }

            foreach ( $plural as $rule => $replacement )
            {
                if ( preg_match( $rule, $word ) )
                {
                    return preg_replace( $rule, $replacement, $word );
                }
            }

            return false;
        }

        public static function singularize( $word )
        {
            $singular = array(
                        '/(quiz)zes$/i'                                                    => '\\1',
                        '/(matr)ices$/i'                                                   => '\\1ix',
                        '/(vert|ind)ices$/i'                                               => '\\1ex',
                        '/^(ox)en/i'                                                       => '\\1',
                        '/(alias|status)es$/i'                                             => '\\1',
                        '/([octop|vir])i$/i'                                               => '\\1us',
                        '/(cris|ax|test)es$/i'                                             => '\\1is',
                        '/(shoe)s$/i'                                                      => '\\1',
                        '/(o)es$/i'                                                        => '\\1',
                        '/(bus)es$/i'                                                      => '\\1',
                        '/([m|l])ice$/i'                                                   => '\\1ouse',
                        '/(x|ch|ss|sh)es$/i'                                               => '\\1',
                        '/(m)ovies$/i'                                                     => '\\1ovie',
                        '/(s)eries$/i'                                                     => '\\1eries',
                        '/([^aeiouy]|qu)ies$/i'                                            => '\\1y',
                        '/([lr])ves$/i'                                                    => '\\1f',
                        '/(tive)s$/i'                                                      => '\\1',
                        '/(hive)s$/i'                                                      => '\\1',
                        '/([^f])ves$/i'                                                    => '\\1fe',
                        '/(^analy)ses$/i'                                                  => '\\1sis',
                        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
                        '/([ti])a$/i'                                                      => '\\1um',
                        '/(n)ews$/i'                                                       => '\\1ews',
                        '/s$/i'                                                            => ''
            );

            $uncountables = array(
                        'equipment',
                        'information',
                        'rice',
                        'money',
                        'species',
                        'series',
                        'fish',
                        'sheep',
                        'press',
                        'sms',
            );

            $irregulars = array(
                        'person' => 'people',
                        'man'    => 'men',
                        'child'  => 'children',
                        'sex'    => 'sexes',
                        'move'   => 'moves'
            );

            foreach ( $uncountables as $uncountable )
            {
                if ( substr( strtolower( $word ), (-1 * strlen( $uncountable ) ) ) == $uncountable )
                {
                    return $word;
                }
            }

            foreach ( $irregulars as $singular_word => $plural_word )
            {
                if ( preg_match( '/(' . $plural_word . ')$/i', $word, $arr ) )
                {
                    return preg_replace( '/(' . $plural_word . ')$/i', substr( $arr[ 0 ], 0, 1 ) . substr( $singular_word, 1 ), $word );
                }
            }

            foreach ( $singular as $rule => $replacement )
            {
                if ( preg_match( $rule, $word ) )
                {
                    return preg_replace( $rule, $replacement, $word );
                }
            }

            return $word;
        }

    }

}
?>
