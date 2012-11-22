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
 * @package bigace.editor
 */

import('classes.item.ItemService');
import('classes.menu.Menu');
import('classes.image.Image');
import('classes.file.File');
import('classes.item.SimpleItemTreeWalker');
import('classes.util.LinkHelper');

function GetFolders( $itemtype, $id )
{
    // remove all slahes from the ID String
    $id = str_replace( '/', '', $id);

	// Arrays that will hold the folders and files names.
	$aFolders	= array() ;

    if ($itemtype == _BIGACE_ITEM_MENU)
    {
        $itemService = new ItemService($itemtype);

        $langid = isset($_GET['brwLang']) ? $_GET['brwLang'] : (isset($_POST['brwLang']) ? $_POST['brwLang'] : $GLOBALS['_BIGACE']['SESSION']->getLanguageID());
        //$tree = $itemService->getWayHome($id, true);

	    $req = new ItemRequest($itemService->getItemType(), $id);
    	$req->setTreetype(ITEM_LOAD_LIGHT);
	    $req->setOrderBy(ORDER_COLUMN_POSITION);
    	$req->setOrder($req->_ORDER_ASC);
    	$req->setLanguageID($langid);
    	// display hidden navi entrys by default
  		$req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
	    $tree = new SimpleItemTreeWalker($req);
        //$tree = $itemService->getTreeForLanguage($id, $langid, 'name');

        $a = $tree->count();
        for ($i=0; $i < $a; $i++)
        {
            $temp = $tree->next();
            //$temp = $itemService->getItem($temp, ITEM_LOAD_FULL, $langid);
            if ($itemtype == _BIGACE_ITEM_MENU && $temp->hasChildren())
            {
                $aFolders[] = createFolderNodeForItem($temp) ;
            }
        }
    }

    echo createCurrentFolder($itemtype,$id);

	// Open the "Folders" node.
	echo "<Folders>" ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	// Close the "Folders" node.
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $itemtype, $id )
{
    // remove all slahes from the ID String
    $id = str_replace( '/', '', $id);

    if(strlen(trim($id)) == 0 || strcmp(trim($id), '/') == 0 || strcmp(trim($id), '') == 0) {
        $id = _BIGACE_TOP_LEVEL;
    }
    $itemService = new ItemService($itemtype);

	// Arrays that will hold the folders and files names.
	$aFolders	= array() ;
	$aFiles		= array() ;

    $langid = isset($_GET['brwLang']) ? $_GET['brwLang'] : (isset($_POST['brwLang']) ? $_POST['brwLang'] : $GLOBALS['_BIGACE']['SESSION']->getLanguageID());

    $req = new ItemRequest($itemService->getItemType(), $id);
    $req->setTreetype(ITEM_LOAD_FULL);
    $req->setOrderBy(ORDER_COLUMN_POSITION);
    $req->setOrder($req->_ORDER_ASC);
    // display hidden navi entrys by default
    $req->setFlagToExclude($req->FLAG_ALL_EXCEPT_TRASH);
    $req->setLanguageID($langid);
    $tree = new SimpleItemTreeWalker($req);
    //$tree = $itemService->getTreeForLanguage($id, $langid, 'name');

    $a = $tree->count();
    for ($i=0; $i < $a; $i++)
    {
        $temp = $tree->next();
        if ($itemtype == _BIGACE_ITEM_MENU && !$itemService->isLeaf($temp->getID()))
        {
            // only menus have folders, submenus and childs
            $aFolders[] = createFolderNodeForItem($temp) ;
        }
        else
        {
            $size = (int)($temp->getSize() / 1024);

            $values = array(
                        'url'   => ConvertToXmlAttribute(LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($temp))),
                        'id'    => $temp->getId()
            );

    	    $aFiles[] = createFileNode($temp->getName(), $size, $values);
    	}

    }

    echo createCurrentFolder($itemtype,$id);

	// Send the folders
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	echo '</Folders>' ;

	// Send the files
	echo '<Files>' ;

	foreach ( $aFiles as $sFile )
		echo $sFile ;

	echo '</Files>' ;
}

function FileUpload( $resourceType, $id )
{
	$sMessage = '' ;
	$sErrorNumber = '0' ;
	$sFileName = '' ;

    $itemtype = substr($resourceType, 4, 1);

    if (strlen($itemtype) != 1)
    {
        $sErrorNumber = '204' ;
        $sMessage = 'Could not find ResourceType: ' . $resourceType;
    }
    else
    {
        if ($itemtype == _BIGACE_ITEM_MENU)
        {
            $sErrorNumber = '203' ;
        }
        else
        {
        	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
        	{
                loadClass('item', 'ItemAdminService');

                if ($itemtype == _BIGACE_ITEM_IMAGE)  {
                    loadClass('image', 'ImageAdminService');
                    $adminService = new ImageAdminService();
                } else if ($itemtype == _BIGACE_ITEM_FILE)  {
                    loadClass('file', 'FileAdminService');
                    $adminService = new FileAdminService();
                }

                if(isset($adminService))
                {
                    $newName = (isset( $_POST['NewTitle'] ) && strlen(trim( $_POST['NewTitle'] )) > 0) ? trim( $_POST['NewTitle'] ) : $_FILES['NewFile']['name'];
                    $newDesc = (isset( $_POST['NewDescription'] ) && strlen(trim( $_POST['NewDescription'] )) > 0) ? trim( $_POST['NewDescription'] ) : '';

                    if($adminService->isAllowed($_FILES['NewFile']))
                    {
                        $data = array('name' => $newName, 'description' => $newDesc);
                        $AdminRequestResult = $adminService->registerUploadedFile($_FILES['NewFile'], $data);

                        if (!$AdminRequestResult->isSuccessful())
                            $sErrorNumber = '210' ;
            		}
            		else
            		{
            			$sErrorNumber = '202' ;
            		}
                }
                else
                {
        			$sErrorNumber = '204' ;
                    $sMessage = $resourceType;
                }
        	}
        	else
        	{
        		$sErrorNumber = '205' ;
        	}
        }
    }

	echo '<script type="text/javascript">' ;
	echo 'window.parent.frames["frmUpload"].OnUploadCompleted(' . $sErrorNumber . ',"' . str_replace( '"', '\\"', $sFileName ) . '", "'.$sMessage.'") ;' ;
	echo '</script>' ;

	exit ;
}
