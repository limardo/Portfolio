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

namespace Portfolio
{

    /**
     * Portfolio Controller
     *
     * @author Luca Limardo
     */
    class Controller extends \Core\Engine\Controller
    {

        /**
         * @content_type html
         * @template auth
         * @dirname portfolio
         * @extension html
         * @before success,auth
         * @after success,auth
         */
        public function index()
        {
            //$this->load->model( 'portfolio' );

            $person1 = array(
                        'firstname' => 'Luca',
                        'lastname'  => 'Limardo'
            );

            $person2 = new \stdClass();
            $person2->firstname = 'Marco';
            $person2->lastname = 'Donna';

            $p1 = new \Portfolio( $person1 );
            $p2 = new \Portfolio( $person2 );
            $p3 = new \Portfolio();

            $p3->firstname = 'Valerio';
            $p3->lastname = 'Grotta';
/*
            var_dump( $p1 );
            $p1->update( array( 'id' => 100 ) )->delete();
            var_dump( $p1 );
*/
            $this->set_data( 'var', 2 );
        }

        /**
         * @once
         */
        public function auth()
        {
            $this->set_data( 'var', 1 );
        }

        public function success()
        {
            $this->set_data( 'var', 3 );
        }

    }

}