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
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation, 
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 * @subpackage logging
 */

/**
 * Shows available Log Messages, lets you browse them, see details and
 * purge selected.
 */

check_admin_login();
loadLanguageFile('logging', ADMIN_LANGUAGE);
admin_header();

if(!is_class_of($GLOBALS['LOGGER'],'DBLogger'))
{
    displayError( getTranslation('wrong_logger') );
    return;
}

if(isset($_POST['action']) && $_POST['action'] == 'deleteAll')
{
    $sql = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('logging_delete_all');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, array());
    $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    unset($sql);
}

if(isset($_GET['action']) && trim($_GET['action']) == 'delete')
{
    $deleteIDs = array();

    if(isset($_GET['deleteID']))
        $deleteIDs[] = $_GET['deleteID'];

    if(isset($_POST['deleteID']) && is_array($_POST['deleteID']))
        $deleteIDs = array_merge($deleteIDs, $_POST['deleteID']);

    if(count($deleteIDs) > 0) {
        $deleteSQL = '';
        for($i=0; $i<count($deleteIDs);$i++) {
            $deleteSQL .= $GLOBALS['_BIGACE']['SQL_HELPER']->quoteAndEscape($deleteIDs[$i]);
            if($i<count($deleteIDs)-1)
                $deleteSQL .= ',';
        }
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement("DELETE FROM {DB_PREFIX}logging WHERE `cid`= {CID} AND `id` IN ({ID})", array('ID' => $deleteSQL));
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
}

$showListing = true;

if(isset($_GET['view']))
{
    $values = array(
                    'ORDER_BY'          => '',
                    'LIMIT'             => '',
                    'WHERE_EXTENSION'   => " AND id='".$_GET['view']."'"
    );
    
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('logging_filter');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $entry = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

    if($entry->count() > 0) 
    {
        $entry = $entry->next();

        $smarty = getAdminSmarty();
        $smarty->assign("BACK_LINK", createBackLink($GLOBALS['MENU']->getID()));
        $smarty->assign("ID", $entry['id']);
        $smarty->assign("CID", $entry['cid']);
        $smarty->assign("USER_ID", $entry['userid']);
        $smarty->assign("LEVEL", $GLOBALS['LOGGER']->getDescriptionForMode($entry['level']));
        $smarty->assign("TIMESTAMP", date('d.m.Y H:i:s',$entry['timestamp']));
        $smarty->assign("NAMESPACE", ($entry['namespace'] == '' ? 'System' : $entry['namespace']));
        $smarty->assign("FILE", $entry['file']);
        $smarty->assign("LINE", $entry['line']);
        $smarty->assign("STACKTRACE", $entry['stacktrace']);
        $smarty->assign("MESSAGE", $entry['message']);
        $smarty->display('LoggingDetails.tpl');    
        
        $showListing = false;
    }
}

if($showListing)
{

    $amount    = (isset($_POST['amount'])    ? $_POST['amount'] :    (isset($_GET['amount'])    ? $_GET['amount']    : '10'));
    $start     = (isset($_POST['start'])     ? $_POST['start'] :     (isset($_GET['start'])     ? $_GET['start']     : '0'));
    $level     = (isset($_POST['level']) 	 ? $_POST['level'] :     (isset($_GET['level'])     ? $_GET['level']     : ''));
    $namespace = (isset($_POST['namespace']) ? $_POST['namespace'] : (isset($_GET['namespace']) ? $_GET['namespace'] : ''));
    
    $where = '';
    if($level != '') {
        $where .= " AND level='".$level."'";
    }
    
    if($namespace != '') {
        $where .= " AND namespace='".$namespace."'";
    }
    
    $values = array(
                    'ORDER_BY'          => 'ORDER BY timestamp DESC',
                    'LIMIT'             => 'LIMIT '.$start.','.$amount,
                    'WHERE_EXTENSION'   => $where
    );
    
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('logging_count');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $total = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    $total = $total->next();
    $total = $total['amount'];

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("LoggingList.tpl.htm", true, true);
    $tpl->setVariable("BULK_DELETE", createAdminLink($MENU->getID(), array('action' => 'delete', 'amount' => $amount, 'level' => $level)));
    $tpl->setVariable("TOTAL", $total);

    $entrys  = '<select name="amount">';
    for($a=1; $a<11; $a++) 
    {
        $checked = ($amount == $a*10 ? 'selected ' : '');
        $entrys .= '<option '.$checked.' value="'.($a*10).'">'.($a*10).'</option>';
    }
    $entrys .= '</select>';

    $allLevel = $GLOBALS['LOGGER']->getErrorLevel();
    $levelSelect  = '<select name="level"><option value=""'.($level == '' ? ' selected' : '').'></option>';
    foreach($allLevel AS $levelValue => $levelName) 
    {
        $checked = ($levelValue == $level ? 'selected ' : '');
        $levelSelect .= '<option '.$checked.' value="'.$levelValue.'">'.$levelName.'</option>';
    }
    $levelSelect .= '</select>';

    $namespaceSelect  = '<select name="namespace"><option value=""'.($namespace == '' ? ' selected' : '').'></option>';
    $namespaces = array(LOGGER_NAMESPACE_AUDIT,LOGGER_NAMESPACE_SYSTEM,LOGGER_NAMESPACE_AUTHENTICATION,LOGGER_NAMESPACE_SEARCH);
    foreach($namespaces AS $levelValue) 
    {
        $checked = ($levelValue == $namespace ? 'selected ' : '');
        $namespaceSelect .= '<option '.$checked.' value="'.$levelValue.'">'.$levelValue.'</option>';
    }
    $namespaceSelect .= '</select>';

    $tpl->setVariable("ENTRYS_PER_PAGE", $entrys);
    $tpl->setVariable("START_ID", $start);
    $tpl->setVariable("LEVEL_SELECT", $levelSelect);
    $tpl->setVariable("NAMESPACE_SELECT", $namespaceSelect);

    // calculate pages
    for($i=0; $i < $total/$amount; $i++)
    {
        $pageStartValue = $i*$amount;
        $tpl->setCurrentBlock("pageSelect");
        $tpl->setVariable("PAGE_ID", $i+1);
        $tpl->setVariable("PAGE_URL", "switchPage('".$pageStartValue."')");
        //$tpl->setVariable("PAGE_URL", createAdminLink($MENU->getID(), array('amount' => $amount, 'start' => $pageStartValue, 'level' => $level)));
        $tpl->parseCurrentBlock("pageSelect");
    }

    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('logging_filter');
    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    $messages = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

    $cssClass = 'row1';

    for($i=0; $i < $messages->count(); $i++)
    {
        $temp = $messages->next();
    
        $tpl->setCurrentBlock("row");
        $tpl->setVariable("BULK_PARAMETER", 'deleteID[]');
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("ID", $temp['id']);
        $tpl->setVariable("CID", $temp['cid']);
        $tpl->setVariable("USER_ID", $temp['userid']);
        $tpl->setVariable("LEVEL", $GLOBALS['LOGGER']->getDescriptionForMode($temp['level']));
        $tpl->setVariable("TIMESTAMP", date('d.m.Y\<\b\r\>H:i:s',$temp['timestamp']));
        $tpl->setVariable("NAMESPACE", ($temp['namespace'] == '' ? 'system' : $temp['namespace']));
        $tpl->setVariable("FILE", $temp['file']);
        $tpl->setVariable("LINE", $temp['line']);
        $tpl->setVariable("STACKTRACE", $temp['stacktrace']);
		$msg = strip_tags($temp['message']);
		if(strlen($msg) > 60)
			$msg = substr($msg, 0, 60) . ' ...';
        $tpl->setVariable("MESSAGE", $msg);
        $tpl->setVariable("INFO_URL", createAdminLink($MENU->getID(), array('view' => $temp['id'])));
        $tpl->setVariable("DELETE_URL", createAdminLink($MENU->getID(), array('action' => 'delete', 'level' => $level, 'amount' => $amount, 'start' => $start, 'deleteID' => $temp['id'])));
        $tpl->parseCurrentBlock("row");
    
        $cssClass = ($cssClass == 'row1') ? 'row2' : 'row1';
    }

    $tpl->show();
}

admin_footer();

?>