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

class Route extends Model
{

    public $siteTree;

    public $sitePage;

    public $pageParams;

    protected $_siteMap = [];

    protected $_three = [];

    public function __construct(){

        parent::__construct();

        $main = $this->database->select('siteMap', '*', ['pid' => 0]);

        $this->_siteMap = Cache::get('_siteMap');
        if(empty($this->_siteMap)) {

            $this->_siteMap = $this->database->query("
          SELECT t1.id, t1.pid, t1.segment, t1.view, t1.layout, t1.controller, t1.action, t1.title, t1.visible
          FROM siteMap t1
          LEFT JOIN authAccess t2
           ON t1.id = t2.smapId
            AND (t2.userId = " . $this->database->quote(Registry::get('_auth')->userId) . "
            OR t2.groupId = " . $this->database->quote(Registry::get('_auth')->groupId) . ")
          WHERE t2.smapId IS NULL OR (NOT t2.smapId IS NULL AND t2.right <> '0')
              ")
                ->fetchAll();

            // ToDo add static route for controllers
            $this->addSystemPage([
                'resizer' => 'helpers',
                'error' => 'helpers',
                'files' => 'helpers',
            ]);

            Cache::set('_siteMap', $this->_siteMap);
        }

        $this->siteTree = Cache::get('siteTree');
        if(empty($this->siteTree)){
            $this->each();
            $this->siteTree = $this->get();
            Cache::set('siteTree', $this->siteTree);
        }

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
                    'controller' => $value,
                    'action' => $key,
                    'title' => '',
                    'visible' => '0',
                ]
            );
        }
    }

    /**
     *
     * @return array
     */
    private function get()
    {
        return $this->_three;
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