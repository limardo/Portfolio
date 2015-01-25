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

namespace Core\Engine
{

    /**
     * Test Class
     *
     * @author Luca Limardo
     */
    class Test
    {

        private static $_tests = array();

        public static function add( $callback, $title = 'Noname', $set = 'General' )
        {
            self::$_tests[] = array(
                        'set'      => $set,
                        'title'    => $title,
                        'callback' => $callback
            );
        }

        public static function run( $before = null, $after = null )
        {
            if ( !is_null( $before ) )
            {
                $before( self::$_tests );
            }

            $passed = array();
            $failed = array();
            $exceptions = array();

            foreach ( self::$_tests as $test )
            {
                try
                {
                    $result = call_user_func( $test[ 'callback' ] );

                    if ( $result )
                    {
                        $passed[] = array(
                                    'set'   => $test[ 'set' ],
                                    'title' => $test[ 'title' ]
                        );
                    }
                    else
                    {
                        $failed[] = array(
                                    'set'   => $test[ 'set' ],
                                    'title' => $test[ 'title' ]
                        );
                    }
                }
                catch ( \Exception $ex )
                {
                    $exceptions[] = array(
                                'set'   => $test[ 'set' ],
                                'title' => $test[ 'title' ],
                                'type'  => get_class( $ex )
                    );
                }
            }

            if ( !is_null( $after ) )
            {
                $after( self::$_tests );
            }

            return array(
                        'passed'     => $passed,
                        'failed'     => $failed,
                        'exceptions' => $exceptions
            );
        }

    }

}