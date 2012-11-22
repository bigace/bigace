<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This Command is used for opening one of the Editors and its Dialogs.
 * It loads the configured Default Editor until nothing else is mentioned.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

import('classes.language.Language');
import('classes.item.Item');
import('classes.item.ItemService');
import('classes.menu.MenuService');
import('classes.administration.EditorContext');
import('classes.util.LinkHelper');
import('classes.util.CMSLink');
import('classes.util.ApplicationLinks');
import('classes.smarty.BigaceSmarty');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');
import('classes.util.links.AjaxItemContentLink');
import('classes.util.links.LoginFormularLink');
import('classes.menu.ContentService');

// load required translations
loadLanguageFile('editor',_ULC_);
loadLanguageFile('bigace',_ULC_);

// the editor that is going to be used
define('EDITOR_TYPE', 'type');
// the mode the editor should execute (show editor, show image dialog...)
define('EDITOR_MODE', 'mode');
// the default mode for the editor
define('DEFAULT_MODE', '1');
// the url parameter where the content is submitted
define('CONTENT_PARAMETER', 'editorContent');
//  define all available editors
define('EDITOR_PLAINTEXT', 'plaintext');
define('EDITOR_FCKEDITOR', 'fckeditor');
// defines the default editor if none was choosen
define('DEFAULT_EDITOR', ConfigurationReader::getConfigurationValue('editor', 'default.editor', EDITOR_PLAINTEXT));

// load admin style for stylesheet ...
require_once(_BIGACE_DIR_ADMIN.'styling.php');

// fetch the editor mode
$mode = extractVar(EDITOR_MODE, DEFAULT_MODE);

// fetch the editor type
$type = $GLOBALS['_BIGACE']['PARSER']->getAction();

// if no editor was passed, use the default one
if ($type == null || strlen(trim($type)) == 0) {
    $type = extractVar(EDITOR_TYPE, DEFAULT_EDITOR);
}

// TODO sanitize $type

// if the editor does not exist, switch back to default editor
if (!file_exists(_BIGACE_DIR_EDITOR . $type . '/' . $type . '.php')) {
    $type = DEFAULT_EDITOR;
}

// the editor plugin directory
$EDITOR_DIRECTORY = _BIGACE_DIR_EDITOR . $type . '/';

// the available editor modes
$EDITOR_MODE = array(
        'EDITOR'       => DEFAULT_MODE,
        'SAVE'         => 'saveContent',
        'READONLY'     => 'readOnly',
        'CLOSE'        => 'close',
        'LOAD'         => 'load',
        'BLANK'        => 'empty',
);

