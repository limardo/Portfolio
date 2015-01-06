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
     * Class View
     *
     * @author Luca Limardo
     */
    class View extends Base
    {

        protected $_dirname;
        protected $_content_type = 'text/html';
        protected $_template;
        protected $_extension = '.html';

        public function render()
        {
            $filename = $this->_get_file();

            if ( file_exists( $filename ) )
            {
                ob_start();
                require_once $filename;
                $output = ob_get_contents();
                ob_end_clean();

                $this->_set_content_type();

                header( 'Content-Type: ' . $this->content_type );
                echo $output;
            }
            else
            {
                trigger_error( 'View: <b>' . $this->template . '</b> not found!', E_USER_NOTICE );
            }
        }

        private function _get_file()
        {
            $this->extension = preg_replace( '/^(\.)/', '', $this->extension );
            return APP_PATH . DIRECTORY_SEPARATOR
                    . strtolower( $this->dirname ) . DIRECTORY_SEPARATOR
                    . strtolower( $this->template ) . '.' . strtolower( $this->extension );
        }

        private function _set_content_type()
        {
            switch ( $this->content_type )
            {
                case 'plain':
                    $this->content_type = 'text/plain';
                    break;
                case 'js':
                    $this->content_type = 'application/javascript';
                    break;
                case 'css':
                    $this->content_type = 'text/css';
                    break;
                case 'xml':
                    $this->content_type = 'application/xml';
                    break;
                case 'json':
                    $this->content_type = 'application/json';
                    break;
                case 'html':
                    $this->content_type = 'text/html';
                    break;
            }
        }

    }

}