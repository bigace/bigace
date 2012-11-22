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
 */

/**
 * Administrate the group settings, de-/activate each Functional right for a selected User Group!
 * User who try to access have to own the Functional right: EDIT_GROUP_FRIGHTS
 *
 * mode 8 = Delete or deactivate a Group Fright
 * mode 9 = Add or activate a Group Fright
 */

check_admin_login();
admin_header();

import('classes.util.html.FormularHelper');
import('classes.fright.Fright');
import('classes.fright.FrightStringsEnumeration');
import('classes.fright.FrightImporter');
import('classes.fright.FrightExporter');
import('classes.fright.FrightService');
import('classes.fright.FrightAdminService');
import('classes.fright.GroupFrightEnumeration');
import('classes.group.Group');
import('classes.group.GroupEnumeration');
import('classes.exception.WrongArgumentException');
require_once(_BIGACE_DIR_LIBS.'sanitize.inc.php');

$data = extractVar('data', array());
$MODE = extractVar('mode', '');

if (!isset($data["gid"])){
	$data["gid"] = '';
}
$id = $data["gid"];


// =============================================================================
// =============================================================================

    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminGroupPermission.tpl.htm", false, true);

    switch ($MODE)
    {
        case '6':
            saveFright($data);
            break;
        case '8':
            deleteGroupFright($data);
            break;
        case '9':
            addGroupFright($data);
            break;
        case 'importFile':
            importFile($data);
            break;
        case 'exportFile':
            exportFile($data);
            break;
    }

    if($MODE == 'exportXml') {
        $exporter = new FrightExporter();
        $tpl->setVariable('EXPORT_XML', '<textarea rows="20" cols="100">' . $exporter->getDump() . '</textarea>');
    }
    else {
        $tpl->setVariable('EXPORT_XML', '');
    }

    $group = null;
    if ($id != "") {
        $group = new Group($id);
    }
    // -------------------------------------------------------
    

    $entries = array();
    $GroupEnum = new GroupEnumeration();
    for ($i=0; $i < $GroupEnum->count(); $i++)
    {
        $temp = $GroupEnum->next();
        if($group == null)
            $group = $temp;
        $entries[$temp->getName()] = $temp->getID();
    }
    unset ($temp);

    $tpl->setVariable('GROUP_SELECT', createSelectBox('gid', $entries, $id, 'this.form.submit();'));
    $tpl->setVariable('ACTION_CHOOSE_GROUP', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => '2')));    

    if($group != null)
    {
    	// TODO add caching after changed function structure???
        //$GLOBALS['FRIGHT_SERVICE']->preloadGroupFrights($group->getID());

        $frightsList = new FrightStringsEnumeration();
        $c = $frightsList->count();

        $cssClass = "row1";

        for ($i=0; $i<$c; $i++)
        {
          $fright = $frightsList->next();
          $rowName = "deactiverow";
          $action  = createAdminLink(
            $GLOBALS['MENU']->getID(), 
            get_csrf_token(array('mode' => '9', 'data[gid]' => $group->getID(), 'data[fright]' => $fright->getID()))
          );

          if ($GLOBALS['FRIGHT_SERVICE']->hasGroupFright($group->getID(), $fright->getName())) {
            $rowName = "activerow";
            $action = createAdminLink(
                $GLOBALS['MENU']->getID(), 
                get_csrf_token(array('mode' => '8', 'data[gid]' => $group->getID(), 'data[fright]' => $fright->getID()))
            );
          }

          $tpl->setCurrentBlock($rowName) ;
          $tpl->setVariable("ACTION_CHANGE_FRIGHT", $action) ;
          $tpl->setVariable("CSS", $cssClass) ;
          $tpl->setVariable("FRIGHT_NAME", $fright->getName()) ;
          $tpl->setVariable("FRIGHT_DESCRIPTION", $fright->getDescription()) ;
          $tpl->parseCurrentBlock($rowName) ;

    	  $cssClass = ($cssClass == "row1") ? "row2" : "row1";

          $tpl->setCurrentBlock("outerrow") ;
          $tpl->parseCurrentBlock("outerrow") ;
    	}
    }

    $tpl->setVariable('EXPORT', createAdminLink($GLOBALS['MENU']->getID(), array('mode' => '2')));    

  	$tpl->show();


// =============================================================================
// =============================================================================

function exportFile($data) 
{
    $exporter = new FrightExporter();
  	$filename = 'fright_export_' . time() . '.' . _IMPORT_FILE_EXTENSION;
    $fullfile = $exporter->saveDump($filename);
    displayMessage( getTranslation('exported_file') . '<br>' . $fullfile);
}

function importFile($data) 
{
        $file = $_FILES['XMLFILE'];
        if (isset($file['name']) && isset($file['tmp_name'])) 
        {
            import('classes.util.IOHelper');
            IOHelper::createDirectory(_BIGACE_IMPORT_DIRECTORY);

            $temp_name = _BIGACE_IMPORT_DIRECTORY  . time() . '_' . $file['name'];
		    $result_upload = move_uploaded_file($file['tmp_name'], $temp_name);
            
            if (file_exists($temp_name)) 
            {
                // TODO translate
                $importer = new FrightImporter();
                if($importer->importFile($temp_name))
                    displayMessage(getTranslation('imported_file') . ' ' . $temp_name);
                else
                    displayError('Problems when importing File!');
                return;
            }
        }
    displayError('Failed executing Import, uploaded File?');
}

function deleteGroupFright($data)
{
    if (isset($data['fright']) && isset($data['gid']) && check_csrf_token())
    {
        $FRIGHT_ADMIN = new FrightAdminService();
        $FRIGHT_ADMIN->deleteGroupFright( $data['gid'], $data['fright'] );
    }
    else
    {
	    ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not deactivate permission.') );
    }
}

function addGroupFright($data)
{
    if (isset($data['fright']) && isset($data['gid']) && check_csrf_token())
    {
        $FRIGHT_ADMIN = new FrightAdminService();
        $FRIGHT_ADMIN->createGroupFright( $data['gid'], $data['fright'] );
    }
    else
    {
	    ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Could not activate permission.') );
    }
}

function saveFright($data)
{
    if (isset($data['name']) && trim($data['name']) != '')
    {
    	$name = sanitize_plain_text($data['name']);
    	$desc = sanitize_plain_text($data['description']);
    	
        $FRIGHT_ADMIN = new FrightAdminService();
        $id = $FRIGHT_ADMIN->createFright($name, $desc);
        if (is_bool($id) && $id === FALSE) {
            displayError( getTranslation('create_failure_title') . '<br/>' . getTranslation('create_failure_description') );
        }
        else {
            displayMessage( getTranslation('create_success_title') );
        }
    }
    else
    {
	    ExceptionHandler::processAdminException( new WrongArgumentException(400, getTranslation('fright_not_created_desc'), createAdminLink($GLOBALS['MENU']->getID(), prepareArrayLink('data',$data))) );
    }
}

admin_footer();
