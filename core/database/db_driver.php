<?php

namespace Core\Database
{

    interface DB_Driver
    {

        public function connect();

        public function disconnect();

        public function execute( $sql );

        public function escape( $value );
    }

}
?>