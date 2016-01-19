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

use Thybag\SharePointAPI;

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

        $config = new \Adldap\Connections\Configuration();
        $config->setAccountSuffix(\Registry::get('_config')['ad']['account_suffix']);
        $config->setDomainControllers(explode(",",str_replace(" ", '', \Registry::get('_config')['ad']['domain_controllers'])));
        $config->setBaseDn(\Registry::get('_config')['ad']['base_dn']);
        $config->setAdminUsername(\Registry::get('_config')['ad']['admin_username']);
        $config->setAdminPassword(\Registry::get('_config')['ad']['admin_password']);

        $ad = new \Adldap\Adldap($config);

        $users = $ad->groups()->find('jira-ADusers')->getMembers();
        foreach ($users as $user) {
            echo $user['cn'][0].'<br>';
        }

        echo "\n";
        echo "</pre>";

    }

}

