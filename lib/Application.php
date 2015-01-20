<?php
/**
 * @package Loyality Portal
 * @author Supme
 * @copyright Supme 2014
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

class Application
{
    /** @var
     *
     */

    private $siteArray =[];
    private $_category_arr = [];
    private $db;

    /**
     * Starts the Application
     */

    public function __construct()
    {
        $this->isInstall('Core');

        // authorize and session
        Registry::set('_auth', new Auth());

        // routing and site map
        $route = new Route();
        Registry::set('_page', $route->sitePage);
        Registry::set('siteTree', $route->siteTree);

        new Translate();
        Translate::setDefaultLang(Registry::get('_config')['site']['lang']);

        $this->isInstall($route->sitePage['module']);

        // Run application

       $classname = "App\\".$route->sitePage['module']."\\Controller\\".$route->sitePage['controller'];
        if (class_exists($classname)){
            $controller = new $classname;
            if (method_exists($controller, '__init')) {
                $controller->{'__init'}($route->pageParams);
            }
            if (method_exists($controller, $route->sitePage['action'])) {
                $controller->{$route->sitePage['action']}($route->pageParams);
            } else {
                $controller->index($route->pageParams);
            }
            if (method_exists($controller, '__close')) {
                $controller->{'_close'}($route->pageParams);
            }
        } else {
            echo "------------Беда блин-------------";
            // redirect user to error page (there's a controller for that)
            //header('location: ' . Registry::get('_config')['site']['url'] . 'error/502');
        }
    }

    private function isInstall($module)
    {
        //Check module installed
        $classname = "App\\".$module."\\init";
        if (class_exists($classname))
        {
            if (method_exists($classname, 'isInstalled')) {
                $init = new $classname;
                if ($init->{'isInstalled'}() !== true){
                    if (method_exists($classname, 'install')) {
                        $install = $init->{'install'}();
                        if ($install !== true){
                            throw new \Exception(
                                "Module '".$module."' method 'install' return error:\n\t".
                                "> '".$install."'"
                            );
                        }
                    } else{
                        throw new \Exception(
                            "Module '".$module."' not installed and not have a method 'install'"
                        );
                    }
                }
            } else {
                throw new \Exception(
                    "Module '".$module."' not have a method 'isInstalled' in class 'init'"
                );
            }
        } else {
            throw new \Exception(
                "Module '".$module."' not have a init class"
            );
        }

    }
 }