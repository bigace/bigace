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
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 * @subpackage guestbook
 */

/**
 * Script for editing and deleting Guestbook Entrys.
 */

check_admin_login();
admin_header();

import('classes.guestbook.Guestbook');
import('classes.guestbook.GuestbookEnumeration');
import('classes.guestbook.GuestbookAdminService');
import('classes.fright.FrightService');
import('classes.util.html.FormularHelper');

$mode = extractVar('mode', 'view');
$data = extractVar('data', array());

if ($mode == "edit")
{
    // Show formular for editing existing Guestbook entry.
    $msg = '';
    if (isset ($data['gbid'])) 
    {
        $gb = new Guestbook($data['gbid']);
        showEditEntry( 'changeEntry', 
        	array('name'    => $gb->getName(),
	              'email'   => $gb->getEmail(),
	              'hp'      => $gb->getHomepage(),
	              'date'    => $gb->getEntryDate(),
	              'comment' => $gb->getComment(),
	              'submit'  => getTranslation('gbEdit'),
	              'gbid'    => $data['gbid'],
	              'title'   => getTranslation('gb_edit_entry')
       ));
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
    * Delete confirmed Guestbook Entry
    */
    $result = '';
    $gbid = $data['gbid'];
    if (isset ($data['gbid']) && $data['gbid'] != '') {
		$gb_admin = new GuestbookAdminService();
		$result = $gb_admin->deleteEntry( $data['gbid'] );
	} else {
	    echo '<b>Could not delete Entry!</b>';
	}

    viewEntrys( extractVar('start', 0) );
} 
else if ($mode == "changeEntry")
{
    /**
    * Perform posted changes.
    */
    $result = '';
    $gbid = $data['gbid'];
    if (isset ($data['gbid']) && $data['gbid'] != '') {
		$gb_admin = new GuestbookAdminService();
		$gb_admin->changeEntry($data['gbid'], $data['name'], $data['email'], $data['hp'], $data['comment']);
	} else {
	    echo '<b>Could not change Entry!</b>';
	}
    viewEntrys( extractVar('start', 0) );
} 
else 
{
    viewEntrys(0);
}

unset ( $mode );
unset ( $data );

// ---------------------------------------------------------
//     FUNCTIONS FOLLOW
// ---------------------------------------------------------

/**
* Shows all Entrys
*/
function viewEntrys($from) 
{
    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminGuestbook.tpl.htm", false, true);

    $cssClass = "row1";
	$homepageLink = '<img src="'._BIGACE_DIR_PUBLIC_WEB . 'cid'._CID_.'/guestbook/home.gif'.'">';

	$gb_info = new GuestbookEnumeration($from);
		
    for($i = 0; $i < $gb_info->countEntrys(); $i++)
    {
    	$temp = $gb_info->getNextEntry();

		$homepage = '';
		$url = $temp->getHomepage();
		// filter historical mistakes 
		if ($url !== null && trim($url) != 'http://' && trim($url) != 'http:' && strlen(trim($url)) > 6) {
			if (stripos($url, 'http') === false || stripos($url, '://') === false) {
				$url = 'http://' . $url;
			}
			$homepage = '<a href="'.$url.'" target="_blank">'.$homepageLink.'</a>';
		}
    	
        $tpl->setCurrentBlock("row") ;
        $tpl->setVariable("GB_LINK", createAdminLink($GLOBALS['MENU']->getID()));
        $tpl->setVariable("CSS_CLASS", $cssClass);
        $tpl->setVariable("GB_ID", $temp->getID());
        $tpl->setVariable("GB_USER", $temp->getName());
        $tpl->setVariable("GB_TITLE", $temp->getComment());
        $tpl->setVariable("GB_HOMEPAGE", $homepage);
        $tpl->setVariable("GB_DATE", date("d.m.Y", $temp->getEntryDate()));
        $tpl->parseCurrentBlock("row") ;

	    $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }
    
    $tpl->show();
}


/**
* Edit new Guestbook Entry
*/
function showEditEntry($mode, $data)
{
	echo createBackLink($GLOBALS['MENU']->getID());
	
    if ( !isset($data['name']) ) 	{ $data['name'] = ''; }
    if ( !isset($data['hp']) ) 		{ $data['hp'] = 'http://'; }
    if ( !isset($data['email']) ) 	{ $data['email'] = ''; }
    if ( !isset($data['comment']) ) { $data['comment'] = ''; }
    if ( !isset($data['title']) ) 	{ $data['title'] = getTranslation('gbEdit'); }
    if ( !isset($data['gbid']) ) 	{ $data['gbid'] = ''; }
    if ( !isset($data['submit']) ) 	{ $data['submit'] = getTranslation('gbSave'); }

    $config = array(

					'align'		    =>	array (
					                        'table'     =>  'center',
					                        'left'      =>  'left'
					                    ),
					'title'			=> 	$data['title'],
					'form_action'	=>	createAdminLink($GLOBALS['MENU']->getID()),
					'form_method'	=>	'post',
					'form_hidden'	=>	array(
											'mode'			    =>	$mode,
											'data[gbid]'		=>	$data['gbid']
									),
					'entries'		=> 	array(
                                            getTranslation('gbUser')      => createTextInputType('name', $data['name'], '255'),
                                            getTranslation('gbEmail')     => createTextInputType('email', $data['email'], '255'),
                                            getTranslation('gbHomepage')  => createTextInputType('hp', $data['hp'], '255'),
                                            getTranslation('gbEntry')     => createTextArea('comment',$data['comment'],'10','40')
									),
					'form_submit'	=>	TRUE,
					'submit_label'	=>	$data['submit']
	);
	echo createTable($config);
}

admin_footer();
