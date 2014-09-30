<?php

namespace Front\Controller
{

    class home extends \Core\Engine\Controller
    {

        function index()
        {
            var_dump( 'index' );
        }

        /**
         * @once
         */
        public function login()
        {
            $this->load->model( 'user/user' );

            /*
              $r1 = $this->db->from( 'test' )
              ->order( 'id' )
              ->result();

              $r2 = $this->db->select()
              ->from( 'test' )
              ->where( 'id = 1' )
              ->and( array( 'text' => 'GO' ) )
              ->or( 'text = "TEXT"' )
              ->group( array( 'id' => 'DESC', 'text' ) )
              ->having( array( 'id' => 1 ) )
              ->having_and( 'text = 1' )
              ->having_or( array( 'text' => 'TE' ) )
              ->order( 'id' )
              ->limit( 1, 0 )
              ->result();

              $r3 = $this->db->select( array( 'id', 'text' ) )
              ->from( 'test' )
              ->group( 'id' )
              ->order( 'id DESC, text ASC' )
              ->limit( 1, 10 )
              ->result();

              $r4 = $this->db->get( 'test' );
              $r5 = $this->db->get_where( 'test', 'id = 2' );


              foreach ( $r1->rows_array() as $row )
              {
              echo '<pre>';
              var_dump( $row );
              echo '</pre>';
              }
             */
        }

        /**
         * @before login
         * @after login
         */
        public function test( $m = null )
        {
            $this->load->model( 'user/user' );


            $data = $this->model_user_user->all();

            $this->view->template( 'mario' );

            var_dump( $m );

            /*
              $this->view->template( 'user' );
              $this->view->css( 'bootstrap' );
              $this->view->javascript( 'jquery' );
              $this->view->javascript( 'bootstrap' );
              $this->view->bind( array( 'users' => $data ) );
             * 
             */
        }

        public function block_menu()
        {
            return array( 'users' => $this->model_user_user->all() );
        }

    }

}