if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
	if ($mode == $EDITOR_MODE['SAVE']) {
		$tl = new LoginFormularLink();
		$L = new Language($GLOBALS['_BIGACE']['PARSER']->getLanguage());
		dumpFeedback(getTranslation('editor_session_timeout') . ' <a target=\"_blank\" href=\"'.LinkHelper::getUrlFromCMSLink($tl).'\">'.getTranslation('link_session_timeout').'</a>', $L->getCharset(), true, false);
	}
	else {
		// if user is not logged in, show error message and login formular with redirection to the editor
		import('classes.exception.ExceptionHandler');
		import('classes.exception.CoreException');
	    ExceptionHandler::processCoreException( new CoreException('anonymous', 'User is not logged in, missing rights!') );
	}
	exit;
}
else
{
    $EDITOR_CONTEXT = new EditorContext($type, $GLOBALS['_BIGACE']['SESSION']->getUserID());

    // ------ Get all values from the Editor Context ------
    $MENU       = $EDITOR_CONTEXT->getMenu();
    $title      = $MENU->getName();
    $content    = $MENU->getContent();
    $LANGUAGE   = $EDITOR_CONTEXT->getLanguage();
    $langid     = $EDITOR_CONTEXT->getLanguageID();
    $closeAfterwards = (isset($_POST['sendClose']) && $_POST['sendClose'] == 'true') ? true : false;
    // ----------------------------------------------------

	if (!$EDITOR_CONTEXT->canAccessEditor())
	{
		import('classes.exception.ExceptionHandler');
		import('classes.exception.CoreException');
	    ExceptionHandler::processCoreException( new CoreException(403, 'User tried to access the editor without permission.') );
	    exit;
	}
	else
	{
	    // configuration for cross-editor dialogs
	    require_once(_BIGACE_DIR_EDITOR . 'editor_properties.php');

    	if ($mode == $EDITOR_MODE['READONLY'])
        {
            $editorSmarty = getEditorSmarty();
            $editorSmarty->assign('stylesheet', $GLOBALS['_BIGACE']['style']['class']->getCSS());
            $editorSmarty->assign('charset', $LANGUAGE->getCharset());
            $editorSmarty->assign('content', $content);
            $editorSmarty->assign('title', $title);
            $editorSmarty->display('readonly.tpl');
        }
        else if ($mode == 'empty')
        {
            echo '<html><head></head><body></body></html>';
        }
        else if ($mode == $EDITOR_MODE['CLOSE'])
        {
            $editorSmarty = getEditorSmarty();
            $editorSmarty->assign('stylesheet', $GLOBALS['_BIGACE']['style']['class']->getCSS());
            $editorSmarty->assign('charset', $LANGUAGE->getCharset());
            $editorSmarty->display('close.tpl');
        }
        else if ($mode == $EDITOR_MODE['LOAD'])
        {
            echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset='.$LANGUAGE->getCharset().'"></head><body>'.$content.'</body></html>';
        }
        else if ($mode == 'translatePreview')
        {
			$css = '';
			if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
			    import('classes.smarty.SmartyDesign');
			    $SMARTY_DESIGN = new SmartyDesign($MENU->getLayoutName());
			    $SMARTY_STYLESHEET = $SMARTY_DESIGN->getStylesheet();
			    $EDITOR_STYLESHEET = $SMARTY_STYLESHEET->getEditorStylesheet();
			    $css = $EDITOR_STYLESHEET->getURL();
			}
			else {
			    import('classes.layout.Layout');
				$layout = new Layout($MENU->getLayoutName());
			    $css = $layout->getSetting('CSS', '');
			}
			$editorSmarty = getPreparedSmarty($GLOBALS['EDITOR_CONTEXT']);
            $editorSmarty->assign('stylesheet', $css);

			$cntName = (isset($_GET['cntName']) ? $_GET['cntName'] : CONTENT_PARAMETER);
			$cnts = getContentToEdit($MENU);
			foreach($cnts AS $addCnt) {
				if($addCnt['param'] == $cntName) {
            		$editorSmarty->assign('content', $addCnt['content']);
					break;
				}
			}
		    $editorSmarty->display('translate.tpl');
        }
        else if ($mode == $EDITOR_MODE['SAVE'])
        {
            // SAVE EDITED CONTENT
            if ($EDITOR_CONTEXT->hasMenuWriteRights())
            {
                import('classes.administration.EditorSaveHelper');
                //$GLOBALS['LOGGER']->logInfo($c); // for debugging log saved content...
                if(isset($_POST[CONTENT_PARAMETER]))
				{
					// find out if there are additional configured project values to save
					$addditional = getContentNames($MENU);
					$toSave = array();
					if(!is_null($addditional) && is_array($addditional) && count($addditional) > 0) {
						foreach($addditional As $toSearch) {
							if(isset($_POST[$toSearch])) {
								$toSave[$toSearch] = $_POST[$toSearch];
							}
						}
					}
					// save everything
                    $saver = new EditorSaveHelper($EDITOR_CONTEXT);
                    $saver->saveContent($_POST[CONTENT_PARAMETER], $toSave);
                    dumpFeedback($saver->getMessage(), $LANGUAGE->getCharset(), false, $closeAfterwards);
                }
				else {
                     dumpFeedback('Failed: Request was uncomplete!', $LANGUAGE->getCharset(), true);
                }
            }
            else {
                 dumpFeedback(getTranslation('error_no_write_rights'), $LANGUAGE->getCharset(), true, $closeAfterwards);
            }
        }
        else
        {
            if(isReadOnly($EDITOR_CONTEXT)) {
                $editorSmarty = getPreparedSmarty($GLOBALS['EDITOR_CONTEXT']);
                showEditorHeader($editorSmarty);
                showEditorFooter($editorSmarty);
            }
            else {
        	    include_once($EDITOR_DIRECTORY . $type . '.php');
            }
        }
	}
}

// display the footer below the editor formular
function showEditorFooter($editorSmarty = '') {
	if($editorSmarty == '')
	    $editorSmarty = getPreparedSmarty($GLOBALS['EDITOR_CONTEXT']);
    $editorSmarty->display('editorframe_footer.tpl');
}

