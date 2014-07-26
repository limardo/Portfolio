<?php

namespace Core\Engine
{

    class View extends Base
    {

        /**
         * @readwrite
         */
        protected $_data;

        /**
         * @readwrite
         */
        protected $_filename;

        public function __construct( $options = array() )
        {
            parent::__construct( $options );

            ob_start();
            include $this->_filename;
            ob_end_flush();
        }

        public function javascript( $path, $position = 'head' )
        {
            $dom = new \DOMDocument();
            $script = $dom->createElement( 'script' );
            $script->setAttribute( 'src', $path );
            $dom->appendChild( $script );
            return $dom->saveHTML();
        }

        public function head()
        {
            echo $this->_filename;
        }

        public function footer()
        {
            echo 'Javascript';
        }

        public function block( $name )
        {
            
        }

        public function event()
        {
            
        }

        public function render()
        {
            
        }

    }

}