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

namespace Core\Engine;
{

    /**
     * Class Loader
     *
     * @author Luca Limardo
     */
    class Loader
    {

        public function __construct()
        {
            spl_autoload_register( array( $this, 'autoload' ) );
            return $this;
        }

        public function autoload( $class )
        {
            if ( defined( 'APP_PATH' ) )
            {
                $file = strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, trim( $class, ' \\' ) ) ) . '.php';
                $combined = APP_PATH . DIRECTORY_SEPARATOR . $file;
                if ( file_exists( $combined ) )
                {
                    include( $combined );
                    return true;
                }
            }
            
            trigger_error( 'Class: <b>' . $class . '</b> not found!', E_USER_ERROR );
        }

        private function _is_class_exists()
        {
            foreach ( debug_backtrace() as $debug )
            {
                if ( is_array( $debug ) )
                {
                    if ( in_array( 'class_exists', $debug ) )
                    {
                        return true;
                    }
                }
            }
            
            return false;
        }

    }

}