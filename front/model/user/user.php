<?php

namespace Front\Model\User {

    /**
     * @table test
     */
    class User extends \Core\Engine\Model
    {

        /**
         * @primary_key
         * @readwrite
         */
        public $id;

        /**
         * @readwrite
         */
        public $text;

        public function callme()
        {
            var_dump( $this->row( 1 )->text );
            var_dump( 'user2' );
        }

    }

}