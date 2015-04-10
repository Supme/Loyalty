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

        $test = new \File;
        echo "<pre>----- files-------\n";
        echo "\nResult: ";print_r($test->isFolder('/files/777/folder','infolder2'));echo "\n";
        //echo "json = '".json_encode($test->getFolderFiles('/folder_1/sub_folder1_1'))."'\n";


        echo "</pre>";

    }

}

