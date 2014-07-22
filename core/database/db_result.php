<?php

namespace Core\Database
{

    interface DB_Result
    {

        public function rows_array();

        public function rows();

        public function row_array();

        public function row();

        public function first_row();

        public function last_row();

        public function next_row();

        public function previous_row();

        public function num_rows();

        public function num_fields();
    }

}
?>