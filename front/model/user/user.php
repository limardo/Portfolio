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

        public function all()
        {
            return $this->free( 'array' );
        }

    }

}