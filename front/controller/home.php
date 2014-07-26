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
        public function test()
        {
            $this->load->model( 'user/user' );
            $this->load->view( 'user' );

            $data = $this->model_user_user->all();

            $this->view->css( 'css/bootstrap', true );
            $this->view->javascript( 'js/jquery', true );
            $this->view->javascript( 'js/bootstrap', true );
            $this->view->render( array( 'users' => $data ) );
        }

    }

}
