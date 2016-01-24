<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.smarty
 * @subpackage function
 */
  
/**
 * Loads the desired template.
 */
function smarty_resource_bigace_source($tpl_name, &$tpl_source, &$smarty_obj)
{
	$values = array('NAME' => $tpl_name);
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_source');
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString,$values,true);
	$tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	if($tpl->count() > 0) {
		$tpl = $tpl->next();
    	$tpl_source = $tpl['content'];
      return true;
  	}
	return false;
}

/**
 * Fetch the last timestamp when the template changed.
 */
function smarty_resource_bigace_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
	$values = array('NAME' => $tpl_name);
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_timestamp');
	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString,$values,true);
	$ts = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	if($ts->count() > 0) {
		$ts = $ts->next();
    	$tpl_timestamp = $ts['timestamp'];
      return true;
  	}
	return false;
}

/**
 * All templates are secure currently.
 */
function smarty_resource_bigace_secure($tpl_name, &$smarty_obj)	{ return true; }
    
/**
 * We trust every include.
 */
function smarty_resource_bigace_trusted($tpl_name, &$smarty_obj) { return true; }
