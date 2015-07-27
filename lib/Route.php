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

class Route extends Db
{

    public $siteTree;

    public $sitePage;

    public $pageParams;

    protected $_siteMap = [];

    protected $_three = [];

    public function __construct(){

        parent::__construct();

        $main = Cache::get('_mainPage');
        if(empty($main)) {
            $main = $this->select('core_sitemap', '*', ['pid' => 0]);
            Cache::set('_mainPage', $main);
        }

        $this->_siteMap = $this->query("
          SELECT distinct sitemap.id, sitemap.pid, sitemap.segment, sitemap.view, sitemap.layout, sitemap.module, sitemap.controller, sitemap.action, sitemap.title, sitemap.menu
          FROM core_sitemap sitemap
        ")->fetchAll();

        // ToDo add static route for controllers
        $this->addSystemPage([
            'auth' => 'helpers',
            'resizer' => 'helpers',
            'error' => 'helpers',
            'files' => 'files',
            'fm' => 'files',
        ]);

        $this->each();
        $this->siteTree = $this->_three;

        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
        } else {
            $url = isset($main['segment'])?$main['segment']:'/';
        }
        $segments = explode('/', $url);

        /*
         *   найти в массиве $segments последнее существующее в массиве $siteTree "childs",
         *   остальное параметры в GET запросе
         */
        $this->sitePage  =  $this->siteTree[0];
        $this->pageParams = [];
        $run = '';
        foreach($segments as $segment){
            $isAction = false;
            if(isset($this->sitePage['childs'])){
                foreach($this->sitePage['childs'] as $child){
                    if($child['segment'] == $segment){
                        $this->sitePage = $child;
                        $run = $segment;
                        $isAction = true;
                    }
                }
            }
            if(!$isAction) $this->pageParams[] = $segment;

        }
    }

    private function addSystemPage($pages){
        foreach ($pages as $key => $value){
            array_push($this->_siteMap,
                [
                    'id' => '',
                    'pid' => '1',
                    'segment' => $key,
                    'view' => NULL,
                    'layout' => NULL,
                    'module' => 'Core',
                    'controller' => $value,
                    'action' => $key,
                    'title' => '',
                    'menu' => '0',
                ]
            );
        }
    }

    /**
     *
     * @return \Tree
     */
    private function each()
    {
        $this->_clear();

        $rebuild_array = array();
        foreach ($this->_siteMap as &$row)
        {
            $rebuild_array[$row['pid']][] = &$row;
        }

        foreach ($this->_siteMap as & $row)
        {
            if(isset($rebuild_array[$row['id']]))
            {
                $row['childs'] = $rebuild_array[$row['id']];
            }
        }

        $this->_three = reset($rebuild_array);

        return $this;
    }

    /**
     *
     * @return \Tree
     */
    private function recurse()
    {
        $this->_clear();

        $rebuild_array = array();
        foreach ($this->_siteMap as $row)
        {
            $rebuild_array[$row['pid']][] = $row;
        }

        ksort($rebuild_array);

        $this->_three = $this->_build_recurse($rebuild_array, reset($rebuild_array));

        return $this;
    }

    /**
     *
     * @param array $array
     * @param array $parent
     * @return array
     */
    protected function _build_recurse( & $array, $parent)
    {
        foreach ($parent as $pid => $row)
        {
            if(isset($array[$row['id']]))
            {
                $row['childs'] = $this->_build_recurse($array, $array[$row['id']]);
            }

            $three[] = $row;
        }

        return $three;
    }

    protected function _clear()
    {
        $this->_three = array();
    }
}