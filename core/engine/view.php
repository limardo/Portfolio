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
    class View
    {

        protected $_content_type;
        protected $_dirname;
        protected $_template;
        protected $_extension = '.html';
        protected $_default = array();
        private $_ob_level;
        private $_output;
        private $_data = array();

        public function __construct( $controller )
        {
            $this->_data = array();
            $this->_ob_level = ob_get_level();

            $template = is_null( Registry::get( 'router' )->get_action() ) ? 'index' : Registry::get( 'router' )->get_action();
            $dirname = is_null( Registry::get( 'router' )->get_controller() ) ? DEFAULT_CONTROLLER : Registry::get( 'router' )->get_controller();

            $inspector = new Inspector( $controller );
            $method = $inspector->get_method_meta( $template );

            $this->_default = array(
                        'content_type' => 'html',
                        'template'     => $template,
                        'dirname'      => $dirname,
                        'extension'    => '.html'
            );

            $this->_parse( $method );

            $this->_output = new Output( array(
                        'content_type' => $this->_content_type
                    ) );
        }

        public function set_data( $data )
        {
            if ( is_array( $data ) || is_object( $data ) )
            {
                foreach ( $data as $k => $v )
                {
                    $this->_data[ $k ] = $v;
                }
            }
        }

        public function get_data()
        {
            return $this->_data;
        }

        public function render()
        {
            $filename = $this->_get_file();

            if ( file_exists( $filename ) )
            {
                extract( $this->_data );

                ob_start();

                include($filename);

                $this->_output->append_output( ob_get_contents() );

                ob_end_clean();

                $this->_output->display();
            }
            else
            {
                trigger_error( 'View: <b>' . $this->template . '</b> not found!', E_USER_NOTICE );
            }
        }

        private function _get_file()
        {
            $this->_extension = preg_replace( '/^(\.)/', '', $this->_extension );
            return APP_PATH . DIRECTORY_SEPARATOR
                    . strtolower( $this->_dirname ) . DIRECTORY_SEPARATOR
                    . strtolower( $this->_template ) . '.' . strtolower( $this->_extension );
        }

        private function _parse( $meta )
        {
            foreach ( $this->_default as $var => $default )
            {
                $value = $default;

                if ( !empty( $meta[ '@' . $var ] ) )
                {
                    $value = current( $meta[ '@' . $var ] );
                }

                $key = '_' . $var;
                $this->$key = $value;
            }
        }

    }

}