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
        // authorize and session
        Registry::set('_auth', new Auth());

        // routing and site map
        $route = new Route();
        Registry::set('_page', $route->sitePage);
        Registry::set('siteTree', $route->siteTree);

        // Run application
        if (file_exists(CONTROLLER_PATH .$route->sitePage['controller'].'.php')){
            require CONTROLLER_PATH .$route->sitePage['controller'].'.php';
            $controller = new $route->sitePage['controller']();
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
            // redirect user to error page (there's a controller for that)
            header('location: ' . URL . 'error/502');
        }
    }

 }