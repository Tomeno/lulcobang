<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {actualurl} function plugin
 *
 */

function smarty_function_actualurl($params, &$smarty)
{
    return Link::getActual();
}

/* vim: set expandtab: */

?>