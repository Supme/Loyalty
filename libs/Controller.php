<?php

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
        $this->auth = Registry::get('_auth');
        // ToDo придумать как выводить ошибки по человечи
        //if(!$this->auth->read) header('location: ' . URL . 'error/403');
    }

    public function render($data_array = array())
    {

        $twig_loader = new Twig_Loader_Filesystem(Registry::get('_config')['path']['views']);
        $twig = new Twig_Environment($twig_loader,
            [
                'cache' => Registry::get('_config')['path']['views_cache'],
                'debug' => Registry::get('_config')['site']['debug']
            ]);
        $twig->addGlobal('_config', Registry::get('_config'));
        $twig->addGlobal('_page', Registry::get('_page'));
        $twig->addGlobal('_view', Registry::get('_page')['view'].Registry::get('_config')['path']['file_type']);
        $twig->addGlobal('_css', Registry::get('_css'));
        $twig->addGlobal('_js', Registry::get('_js'));
        $twig->addGlobal('_menu', Registry::get('_menu'));
        $twig->addGlobal('_siteTree', Registry::get('siteTree'));
        $twig->addGlobal('_notification', Registry::notification());
        $twig->addGlobal('_user', $this->auth);

        echo $twig->render('_templates/'.Registry::get('_page')['layout'].Registry::get('_config')['path']['file_type'], $data_array);
    }

    public function json($data_array = array())
    {
        header('Content-type: application/json');
        echo json_encode($data_array);
        exit(0);
    }

}
