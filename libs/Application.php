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

        //$this->dbConnect();

        // authorize and session
        Registry::set('_auth', new Auth());

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

    // create database connection
    private function dbConnect(){

        try {
            if (DB_TYPE == 'sqlite')
                $this->db = new Database(DB_TYPE, DB_HOST);
            else
                $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            die('Database connection could not be established.');
        }
        Registry::set('_db', $this->db);

    }
 }