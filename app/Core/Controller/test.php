<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

namespace App\Core\Controller;

class Test extends \Controller
{
    function index()
    {
        $form = new \Form([
        ]);

        $form->legend('Test form');

        $form->field([
            'label'       => 'Email Address',
            'name'        => 'email',
            'input-prepend' => '<i class="icon-envelope"></i>',
            'input-attributes' => ['placeholder' => 'example@example.com'],
            'input-class' => 'input-small'

        ]);

        $form->field([
            'type'  => 'select',
            'label'       => 'Select form',
            'name'        => 'select',
            'options' => [
                'first'=>'First',
                'second'=>'Second',
                ],
            'input-class' => 'input-large'
        ]);

        $form->field([
            'type'  => 'submit',
            'value'        => 'Send',
            'input-class' => 'input-large'
        ]);

        $this->render(['form' => $form->render()]);


        echo "<pre>----- content-------\n";
        echo "\n";

        //$content = new \Auth();
        $url = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        var_dump(\Registry::get('_page'));
        var_dump(\Registry::get('_config')['user']['session_lifetime']);
        var_dump(session_get_cookie_params());
        echo "\n";

        echo "</pre>";

    }

}

