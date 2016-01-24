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
 * For further information visit {@link http://www.bigace.de www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

check_admin_login();
admin_header();

import('classes.exception.NoWriteRightException');
import('classes.exception.WrongArgumentException');
import('classes.exception.ExceptionHandler');
import('classes.menu.Menu');
import('classes.menu.MenuAdminService');
import('classes.item.ItemService');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');

define('PARAM_SAVE_REORDERED', 'hg5kjh9vgfds7');
$parentToReorder = extractVar('parentid', null);
$lngToReorder = extractVar('lngID', null);

if(!has_item_permission(_BIGACE_ITEM_MENU, $parentToReorder, 'w'))
{
    displayError( getTranslation('no_right') );
}
else if(is_null($parentToReorder) || is_null($lngToReorder)) 
{
    displayError( getTranslation('missing_values') );
}
else
{
    $_SERVICE = new ItemService(_BIGACE_ITEM_MENU);
    $item = $_SERVICE->getItem($parentToReorder, ITEM_LOAD_FULL, $lngToReorder);

    if(extractVar(PARAM_SAVE_REORDERED, null) != null)
    {
        $newPageOrder = extractVar('pageIDs', null);
        if( $newPageOrder != null )
        {
            $i = 0;
            $_ADMIN = new MenuAdminService();
            foreach($newPageOrder AS $pageID) {
                $_ADMIN->setItemPosition($pageID, $i);
                $i++;
            }
        }
        else
        {
            ExceptionHandler::processAdminException( new WrongArgumentException(400, 'Can not reorder Menus, Parameter missing!') );
        }
    }

	$req = new ItemRequest(_BIGACE_ITEM_MENU, $item->getID());
	$req->setTreetype(ITEM_LOAD_LIGHT);
	$req->setOrderBy(ORDER_COLUMN_POSITION);
	$req->setOrder($req->_ORDER_ASC);
	$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
	$req->setLanguageID($item->getLanguageID());
	$childs = new SimpleItemTreeWalker($req);

    ?>
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/selectbox.js"></script>
    <script type="text/javascript">
        function getOrderSelectBox() {
            return document.getElementById('pageOrder<?php echo $item->getID(); ?>');
        }

        function selectAllPages() {
            return selectAll( getOrderSelectBox() );
        }
    </script>
    <form action="<?php echo createAdminLink($GLOBALS['MENU']->getID()); ?>" method="post" onSubmit="return selectAllPages()">
    <input type="hidden" name="parentid" value="<?php echo $item->getID(); ?>" />
    <input type="hidden" name="<?php echo PARAM_SAVE_REORDERED; ?>" value="true" />
    <input type="hidden" name="lngID" value="<?php echo $lngToReorder; ?>" />
    <table border="0">
    <tr>
        <td colspan="2">
            <?php echo getTranslation('sort_childs_of'); ?>:<br/>
            <b><?php echo $item->getName(); ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <select multiple="multiple" size="10" style="width:100%" name="pageIDs[]" id="pageOrder<?php echo $item->getID(); ?>">
            <?php
            for($i=0; $i < $childs->count(); $i++)
            {
                $temp = $childs->next();
                echo '<option value="'.$temp->getID().'">'.$temp->getName().'</option>' . "\n";
            }
            ?>

            </select>
        </td>
    </tr>
    <tr>
        <td align="left">
            <a href="#" onclick="moveToTop( getOrderSelectBox() ); return false;"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>up_top.png" border="0"></a>
            <a href="#" onclick="moveUp( getOrderSelectBox() ); return false;"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>up.png" border="0"></a>
            <a href="#" onclick="moveDown( getOrderSelectBox() ); return false;"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>down.png" border="0"></a>
            <a href="#" onclick="moveToBottom( getOrderSelectBox() ); return false;"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>down_bottom.png" border="0"></a>
        </td>
        <td align="right">
            <button type="submit"><?php echo getTranslation('sort_button'); ?></button>
        </td>
    </tr>
    </table>
    </form>
    <?php

}
