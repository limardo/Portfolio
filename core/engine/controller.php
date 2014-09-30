<?php

namespace Core\Engine
{

    class Controller
    {

        /**
         * @readwrite
         */
        protected $_view;

        public function __construct()
        {
            $this->_view = new \Core\Engine\View();
            \Core\Engine\Registry::set( 'view', $this->_view );
        }

        public function __get( $name )
        {
            return \Core\Engine\Registry::get( $name );
        }

        public function __set( $name, $value )
        {
            \Core\Engine\Registry::set( $name, $value );
        }

        public function __destruct()
        {
            var_dump( 'Render' );
        }

    }

}
?>