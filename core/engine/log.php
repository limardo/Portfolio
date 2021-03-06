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
     * Class Log
     *
     * @author Luca Limardo
     */
    class Log extends Base
    {

        protected $_dirname = 'core/logs/';

        public function system( $message )
        {
            $filename = $this->dirname . 'php_error.log';
            $message = \Core\Helper\DateHelper::now( 'r', '[', '] ' ) . $message . PHP_EOL;
            $this->_write( $filename, $message );
        }

        public function get_dirname()
        {
            return $this->dirname;
        }

        public function set_dirname( $dirname )
        {
            $this->dirname = $dirname;
        }

        private function _write( $filename, $message )
        {
            if ( is_writable( $filename ) )
            {
                file_put_contents( $filename, $message, FILE_APPEND | LOCK_EX );
            }
        }

    }

}