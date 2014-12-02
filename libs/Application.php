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

        $this->dbConnect();

        // authorize and session
        Registry::set('_auth', new Auth());

        $siteMap = $this->getSiteMap();
        Registry::set('_page', $siteMap['page']);

        // Run application
        if (file_exists(CONTROLLER_PATH .$siteMap['page']['controller'].'.php')){
            require CONTROLLER_PATH .$siteMap['page']['controller']. '.php';
            $controller = new $siteMap['page']['controller']();
            if (method_exists($controller, '__init')) {
                $controller->{'__init'}($siteMap['params']);
            }
            if (method_exists($controller, $siteMap['page']['action'])) {
                $controller->{$siteMap['page']['action']}($siteMap['params']);
            } else {
                $controller->index($siteMap['params']);
            }
            if (method_exists($controller, '__close')) {
                $controller->{'_close'}($siteMap['params']);
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

    private function getSiteMap(){

        $query = $this->db->prepare('SELECT * FROM siteMap WHERE pid = 0');
        $query->execute();
        $main = $query->fetch();

        $query = $this->db->prepare("
          SELECT t1.id, t1.pid, t1.segment, t1.view, t1.layout, t1.controller, t1.action, t1.title, t1.visible
          FROM siteMap t1
          LEFT JOIN authAccess t2 ON t1.id = t2.smapId AND (t2.userId = ? OR t2.groupId = ?)
          WHERE t2.smapId IS NULL OR (NOT t2.smapId IS NULL AND t2.right <> '0')
              ");
        $query->execute([Registry::get('_auth')->userId, Registry::get('_auth')->groupId]);
        $siteMap = $query->fetchAll();

        $this->addSystemPage($siteMap);

        $tree = new Tree($siteMap);
        $tree->each();
        $siteTree = $tree->get();

        Registry::set('siteTree', $siteTree);

        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
        } else {

            $url = $main['segment'];
        }
        $segments = explode('/', $url);


        /*
         *   найти в массиве $segments последнее существующее в массиве $siteTree "childs",
         *   остальное параметры в GET запросе
         */
        $page  =  $siteTree[0];
        $params = [];
        $run = '';
        foreach($segments as $segment){
            $isAction = false;
            if(isset($page['childs'])){
                foreach($page['childs'] as $child){
                    if($child['segment'] == $segment){
                        $page = $child;
                        $run = $segment;
                        $isAction = true;
                    }
                }
            }
            if(!$isAction) $params[] = $segment;

        }

        return ['page'=>$page, 'params'=>$params];
    }

    private function addSystemPage(&$siteMap){
        $systemPage = [
            'resizer' => 'helpers',
            'error' => 'helpers',
            'download' => 'helpers',
        ];

        foreach ($systemPage as $key => $value){
            array_push($siteMap,
                [
                    'id' => '',
                    'pid' => '1',
                    'segment' => $key,
                    'view' => NULL,
                    'layout' => NULL,
                    'controller' => $value,
                    'action' => $key,
                    'title' => '',
                    'visible' => '0',
                ]
            );

        }
    }

 }