<?php

namespace Core\Database\Mysqli
{

    class Query extends \Core\Engine\Base implements \Core\Database\DB_Query
    {

        /**
         * @readwrite
         */
        protected $_connector;

        /**
         * @readwrite
         */
        protected $_table;

        /**
         * @readwrite
         */
        protected $_field = '*';

        /**
         * @readwrite
         */
        protected $_where;

        /**
         * @readwrite
         */
        protected $_order;

        /**
         * @readwrite
         */
        protected $_limit;

        /**
         * @readwrite
         */
        protected $_group;

        /**
         * @readwrite
         */
        protected $_having;

        /**
         * @readwrite
         */
        protected $_result;

        /**
         * 
         * @write
         */
        private $_query_method = 'SELECT';

        /**
         * 
         * @write
         */
        private $_where_having = '_where';

        public function __call( $name, $arguments )
        {
            $method = '_' . strtolower( $name );
            if ( method_exists( $this, $method ) )
            {
                $inspector = new \Core\Engine\Inspector( $this );
                if ( !$inspector->isPrivateMethod( $method ) )
                {
                    return call_user_func_array( array( $this, $method ), $arguments );
                }
                else
                {
                    trigger_error( "<b>{$method}</b> is private!", E_USER_ERROR );
                }
            }

            return $this;
        }

        private function _quote_service( $value )
        {
            return $this->_quote( $value, '`' );
        }

        private function _quote( $value, $delimiter = "'" )
        {
            if ( is_string( $value ) )
            {
                $escaped = $this->_connector->escape( $value );
                $escaped = $delimiter . $escaped . $delimiter;
                return $escaped;
            }

            if ( is_array( $value ) )
            {
                foreach ( $value as $i => $val )
                {
                    $value[ $i ] = $this->_quote( $val, $delimiter );
                }

                return implode( ', ', $value );
            }

            return $this->_connector->escape( $value );
        }

        private function _reinit_property()
        {
            $this->_field = '*';
            $this->_table = null;
            $this->_where = null;
            $this->_group = null;
            $this->_having = null;
            $this->_order = null;
            $this->_limit = null;
        }

        private function _build_query()
        {
            $method = '_build_' . strtolower( $this->_query_method );
            $query = $this->$method();
            $this->_query_method = 'SELECT';
            if ( !is_null( $query ) )
            {
                return $this->_query( $query );
            }
            return null;
        }

        private function _build_select()
        {
            $field = '';
            $table = '';
            $where = '';
            $group = '';
            $having = '';
            $order = '';
            $limit = '';
            $tpl = 'SELECT %s FROM %s %s';

            if ( !empty( $this->_table ) )
            {
                $field = $this->_field;
                $table = $this->_connector->_prefix . $this->_table;

                if ( !empty( $this->_where ) )
                {
                    $where = 'WHERE ' . $this->_where . ' ';
                }

                if ( !empty( $this->_group ) )
                {
                    $group = 'GROUP BY ' . $this->_group . ' ';
                    $where .= $group;
                }

                if ( !empty( $this->_having ) )
                {
                    $having = 'HAVING ' . $this->_having . ' ';
                    $where .= $having;
                }

                if ( !empty( $this->_order ) )
                {
                    $order = 'ORDER BY ' . $this->_order . ' ';
                    $where .= $order;
                }

                if ( !empty( $this->_limit ) )
                {
                    $limit = 'LIMIT ' . $this->_limit . ' ';
                    $where .= $limit;
                }

                $this->_reinit_property();

                $query = sprintf( $tpl, $field, $table, $where );
                return trim( $query ) . ';';
            }

            return null;
        }

        private function _build_insert()
        {
            $table = '';
            $set = '';
            $values = '';
            $tpl = 'INSERT INTO %s (%s) VALUES (%s)';

            if ( !empty( $this->_table ) )
            {
                $table = $this->_connector->_prefix . $this->_table;

                if ( is_array( $this->_field ) )
                {
                    $set = array();
                    $values = array();

                    foreach ( $this->_field as $key => $value )
                    {
                        $set[] = $this->_quote_service( $key );
                        $values[] = $this->_quote( $value );
                    }

                    $set = implode( ', ', $set );
                    $values = implode( ', ', $values );
                }

                $this->_reinit_property();

                $query = sprintf( $tpl, $table, $set, $values );
                return trim( $query ) . ';';
            }

            return null;
        }