// display the header above the editor formular
function showEditorHeader($editorSmarty = '') {
	if($editorSmarty == '')
	    $editorSmarty = getPreparedSmarty($GLOBALS['EDITOR_CONTEXT']);
    $editorSmarty->display('editorframe_header.tpl');
}

// check, if we display a READ-ONLY message and content or the editor itself
function isReadOnly($ec = null) {
    if($ec == null)
        $ec = $GLOBALS['EDITOR_CONTEXT'];

    $MENU = $ec->getMenu();
    $wfs = new WorkflowService(_BIGACE_ITEM_MENU);
    if($ec->isWorkflowVersion() && !$wfs->isPermittedToEdit($GLOBALS['_BIGACE']['SESSION']->getUserID(), $MENU->getID(), $MENU->getLanguageID()))
    {
        return true;
    }
    return false;
}

// get an prepared instace of the smarty engine, to display a valid editor template
function getPreparedSmarty($EDITOR_CONTEXT) {

    $MENU       = $EDITOR_CONTEXT->getMenu();
    $title      = $MENU->getName();
    $content    = $MENU->getContent();
    $LANGUAGE   = $EDITOR_CONTEXT->getLanguage();

	$IS = new ItemService( _BIGACE_ITEM_MENU );
	$ile = $IS->getItemLanguageEnumeration($MENU->getID());
	$langs = array();
	for ($i=0; $i < $ile->count(); $i++) {
    	$langs[] = $ile->next();
	}

	$inWorkflowNoSave = true;
	$readOnly = false;
	if(isReadOnly($EDITOR_CONTEXT)) {
	    $readOnly = true;
	    $inWorkflowNoSave = false;
	}

	$link = new MenuChooserLink();
	$link->setItemID(_BIGACE_TOP_LEVEL);

	$link2 = new AjaxItemContentLink();
	$link2->setItemID('"+itemid+"');
	$link2->setLanguageID('"+languageid+"');

	$editorSmarty = getEditorSmarty();

	$editorSmarty->assign('stylesheet', $GLOBALS['_BIGACE']['style']['class']->getCSS());
	$editorSmarty->assign('styleDir', $GLOBALS['_BIGACE']['style']['DIR']);
	$editorSmarty->assign('charset', $LANGUAGE->getCharset());
	$editorSmarty->assign('contentParam', CONTENT_PARAMETER);
	$editorSmarty->assign('content', $content);
	$editorSmarty->assign('title', $title);
	$editorSmarty->assign('readOnly', $readOnly);
	$editorSmarty->assign('jsname', prepareJSName($MENU->getName()));
	$editorSmarty->assign('MENU', $MENU);
	$editorSmarty->assign('editorType', $GLOBALS['type']);
	$editorSmarty->assign('inWorkflowNoSave', $inWorkflowNoSave);
	$editorSmarty->assign('menuChooserUrl', LinkHelper::getUrlFromCMSLink($link));
	$editorSmarty->assign('ajaxContentUrl', LinkHelper::getUrlFromCMSLink($link2, array('itemtype' => _BIGACE_ITEM_MENU)));
	$editorSmarty->assign('ajaxItemUrl', ApplicationLinks::getAjaxItemInfoURL(_BIGACE_ITEM_MENU, '"+itemid+"', '"+languageid+"'));
	$editorSmarty->assign('editorUrl', createEditorLink($GLOBALS['type'], "'+editorHelper.getMenuID()+'", "'+editorHelper.getLanguageID()+'", "'+mode+'"));
	$editorSmarty->assign('editFrameworkUrl', createEditorLink($GLOBALS['type'], "'+menuid+'", "'+menulanguage+'", ""));
	$editorSmarty->assign('translateUrl', createEditorLink($GLOBALS['type'], "'+menuid+'", "'+toLanguage+'", "translate&translate='+fromLanguage+'"));
	$editorSmarty->assign('readonlyUrl', createEditorLink($GLOBALS['type'], $MENU->getID(), $MENU->getLanguageID(), $GLOBALS['EDITOR_MODE']['READONLY'], array()));
	$editorSmarty->assign('saveUrl', createEditorLink($GLOBALS['type'], $MENU->getID(), $MENU->getLanguageID(), $GLOBALS['EDITOR_MODE']['SAVE'], array()));
	$editorSmarty->assign('mode_save', $GLOBALS['EDITOR_MODE']['SAVE']);
	$editorSmarty->assign('mode_close', $GLOBALS['EDITOR_MODE']['CLOSE']);
	$editorSmarty->assign('mode_load', $GLOBALS['EDITOR_MODE']['LOAD']);
	$editorSmarty->assign('contents', getContentToEdit($MENU));
    $editorSmarty->assign('saveDirtyChanges', str_replace("'", '"', getTranslation('save_before_exit')));

	if(isset($_GET[EDITOR_MODE]) && $_GET[EDITOR_MODE] == 'translate' && isset($_GET['translate'])) {
		$editorSmarty->assign('SHOW_TRANSLATOR', $_GET['translate']);
		$editorSmarty->assign('translateContents', getContentToEditLanguage($MENU->getID(), $_GET['translate']));
	} else {
		$editorSmarty->assign('SHOW_TRANSLATOR', '');
	}

	if(!$readOnly)
	    $editorSmarty->assign('languages', $langs);
	else
	    $editorSmarty->assign('languages', array());

	return $editorSmarty;
}

