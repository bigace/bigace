<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sort} function plugin
 *
 * Type:     function<br>
 * Name:     sort<br>
 * Purpose:  sort an array
 *
 * @param array
 * @param Smarty
 */
function smarty_function_sort($params, &$smarty)
{
    if (!isset($params['var'])) {
        $smarty->trigger_error("sort: missing 'var' parameter");
        return;
    }

    if(!is_array($params['var'])) {
        return;
    }
    
    asort($params['var']);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_contents);
    } else {
        return $_contents;
    }
}

