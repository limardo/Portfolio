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

/**
 * Class Output
 *
 * @author Luca Limardo
 */
class Output extends Base
{

    protected $_output;
    protected $_content_type = 'html';

    public function get_output()
    {
        return $this->_output;
    }

    public function set_output( $output )
    {
        $this->_output = $output;
    }

    public function append_output( $output )
    {
        $this->_output .= $output;
    }

    public function display()
    {
        $this->_set_content_type();
        header( 'Content-Type: ' . $this->_content_type );
        echo $this->_output;
    }

    private function _set_content_type()
    {
        switch ( $this->_content_type )
        {
            case 'js':
                $this->_content_type = 'application/javascript';
                break;
            case 'css':
                $this->_content_type = 'text/css';
                break;
            case 'xml':
                $this->_content_type = 'application/xml';
                break;
            case 'json':
                $this->_content_type = 'application/json';
                break;
            case 'html':
                $this->_content_type = 'text/html';
                break;
            case 'plain':
                $this->_content_type = 'text/plain';
                break;
        }
    }

}
