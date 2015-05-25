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
        // what time is it?
        $start = microtime(true);

        Auth::sec_session_start();

        // load application config
        if(file_exists('../config.ini'))
            Registry::set('_config', array_merge(parse_ini_file('../config.dist.ini', true), parse_ini_file('../config.ini', true)));
        else
            Registry::set('_config', parse_ini_file('../config.dist.ini', true));

        $this->isInstall('Core');

        // routing and site map
        $route = new Route();
        Registry::set('_page', $route->sitePage);
        Registry::set('siteTree', $route->siteTree);

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
            echo "------------Совсем беда, блин-------------";
            // redirect user to error page (there's a controller for that)
            //header('location: http://'.$_SERVER['HTTP_HOST'] . 'error/502');
        }

        // debug info
        if(Registry::get('_config')['site']['debug']){

            echo "<script type='text/javascript'>function debug_show(){ $('.debug-information').toggle( function(){ $(this).siblings('.debug-information.hide').stop(false, true).slideDown(500);}, function(){ $(this).siblings('.debug-information.hide').stop(false, true).slideUp(500);})};</script>";
            echo "<p style='text-align: right; font-size: xx-small'><a onclick='debug_show();'>Debug</a></p>";
            echo "<div class='debug-information' style='display: none;'>\n<pre>";
            printf('Scripts are executed %.4F seconds.', microtime(true) - $start);
            var_dump( \Cache::log() );
            echo "Application information";
            var_dump( array_reverse( \Registry::log() ) );
            echo "User information:\n";
            $user = new \Auth();
            var_dump($user->getRight());
            echo "</pre>\n</div>";
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