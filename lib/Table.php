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

abstract class Table extends Db
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

    function __construct($params=false){
        $this->params = $params;
        parent::__construct();
    }

    function isRequest(){
        return \Request::ajax() and isset($_REQUEST['draw']);
    }

    /**
     * @param $start
     * @param $lenght
     * @param $order
     * @param $filter
     * @return array
     */
    abstract function data($start, $lenght, $order, $filter);

    /**
     * @return array
     */
    abstract function column();

    /**
     * @return int
     */
    abstract function total();

    /**
     * @return int
     */
    abstract function filtered();

    /**
     *
     */
    function ajax()
    {
        header('Content-type: application/json');
        echo json_encode(
            [
                "draw" => intval( $_REQUEST['draw'] ),
                "recordsTotal" => intval( $this->total($this->params) ),
                "recordsFiltered" => intval( $this->filtered($this->params) ),
                "data" =>
                    $this->data(
                        intval($_REQUEST['start']),
                        intval($_REQUEST['length']),
                        'orderable', // ToDo вот тут нужно разбираться
                        $_REQUEST['search']['value'] // ToDo да и тут
                    )
            ]
        );
        exit(0);
    }

    /**
     * @param array|bool $params
     * @return string
     */
    function html( $params = false ){

        $name = isset($params['name'])?$params['name']:'noname';
        $lang = Translate::langName(Registry::get('_config')['site']['lang']);
        $ordering = isset($params['ordering'])?$params['ordering']:'false';
        $filter = isset($params['filter'])?$params['filter']:'false';

        Registry::js(["/assets/datatables/1.10.6/media/js/jquery.dataTables.min.js"]);
        Registry::css(["/assets/datatables/1.10.6/media/css/jquery.dataTables.min.css"]);

        $hcols = "";foreach ($this->column(1) as $col) $hcols .= "<th>$col</th>";
        $html  = "
        <table id='$name' class='display' cellspacing='0' width='100%'>\n
        <thead>
        <tr>$hcols</tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
        <tr>$hcols</tr>
        </tfoot>
        </table>";

        $html .= "<script type='text/javascript'>";
        $html .= "$(document).ready(function(){";
        $html .= "$('#$name').dataTable({";
        $html .= "'columns': ["; foreach ($this->column($params) as $col) $html .= "{ 'data': '$col' },"; $html = substr_replace($html, "],", -1);
        $html .= "'ordering': $ordering,";
        $html .= "'bFilter': $filter,";
        $html .= "'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/$lang.json'},";
        $html .= "'processing': true,";
        $html .= "'serverSide': true,";
        $html .= "'ajax': ''";
        $html .= "});});</script>\n";

        return $html;
    }

}