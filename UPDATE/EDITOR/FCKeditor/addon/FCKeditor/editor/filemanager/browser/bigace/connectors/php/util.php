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
 * --------------------------------------------------------   
 * Some of the functions are copyright or inspired by
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 *      FCKeditor - The text editor for internet
 * --------------------------------------------------------   
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.editor
 */

function ConvertToXmlAttribute( $value )
{
	return htmlspecialchars( $value );
}

function createFileNode($name, $size, $keyValue = array()) 
{
    $node = '<File name="' . ConvertToXmlAttribute( $name ) . '" size="' . $size . '"';
    if(count($keyValue) > 0) {
        foreach($keyValue AS $key => $value) {
            $node .= ' '.$key.'="'.$value.'"';
        }
    }
    return $node . ' />';
}

function createFolderNode($name, $size, $keyValue = array()) 
{
    $node = '<Folder name="' . ConvertToXmlAttribute( $name ) . '" size="' . $size . '"';
    if(count($keyValue) > 0) {
        foreach($keyValue AS $key => $value) {
            $node .= ' '.$key.'="'.$value.'"';
        }
    }
    return $node . ' />';
}

function RemoveExtension( $fileName )
{
	return substr( $fileName, 0, strrpos( $fileName, '.' ) ) ;
}

function GetRootPath()
{
	$sRealPath = realpath( './' ) ;

	$sSelfPath = $_SERVER['PHP_SELF'] ;
	$sSelfPath = substr( $sSelfPath, 0, strrpos( $sSelfPath, '/' ) ) ;

	return substr( $sRealPath, 0, strlen( $sRealPath ) - strlen( $sSelfPath ) ) ;
}

function createFolderNodeForItem($item) 
{
    $size = (int)($item->getSize() / 1024);
    $values = array(
                    'id'    => $item->getID(),
                    'url'   => ConvertToXmlAttribute(LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($item))),
    );
              
    return createFolderNode(ConvertToXmlAttribute( $item->getName() ), $size, $values) ;
}

function createCurrentFolder($itemtype,$id)
{
    $itemService = new ItemService($itemtype);
    $langid = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
    $tree = $itemService->getWayHome($id, true);
    $tree = array_reverse($tree);
    
    $item = $itemService->getItem($id, ITEM_LOAD_FULL, $langid);
    
    $name = '/';

    $a = count($tree);
    for ($i=0; $i < $a; $i++) 
    {
        $temp = $tree[$i];
        if($temp != _BIGACE_TOP_LEVEL) {
            $temp = $itemService->getItem($temp, ITEM_LOAD_FULL, $langid);
            $name .= $temp->getName() . '/' ;
        }
    }
    
    return '<CurrentFolder parent="'.$item->getParentID().'" id="'.$id.'" url="'.ConvertToXmlAttribute($name).'" path="'.ConvertToXmlAttribute($name).'" />';

}

?>