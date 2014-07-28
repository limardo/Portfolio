<?php

namespace Core\Engine;
{

    class Log extends Base
    {

        /**
         * @readwrite
         */
        protected $_dirname;

        public function system( $message )
        {
            $filename = $this->_dirname . 'php_error.log';
            $message = $this->_now() . $message . PHP_EOL;
            $this->_write( $filename, $message );
        }

        protected function _write( $filename, $message )
        {
            if ( is_writable( $filename ) )
            {
                file_put_contents( $filename, $message, FILE_APPEND | LOCK_EX );
            }
        }

        protected function _now()
        {
            return '[' . date( 'r' ) . '] ';
        }

    }

}