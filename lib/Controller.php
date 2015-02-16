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

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */

class Controller
{
    /**
     * @var
     */
    public $db = null;
    public $auth;
    public $params;

    /**
     * Whenever a controller is created, open a database connection too. The idea behind is to have ONE connection
     * that can be used by multiple models (there are frameworks that open one connection per model).
     */
    function __construct()
    {
        // ToDo придумать как выводить ошибки по человечи
        //if(!$this->auth->read) header('location: ' . URL . 'error/403');
    }

    public function render($data_array = array())
    {

        $twig_loader = new Twig_Loader_Filesystem('../layout/');
        $twig_loader->addPath('../app/'.Registry::get('_page')['module'].'/View/');
        $twig = new Twig_Environment($twig_loader,
            [
                'cache' => Registry::get('_config')['path']['views_cache'],
                'debug' => Registry::get('_config')['site']['debug']
            ]);
        $twig->addGlobal('_config', Registry::get('_config'));
        $twig->addGlobal('_page', Registry::get('_page'));
        $twig->addGlobal('_view', Registry::get('_page')['view'].'.twig');
        $twig->addGlobal('_css', Registry::get('_css'));
        $twig->addGlobal('_js', Registry::get('_js'));
        $twig->addGlobal('_menu', Registry::get('_menu'));
        $twig->addGlobal('_siteTree', Registry::get('siteTree'));
        $twig->addGlobal('_notification', Registry::notification());
        $twig->addGlobal('_user', Registry::get('_auth'));
        $twig->addGlobal('_lang', new Translate());

        echo $twig->render('_'.Registry::get('_page')['layout'].'.twig', $data_array);
    }

    public function json($data_array = array())
    {
        header('Content-type: application/json');
        echo json_encode($data_array);
        exit(0);
    }

}