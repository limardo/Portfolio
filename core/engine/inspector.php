<?php

namespace Core\Engine
{

    class Inspector
    {

        private $_class;
        private $_meta = array(
                    'class'      => array(),
                    'properties' => array(),
                    'methods'    => array()
        );
        private $_name = array(
                    'properties' => array(),
                    'methods'    => array()
        );

        public function __construct( $class )
        {
            $this->_class = $class;
        }

        public function __call( $name, $arguments )
        {
            $pattern = '([A-Z][a-z]*)$';
            $matches = \Core\Helper\StringHelper::match( $name, $pattern );

            if ( !is_null( $matches ) )
            {
                foreach ( $matches as $match )
                {
                    switch ( $match )
                    {
                        case 'Method':
                            $property = str_replace( 'Method', '', $name );
                            $reflaction = new \ReflectionMethod( $this->_class, $arguments[ 0 ] );
                            return call_user_func( array( $reflaction, $property ) );
                            break;
                    }
                }
            }

            return null;
        }

        private function _get_class_commet()
        {
            $reflection = new \ReflectionClass( $this->_class );
            return $reflection->getDocComment();
        }

        private function _get_class_properties()
        {
            $reflection = new \ReflectionClass( $this->_class );
            return $reflection->getProperties();
        }

        private function _get_class_methods()
        {
            $reflection = new \ReflectionClass( $this->_class );
            return $reflection->getMethods();
        }

        private function _get_property_comment( $property )
        {
            $reflection = new \ReflectionProperty( $this->_class, $property );
            return $reflection->getDocComment();
        }

        private function _get_method_comment( $method )
        {
            $reflection = new \ReflectionMethod( $this->_class, $method );
            return $reflection->getDocComment();
        }

        private function _parse( $comment )
        {
            $meta = array();
            $pattern = "(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_]*)";
            $matches = \Core\Helper\StringHelper::match( $comment, $pattern );

            if ( !is_null( $matches ) )
            {
                foreach ( $matches as $match )
                {
                    $item = \Core\Helper\ArrayHelper::clean(
                                    \Core\Helper\ArrayHelper::trim(
                                            \Core\Helper\StringHelper::split( $match, '[\s]', 2 )
                                    )
                    );

                    $meta[ $item[ 0 ] ] = true;

                    if ( count( $item ) > 1 )
                    {
                        $meta[ $item[ 0 ] ] = \Core\Helper\ArrayHelper::clean(
                                        \Core\Helper\ArrayHelper::trim(
                                                \Core\Helper\StringHelper::split( $item[ 1 ], ',' )
                                        )
                        );
                    }
                }
            }

            return $meta;
        }

        public function get_class_meta()
        {
            if ( empty( $this->_meta[ 'class' ] ) )
            {
                $comment = $this->_get_class_commet();

                if ( !empty( $comment ) )
                {
                    $this->_meta[ 'class' ] = $this->_parse( $comment );
                }
                else
                {
                    $this->_meta[ 'class' ] = array();
                }
            }

            return $this->_meta[ 'class' ];
        }

        public function get_class_properties()
        {
            if ( empty( $this->_name[ 'properties' ] ) )
            {
                $properties = $this->_get_class_properties();

                foreach ( $properties as $property )
                {
                    array_push( $this->_name[ 'properties' ], $property->getName() );
                }
            }

            return $this->_name[ 'properties' ];
        }

        public function get_property_meta( $property )
        {

            if ( !isset( $this->_meta[ 'properties' ][ $property ] ) )
            {
                $comment = $this->_get_property_comment( $property );

                if ( !empty( $comment ) )
                {
                    $this->_meta[ 'properties' ][ $property ] = $this->_parse( $comment );
                }
                else
                {
                    $this->_meta[ 'properties' ][ $property ] = null;
                }
            }

            return $this->_meta[ 'properties' ][ $property ];
        }

        public function get_class_methods()
        {
            if ( empty( $this->_name[ 'methods' ] ) )
            {
                $methods = $this->_get_class_methods();

                foreach ( $methods as $method )
                {
                    array_push( $this->_name[ 'methods' ], $method->getName() );
                }
            }

            return $this->_name[ 'methods' ];
        }

        public function get_method_meta( $method )
        {
            if ( !isset( $this->_meta[ 'methods' ][ $method ] ) )
            {
                $comment = $this->_get_method_comment( $method );

                if ( !empty( $comment ) )
                {
                    $this->_meta[ 'methods' ][ $method ] = $this->_parse( $comment );
                }
                else
                {
                    $this->_meta[ 'methods' ][ $method ] = null;
                }
            }

            return $this->_meta[ 'methods' ][ $method ];
        }

    }

}
?>