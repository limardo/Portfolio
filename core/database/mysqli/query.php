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

namespace Core\Database\Mysqli
{

    /**
     * Class Query
     *
     * @author Luca Limardo
     */
    class Query extends \Core\Engine\Base implements \Core\Database\Query
    {

        protected $_connector;
        protected $_table;
        protected $_field = '*';
        protected $_where;
        protected $_order;
        protected $_limit;
        protected $_group;
        protected $_having;
        protected $_result;
        private $_query_method = 'SELECT';

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
                $this->_reset();

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
            $this->_query_method = 'INSERT';
            $this->_from( $table );
            $this->_field = is_object( $values ) ? \Core\Helper\ArrayHelper::from_object( $values ) : $values;
            return $this->_build_query();
        }

        public function update( $table, $values )
        {
            $this->_query_method = 'UPDATE';
            $field = is_object( $values ) ? \Core\Helper\ArrayHelper::from_object( $values ) : $values;
            $output = array();

            if ( is_array( $field ) )
            {
                foreach ( $field as $key => $value )
                {
                    $key = $this->_quote( $key, '`' );
                    $value = $this->_quote( $value );
                    array_push( $output, $key . ' = ' . $value );
                }
            }

            if ( is_string( $field ) )
            {
                array_push( $output, $field );
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

        public function __call( $name, $arguments )
        {
            $method = '_' . strtolower( $name );
            if ( method_exists( $this, $method ) )
            {
                $inspector = new \Core\Engine\Inspector( $this );
                if ( !$inspector->is_method_private( $method ) )
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
            $this->_field = $field == '*' ? '*' : $this->_quote( $field, '`' );
            return $this;
        }

        protected function _from( $table )
        {
            $this->_table = $this->_quote( $this->_connector->prefix . $table, '`' );
            return $this;
        }

        protected function _where( $where, $condition = '%s = %s', $logical = 'AND' )
        {
            $this->_where_having( '_where', $where, $condition, $logical );
            return $this;
        }

        protected function _and( $where_and, $condition = '%s = %s' )
        {
            $this->_and_where_having( '_where', $where_and, $condition );
            return $this;
        }

        protected function _or( $where_or, $condition = '%s = %s' )
        {
            $this->_or_where_having( '_where', $where_or, $condition );
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
                        $o = $this->_quote( $key, '`' ) . ' ' . strtoupper( $value );
                        array_push( $output, $o );
                        continue;
                    }
                    array_push( $output, $this->_quote( $value, '`' ) );
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
            $this->_where_having( '_having', $having, $condition, $logical );
            return $this;
        }

        protected function _having_and( $having_and, $condition = '%s = %s' )
        {
            $this->_and_where_having( '_having', $having_and, $condition );
            return $this;
        }

        protected function _having_or( $having_or, $condition = '%s = %s' )
        {
            $this->_or_where_having( '_having', $having_or, $condition );
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

        private function _reset()
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
                $table = $this->_table;
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
                $this->_reset();
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
                $table = $this->_table;
                if ( is_array( $this->_field ) )
                {
                    $set = array();
                    $values = array();
                    foreach ( $this->_field as $key => $value )
                    {
                        $set[] = $this->_quote( $key, '`' );
                        $values[] = $this->_quote( $value );
                    }
                    $set = implode( ', ', $set );
                    $values = implode( ', ', $values );
                }

                $this->_reset();
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
                $table = $this->_table;
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

                $this->_reset();
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
                $table = $this->_table;
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

                $this->_reset();
                $query = sprintf( $tpl, $table, $where );
                return trim( $query ) . ';';
            }
            return null;
        }

        private function _where_having( $type, $where, $condition = '%s = %s', $logical = 'AND' )
        {
            $output = array();

            if ( is_array( $where ) )
            {
                foreach ( $where as $key => $value )
                {
                    $key = $this->_quote( $key, '`' );
                    $value = $this->_quote( $value );
                    $o = sprintf( $condition, $key, $value );
                    array_push( $output, $o );
                }
            }

            if ( is_string( $where ) )
            {
                array_push( $output, $where );
            }

            $this->$type = implode( ' ' . $logical . ' ', $output );

            return $this;
        }

        private function _and_where_having( $type, $and, $condition = '%s = %s' )
        {
            $output = array();

            if ( is_array( $and ) )
            {
                foreach ( $and as $key => $value )
                {
                    $key = $this->_quote( $key, '`' );
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
                $this->$type .= ' AND ' . implode( ' AND ', $output );
            }

            return $this;
        }

        private function _or_where_having( $type, $or, $condition = '%s = %s' )
        {
            $output = array();

            if ( is_array( $or ) )
            {
                foreach ( $or as $key => $value )
                {
                    $key = $this->_quote( $key, '`' );
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
                $this->$type .= ' OR ' . implode( ' OR ', $output );
            }

            return $this;
        }

    }

}