        private function _build_update()
        {
            $table = '';
            $set = '';
            $where = '';
            $order = '';
            $limit = '';
            $tpl = 'UPDATE %s SET %s %s';

            if ( !empty( $this->_table ) )
            {
                $table = $this->_connector->_prefix . $this->_table;
                $set = $this->_field;

                if ( !empty( $this->_where ) )
                {
                    $where = 'WHERE ' . $this->_where . ' ';
                }

                if ( !empty( $this->_order ) )
                {
                    $order = 'ORDER BY ' . $this->_order . ' ';
                    $where .= $order;
                }

                if ( !empty( $this->_limit ) )
                {
                    $limit = 'LIMIT ' . $this->_limit . ' ';
                    $where .= $limit;
                }

                $this->_reinit_property();

                $query = sprintf( $tpl, $table, $set, $where );
                return trim( $query ) . ';';
            }
            return null;
        }

        private function _build_delete()
        {
            $table = '';
            $where = '';
            $order = '';
            $limit = '';
            $tpl = 'DELETE FROM %s %s';

            if ( !empty( $this->_table ) )
            {
                $table = $this->_connector->_prefix . $this->_table;

                if ( !empty( $this->_where ) )
                {
                    $where = 'WHERE ' . $this->_where . ' ';
                }

                if ( !empty( $this->_order ) )
                {
                    $order = 'ORDER BY ' . $this->_order . ' ';
                    $where .= $order;
                }

                if ( !empty( $this->_limit ) )
                {
                    $limit = 'LIMIT ' . $this->_limit . ' ';
                    $where .= $limit;
                }

                $this->_reinit_property();

                $query = sprintf( $tpl, $table, $where );
                return trim( $query ) . ';';
            }

            return null;
        }

        protected function _query( $query )
        {
            $query_result = $this->_connector->execute( $query );
            if ( $query_result === false )
            {
                trigger_error( $this->_connector->get_last_error(), E_USER_ERROR );
            }

            if ( is_object( $query_result ) )
            {
                $result = new \Core\Database\Mysqli\Result( $query_result );
            }
            else
            {
                $result = $query_result;
            }

            return $result;
        }

        protected function _select( $field = '*' )
        {
            $this->_field = $field == '*' ? '*' : $this->_quote_service( $field );
            return $this;
        }

        protected function _from( $table )
        {
            $this->_table = $this->_quote_service( $table );
            return $this;
        }

        protected function _where( $where, $condition = '%s = %s', $logical = 'AND' )
        {
            $var = $this->_where_having;
            $output = array();

            if ( is_array( $where ) )
            {
                foreach ( $where as $key => $value )
                {
                    $key = $this->_quote_service( $key );
                    $value = $this->_quote( $value );
                    $o = sprintf( $condition, $key, $value );
                    array_push( $output, $o );
                }
            }

            if ( is_string( $where ) )
            {
                array_push( $output, $where );
            }

            $this->$var = implode( ' ' . $logical . ' ', $output );
            $this->_where_having = '_where';

            return $this;
        }

        protected function _and( $and, $condition = '%s = %s' )
        {
            $var = $this->_where_having;
            $output = array();

            if ( is_array( $and ) )
            {
                foreach ( $and as $key => $value )
                {
                    $key = $this->_quote_service( $key );
                    $value = $this->_quote( $value );
                    $o = sprintf( $condition, $key, $value );
                    array_push( $output, $o );
                }
            }

            if ( is_string( $and ) )
            {
                array_push( $output, $and );
            }

            if ( !empty( $output ) )
            {
                $this->$var .= ' AND ' . implode( ' AND ', $output );
            }
            $this->_where_having = '_where';

            return $this;
        }

        protected function _or( $or, $condition = '%s = %s' )
        {
            $var = $this->_where_having;
            $output = array();

            if ( is_array( $or ) )
            {
                foreach ( $or as $key => $value )
                {
                    $key = $this->_quote_service( $key );
                    $value = $this->_quote( $value );
                    $o = sprintf( $condition, $key, $value );
                    array_push( $output, $o );
                }
            }

            if ( is_string( $or ) )
            {
                array_push( $output, $or );
            }

            if ( !empty( $output ) )
            {
                $this->$var .= ' OR ' . implode( ' OR ', $output );
            }
            $this->_where_having = '_where';

            return $this;
        }

