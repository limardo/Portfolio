<?php

namespace Core\Database
{

    interface DB_Query
    {

        public function get( $table );

        public function get_where( $table, $where = array() );

        public function set( $table, $values );

        public function insert( $table, $values );

        public function update( $table, $values );

        public function delete( $table, $where = null );
    }

}
?>