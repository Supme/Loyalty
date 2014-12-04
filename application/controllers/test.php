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

class Test extends Controller
{
    function index()
    {
        Registry::css([
            '/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/css/AdminLTE.css',
            '/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.css',
        ]);
        Registry::css('/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/css/bootstrap-slider/slider.css');

        Registry::js('/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/js/AdminLTE/app.js');
        Registry::js([
            '/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/js/AdminLTE/dashboard.js',
            '/home/aagafonov/PhpstormProjects/ly/vendor/almasaeed2010/adminlte/js/AdminLTE/demo.js',
        ]);

        echo Registry::get('_css').Registry::get('_js');
    }
}