        protected function _order( $order )
        {
            $output = array();

            if ( is_array( $order ) )
            {
                foreach ( $order as $key => $value )
                {
                    if ( is_int( $key ) )
                    {
                        $o = $value . ' ASC';
                        array_push( $output, $o );
                    }

                    if ( is_string( $key ) )
                    {
                        $o = $key . ' ' . strtoupper( $value );
                        array_push( $output, $o );
                    }
                }
            }

            if ( is_string( $order ) )
            {
                $pattern = '\s([A-Za-z]*)$';
                $o = \Core\Helper\StringHelper::match( $order, $pattern );
                if ( !is_null( $o ) )
                {
                    foreach ( $o as $match )
                    {
                        switch ( strtolower( $match ) )
                        {
                            case 'asc':
                            case 'desc':
                                array_push( $output, $order );
                                break;
                            default :
                                array_push( $output, $order . ' ASC' );
                                break;
                        }
                    }
                }
                else
                {
                    array_push( $output, $order . ' ASC' );
                }
            }

            $this->_order = implode( ', ', $output );

            return $this;
        }

        protected function _group( $group )
        {
            $output = array();

            if ( is_array( $group ) )
            {
                foreach ( $group as $key => $value )
                {
                    if ( is_string( $key ) )
                    {
                        $o = $this->_quote_service( $key ) . ' ' . strtoupper( $value );
                        array_push( $output, $o );
                        continue;
                    }

                    array_push( $output, $this->_quote_service( $value ) );
                }
            }

            if ( is_string( $group ) )
            {
                array_push( $output, $group );
            }

            $this->_group = implode( ', ', $output );

            return $this;
        }

        protected function _having( $having, $condition = '%s = %s', $logical = 'AND' )
        {
            $this->_where_having = '_having';
            $this->_where( $having, $condition, $logical );

            return $this;
        }

        protected function _having_and( $having_and, $condition = '%s = %s' )
        {
            $this->_where_having = '_having';
            $this->_and( $having_and, $condition );

            return $this;
        }

        protected function _having_or( $having_or, $condition = '%s = %s' )
        {
            $this->_where_having = '_having';
            $this->_or( $having_or, $condition );

            return $this;
        }

        protected function _limit( $limit, $offset = null )
        {
            $output = is_null( $offset ) ? $limit . ', 0' : $limit . ', ' . $offset;
            $this->_limit = $output;
            return $this;
        }

        protected function _result()
        {
            return $this->_build_query();
        }

        public function get( $table )
        {
            return $this->_from( $table )->_result();
        }

        public function get_where( $table, $where = array() )
        {
            return $this->_from( $table )->_where( $where )->_result();
        }

        public function set( $table, $values )
        {
            $where = $this->_where;
            if ( !is_null( $this->_where ) )
            {
                $select = $this->get( $table );
                $this->_reinit_property();

                if ( $select->num_rows() )
                {
                    $this->_where = $where;
                    return $this->update( $table, $values );
                }
            }

            return $this->insert( $table, $values );
        }

        public function insert( $table, $values )
        {
            $values = is_object( $values ) ? \Core\Helper\ArrayHelper::from_object( $values ) : $values;
            $this->_query_method = 'INSERT';
            $this->_from( $table );
            $this->_field = $values;
            return $this->_build_query();
        }

        public function update( $table, $values )
        {
            $values = is_object( $values ) ? \Core\Helper\ArrayHelper::from_object( $values ) : $values;
            $this->_query_method = 'UPDATE';
            $output = array();

            if ( is_array( $values ) )
            {
                foreach ( $values as $key => $value )
                {
                    $key = $this->_quote_service( $key );
                    $value = $this->_quote( $value );
                    array_push( $output, $key . ' = ' . $value );
                }
            }

            if ( is_string( $values ) )
            {
                array_push( $output, $values );
            }

            $this->_field = implode( ', ', $output );

            $this->_from( $table );

            return $this->_build_query();
        }

        public function delete( $table, $where = null )
        {
            $this->_query_method = 'DELETE';
            $this->_from( $table );
            if ( !is_null( $where ) )
            {
                $this->_where( $where );
            }
            return $this->_build_query();
        }

    }

}
?>
