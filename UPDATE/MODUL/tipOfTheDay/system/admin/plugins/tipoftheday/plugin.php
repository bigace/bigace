<?php
/**
* Script for editing and deleting TipOfTheDay Entrys.
*
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
* @package bigace.administration
*/

check_admin_login();
admin_header();

import('classes.tipoftheday.TipOfTheDay');
import('classes.tipoftheday.TipOfTheDayService');
import('classes.tipoftheday.TipOfTheDayEnumeration');
import('classes.tipoftheday.TipOfTheDayAdminService');
import('classes.fright.FrightService');
import('classes.util.html.FormularHelper');

$mode = extractVar('mode', 'view');
$data = extractVar('data', array());

if ($mode == "edit")
{
    /**
    * Show formular for editing existing TipOfTheDay entry.
    */
    $msg = '';
    if (isset ($data['id'])) 
    {
        $gb = new TipOfTheDay($data['id']);
        showEditEntry('changeEntry', $gb->getID(), $gb->getName(), $gb->getLink(), $gb->getTip(), getTranslation('totd_edit_entry'), getTranslation('totd_Edit'));
    } 
    else {
        $msg .= '<b>Could not edit Entry, ID missing</b>';
    }
    echo $msg;
    unset ($msg);
    unset ($gb);
}
else if ($mode == "delete")
{
    /**
    * Show Formular and ask User if entry should really be deleted.
    */
    if (isset ($data['id'])) 
    {
        $gb = new TipOfTheDay($data['id']);
        showEditEntry('deleteEntry', $gb->getID(), $gb->getName(), $gb->getLink(), $gb->getTip(), getTranslation('totd_delete_entry'), getTranslation('totd_Delete'));
    } 
    else {
        echo '<b>Could not edit Entry, ID missing</b>';
    }
} 
else if ($mode == "deleteEntry")
{
    /**
    * Delete confirmed TipOfTheDay Entry
    */
    $result = '';
    $gbid = $data['id'];
    if (isset ($data['id']) && $data['id'] != '') {
		$gb_admin = new TipOfTheDayAdminService();
		$result = $gb_admin->deleteEntry( $data['id'] );
	} else {
	    echo '<b>Could not delete Entry!</b>';
	}

    viewEntrys( extractVar('start', '0') );
} 
else if ($mode == "create")
{
    /**
    * Create a new TipOfTheDay Entry
    */
    $result = '';
    if (isset ($data['name']) && $data['tip']) {
    	if(!isset($data['namespace']) || trim($data['namespace']) == '')
    		$data['namespace'] = 'default';
		$gb_admin = new TipOfTheDayAdminService();
		$result = $gb_admin->createEntry($data['namespace'],$data['name'],$data['link'],$data['tip']);
	} else {
	    echo '<b>Could not create Entry, values missing!</b>';
	}

    viewEntrys( extractVar('start', '0') );
} 
else if ($mode == "changeEntry")
{
    /**
    * Perform posted changes.
    */
    $result = '';
    $gbid = $data['id'];
    if (isset ($data['id']) && $data['id'] != '') {
    	if(!isset($data['namespace']) || trim($data['namespace']) == '')
    		$data['namespace'] = 'default';
		$gb_admin = new TipOfTheDayAdminService();
		$gb_admin->changeEntry($data['id'],$data['namespace'],$data['name'],$data['link'],$data['tip']);
	} else {
	    echo '<b>Could not change Entry!</b>';
	}
    viewEntrys( extractVar('start', '0') );
} 
else 
{
    viewEntrys( '0' );
}

unset ( $mode );
unset ( $data );

// ------------------------------------------------------
//       FUNCTIONS FOLLOW
// ------------------------------------------------------


/**
* Shows all Entrys
*/
function viewEntrys($from) 
{
	
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminTipOfTheDay.tpl.htm", false, true);

    $cssClass = "row1";
    
    // Display Create Tip Form
    $tpl->setCurrentBlock("createTip") ;
    $tpl->setVariable("CREATE_LINK", createAdminLink($GLOBALS['MENU']->getID()));
    $tpl->setVariable("CSS_CLASS", $cssClass);
    $tpl->setVariable("TITLE", createTextInputType('name', '', '200', false));
    $tpl->setVariable("LINK", createTextInputType('link', '', '200', false));
    $tpl->setVariable("TIP", createTextArea('tip', '', 3, 30));
    $tpl->parseCurrentBlock("row") ;

	$gb_info = new TipOfTheDayEnumeration($from, '-1');
		
    for($i = 0; $i < $gb_info->count(); $i++)
    {
    	$temp = $gb_info->next();
    	
        $tpl->setCurrentBlock("row") ;
        $tpl->setVariable("EDIT_LINK", createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $temp->getID(), 'mode' => 'edit')));
        $tpl->setVariable("DELETE_LINK", createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $temp->getID(), 'mode' => 'delete')));
        $tpl->setVariable("CSS_CLASS", $cssClass);
        $tpl->setVariable("ID", $temp->getID());
        $tpl->setVariable("TITLE", $temp->getName());
        $tpl->setVariable("LINK", $temp->getLink());
        $tpl->setVariable("TIP", nl2br($temp->getTip()));
        $tpl->parseCurrentBlock("row") ;

	    $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }
    
    $tpl->show();
}


/**
* Edit new TipOfTheDay Entry
*/
function showEditEntry($mode, $id, $name, $link, $tip, $title, $submit)
{
	echo createBackLink($GLOBALS['MENU']->getID());
	
    $config = array(
					'width'		    =>	'350',
					'align'		    =>	array (
					                        'table'     =>  'center',
					                        'left'      =>  'left'
					                    ),
					'image'		    => 	$GLOBALS['_BIGACE']['style']['DIR'].($mode == 'deleteEntry' ? 'delete.png' : 'edit.png'),
					'title'			=> 	$title,
					'form_action'	=>	createAdminLink($GLOBALS['MENU']->getID()),
					'form_method'	=>	'post',
					'form_hidden'	=>	array(
											'mode'		=>	$mode,
											'data[id]'	=>	$id
									),
					'entries'		=> 	array(
                                            getTranslation('totd_name')     => createTextInputType('name', $name, '200'),
                                            getTranslation('totd_link')     => createTextInputType('link', $link, '200'),
                                            getTranslation('totd_tip')      => createTextArea('tip', $tip)
									),
					'form_submit'	=>	TRUE,
					'submit_label'	=>	$submit
	);
	echo createTable($config);
}


admin_footer();