// returns the smarty to be used for all editors, with preset values for config/cache/templates_c path
function getEditorSmarty() {
    $editorSmarty = new Smarty();
    $editorSmarty->template_dir   = _BIGACE_DIR_EDITOR;
    $editorSmarty->compile_dir    = _BIGACE_DIR_ADMIN . 'smarty/templates_c/';
    $editorSmarty->config_dir     = _BIGACE_DIR_ADMIN . 'smarty/configs/';
    $editorSmarty->cache_dir      = _BIGACE_DIR_ADMIN . 'smarty/cache/';
    return $editorSmarty;
}

// create an url to any editor/mode combination
function createEditorLink($type, $itemid, $language, $mode = 'empty', $values = array(), $name = '')
{
    $values[EDITOR_MODE] = $mode;
    return ApplicationLinks::getEditorTypeURL($type, $itemid, $language, $values);
}

// use this method to give feedback after loading/saving...
function dumpFeedback($feedback, $charset, $isError = false, $close = false)
{
    $editorSmarty = getEditorSmarty();
    $editorSmarty->assign('stylesheet', $GLOBALS['_BIGACE']['style']['class']->getCSS());
    $editorSmarty->assign('charset', $charset);
    $editorSmarty->assign('feedback', $feedback);
    $editorSmarty->assign('isError', $isError);
    $editorSmarty->assign('close', $close);
    $editorSmarty->display('feedback.tpl');
    //$GLOBALS['LOGGER']->logInfo($feedback); // for debugging log message
}

function prepareJSName($str) {
    $str = htmlspecialchars($str);
    $str = str_replace('"', '&quot;', $str);
    $str = str_replace("'", '&#039;', $str);
    return $str;
}

function getContentNames($ITEM) {
	$addContent = array();
	if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
	    import('classes.smarty.SmartyDesign');
	    $SMARTY_DESIGN = new SmartyDesign($ITEM->getLayoutName());
	    $addContent = $SMARTY_DESIGN->getContentNames();
	}
	else {
	    import('classes.layout.Layout');
		$layout = new Layout($ITEM->getLayoutName());
	    $addContent = $layout->getContentNames();
	}
	return $addContent;
}

/**
 * Will return an array with at least one entry.
 */
function getContentToEdit($MENU)
{
	$editableContent = array();
	$names = getContentNames($MENU);
	$editableContent[] = array(
			'param'		=> CONTENT_PARAMETER,
			'title'		=> getTranslation('content_page') . ': ' . '<img style="margin-right:5px" alt="'.$MENU->getLanguageID().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$MENU->getLanguageID().'.gif" class="langFlag">'.$MENU->getName(),
			'content'	=> $MENU->getContent(),
			'translate' => createEditorLink($GLOBALS['type'], $MENU->getID(), $MENU->getLanguageID(), "translatePreview&cntName=".CONTENT_PARAMETER)
	);

	if(!is_null($names) && count($names) > 0)
	{
		foreach($names AS $addCnt) {
			$content = get_content_raw($MENU->getID(), $MENU->getLanguageID(), $addCnt);

			$editableContent[] = array(
					'param'		=> $addCnt,
					'title'		=> $addCnt,
					'content'	=> $content['content'],
					'translate' => createEditorLink($GLOBALS['type'], $MENU->getID(), $MENU->getLanguageID(), "translatePreview&cntName=".$addCnt)
			);
	    }
	}
	return $editableContent;
}

function getContentToEditLanguage($id, $language)
{
	$mS = new MenuService();
	$MENU = $mS->getMenu($id, $language);
	return getContentToEdit($MENU);
}
