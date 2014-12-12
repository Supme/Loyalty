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

abstract class Table extends Model
{

    /**
     * model object mast have function:
     *  @data array
     *  @total int
     *  @filtered int
     *  @start int
     *  @lenght int
     *  @order array
     */

    public $params;

    function __construct($params){
        $this->params = $params;
        parent::__construct();
    }

    function isRequest(){
        return isset($_REQUEST['draw']);
    }

    /**
     * @param $params
     * @param $start
     * @param $lenght
     * @param $order
     * @param $filter
     * @return array
     */
    abstract function data($params, $start, $lenght, $order, $filter);

    /**
     * @param $params
     * @return array
     */
    abstract function column($params);

    /**
     * @param $params
     * @return int
     */
    abstract function total($params);

    /**
     * @param $params
     * @return int
     */
    abstract function filtered($params);

    function ajax(){
        {
            header('Content-type: application/json');
            echo json_encode(
                [
                    "draw" => intval( $_REQUEST['draw'] ),
                    "recordsTotal" => intval( $this->total($this->params) ),
                    "recordsFiltered" => intval( $this->filtered($this->params) ),
                    "data" =>
                        $this->data(
                            $this->params,
                            intval($_REQUEST['start']),
                            intval($_REQUEST['length']),
                            'orderable', // ToDo вот тут нужно разбираться
                            $_REQUEST['search']['value'] // ToDo да и тут
                        )
                ]
            );
        }
    }

    function html( $params = false ){

        $name = isset($params['name'])?$params['name']:'noname';
        $lang = isset($params['lang'])?$params['lang']:'Russian'; // ToDo брать из конфига
        $ordering = isset($params['ordering'])?$params['ordering']:'false';
        $filter = isset($params['filter'])?$params['filter']:'false';

        Registry::js(["//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"]);
        Registry::css(["//cdn.datatables.net/1.10.4/css/jquery.dataTables.css"]);

        $html  = "<script type='text/javascript'>";
        $html .= "$(document).ready(function(){";
        $html .= "$('#$name').dataTable({";
        //$html .= "'columns': ["; foreach ($columns as $col) $html .= "{ 'data': '$col' },"; $html = substr_replace($html, "],", -1);
        $html .= "'ordering': $ordering,";
        $html .= "'bFilter': $filter,";
        $html .= "'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/$lang.json'},";
        $html .= "'processing': true,";
        $html .= "'serverSide': true,";
        $html .= "'ajax': ''";
        $html .= "});});</script>\n";

        $hcols = "";foreach ($this->column(2) as $col) $hcols .= "<th>$col</th>";
        $html .= "<table id='$name' class='display' cellspacing='0' width='100%'><thead><tr>$hcols</tr></thead><tbody></tbody><tfoot><tr>$hcols</tr></tfoot></table>";

        return $html;
    }

}