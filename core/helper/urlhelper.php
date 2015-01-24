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

namespace Core\Helper;
{

    /**
     * Class UrlHelper
     *
     * @author Luca Limardo
     */
    class UrlHelper
    {

        public static function root()
        {
            $current = self::server();
            $current .= current( StringHelper::split( $_SERVER[ "REQUEST_URI" ], 'index.php' ) );
            return $current;
        }

        public static function current()
        {
            $current = self::server();
            $current .= $_SERVER[ "REQUEST_URI" ];
            return $current;
        }

        public static function server()
        {
            $current = @$_SERVER[ "HTTPS" ] == "on" ? "https://" : "http://";
            $current .= $_SERVER[ "SERVER_NAME" ];

            if ( $_SERVER[ "SERVER_PORT" ] != "80" && $_SERVER[ "SERVER_PORT" ] != "443" )
            {
                $current .= ":" . $_SERVER[ "SERVER_PORT" ];
            }

            return $current;
        }

    }

}