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
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

/**
 * This script prints general information about one Item.
 * <br>
 * Parameter
 * ----------
 * ID:       Pass an Parameter called data[id]. Default (if not found) is the current Menu ID.
 * ITEMTYPE: Pass an Parameter called data[itemtype]. Default (if not found) is _BIGACE_ITEM_MENU.
 * LANGUAGE: Pass an Parameter called data[language]. Default (if not found) is the Session Language.
 */

import('classes.language.ItemLanguageEnumeration');
import('classes.item.ItemService');
import('classes.layout.Layout');
import('classes.smarty.SmartyDesign');
import('classes.menu.Menu');
import('classes.image.Image');
import('classes.modul.Modul');
import('classes.file.File');
import('classes.core.ServiceFactory');
import('classes.right.RightService');
import('classes.item.ItemHistoryService');
import('classes.util.html.FormularHelper');

include_once(_BIGACE_DIR_ADMIN . 'styling.php');

loadLanguageFile('administration', $GLOBALS['_BIGACE']['PARSER']->getLanguage());
loadLanguageFile('bigace', $GLOBALS['_BIGACE']['PARSER']->getLanguage());

$data = extractVar('data', array());

if(!isset($data['id']))
    $data['id'] = $GLOBALS['_BIGACE']['PARSER']->getItemID();

if(!isset($data['itemtype']))
    $data['itemtype'] = _BIGACE_ITEM_MENU;

if(!isset($data['language']))
    $data['language'] = $GLOBALS['_BIGACE']['PARSER']->getLanguage();

$rs = new RightService();
$item_right = $rs->getItemRight( $data['itemtype'], $data['id'], $GLOBALS['_BIGACE']['SESSION']->getUserID() );
unset($rs);

if ($item_right->canRead())
{
    $_ISERVICE  = new ItemService($data['itemtype']);
    $item       = $_ISERVICE->getClass($data['id'], ITEM_LOAD_FULL, $data['language']);

        // Create List of not editable Object Infos
        $services = ServiceFactory::get();
        $PRINCIPALS = $services->getPrincipalService();

        $last_user      = $PRINCIPALS->lookupByID($item->getLastByID());
        $create_user    = $PRINCIPALS->lookupByID($item->getCreateByID());

        // all item information entries
        $entries = array(
                        getTranslation('name')             => $item->getName(),
                        getTranslation('id')               => $item->getID(),
                        );
		$uurl = LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($item));
        $entries = array_merge($entries, array(
                         getTranslation('unique_name') => '<a href="'.$uurl.'" target="_blank">'.$uurl.'</a>'
                   ));

        // calculate avilable language versions for this item
        $ile =  $_ISERVICE->getItemLanguageEnumeration($item->getID());
        $availLanguages = '';
        for ($i=0; $i < $ile->count(); $i++)
        {
            $tempLanguage = $ile->next();
            $availLanguages .= ' <img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale().'.gif" class="langFlag">';
        }

        $entries = array_merge($entries, array(
                         getTranslation('language_versions') => $availLanguages
                   ));


        // if optional description is available
        if(strlen($item->getDescription()) > 0) {
            $entries = array_merge($entries, array(
                             getTranslation('description') => $item->getDescription()
                       ));
        }

        // if optional catchwords are available
        if(strlen($item->getCatchwords()) > 0) {
            $entries = array_merge($entries, array(
                             getTranslation('catchwords') => $item->getCatchwords()
                       ));
        }

        if($item->getItemType() == _BIGACE_ITEM_MENU)
        {
            $mod = $item->getModul();

			if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
            	$lay = new SmartyDesign($item->getLayoutName());
			} else {
            	$lay = new Layout($item->getLayoutName());
			}
            $entries = array_merge($entries, array(
                            getTranslation('modul')         => $mod->getName() . '<br><i>'.$mod->getDescription().'</i>',
                            getTranslation('layout')        => $lay->getName() . '<br><i>'.$lay->getDescription().'</i>',

                       ));

            // if no workflow is configured its name will be empty
            if(strlen($item->getWorkflowName()) > 0) {
                $entries = array_merge($entries, array(
                                getTranslation('menu_workflow') => $item->getWorkflowName(),
                           ));
            }
        }

        $ihs = new ItemHistoryService($item->getItemtypeID());
        $ihcount = $ihs->countHistoryVersions($item->getID(), $item->getLanguageID());

        $entries = array_merge($entries, array(
                        getTranslation('history_versions') => $ihcount,
                        getTranslation('created')          => date("Y-m-d H:i:s", $item->getCreateDate()) . ' ' . getTranslation('by') . ' ' . $create_user->getName(),
                        getTranslation('last_edited')      => date("Y-m-d H:i:s", $item->getLastDate()) . ' ' . getTranslation('by') . ' ' . $last_user->getName(),
                        getTranslation('history_versions') => ($ihcount == 0) ? '0' : $ihcount,
                        getTranslation('filename')         => $item->getOriginalName(),
                    ));

        // Mimetype for menu is always text/html, therfor skip it
        if($item->getItemType() != _BIGACE_ITEM_MENU)
        {
            $entries = array_merge($entries, array(
                            'Mimetype'     => $item->getMimetype(),
                       ));
        }
        $LANGUAGE = new Language($item->getLanguageID());

        ?><!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	    <html>
	    <head>
	    <title><?php echo getTranslation('object_infos'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $LANGUAGE->getCharset(); ?>">
        <meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>">
	    <link rel="stylesheet" href="<?php echo $GLOBALS['_BIGACE']['style']['class']->getCSS(); ?>" type="text/css">
	    <script language="JavaScript">
	    <!--
	    window.focus();

	    function doClose() {
	        window.close();
	    }
	    // -->
	    </script>
	    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/jquery.js"></script>
	    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/tablesorter/jquery.tablesorter.js"></script>
	    </head>
	    <body style="margin:10px;">
		<table class="tablesorter" cellspacing="0" width="100%">
			<col />
			<col />
			<thead>
				<tr>
					<th colspan="2"><?php echo getTranslation('object_infos'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach($entries AS $rowName => $rowValue) {
				?>
			    <tr>
			        <td valign="top"><?php echo $rowName; ?></td>
			        <td><?php echo $rowValue; ?></td>
			    </tr>
			    <?php
			}
			?>
			</tbody>
		</table>

		<script type="text/javascript">
        <!--
		$(document).ready( function() {
	        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 1: {sorter: false} } });
		    }
		);

        document.write('<div align="center"><button style="margin-top:20px;" onclick="self.close()"><?php echo getTranslation('close'); ?></button></div>');
        // -->
        </script>
        <?php
            import('classes.util.html.CopyrightFooter');
            CopyrightFooter::toString();
        ?>
        </body>
        </html>

        <?php
        unset ($last_user);
        unset ($create_user);
        unset($entries);
	    include_once(_BIGACE_DIR_LIBS . 'footer.inc.php');

}
else
{
    header("Location: " . createMenuLink(_BIGACE_TOP_LEVEL . '_tERROR_kerror-403'));
}

?>