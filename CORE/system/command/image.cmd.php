<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
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
 * This Command is specialized for sending images to clients.
 *
 * It can handle simple some manipulations as well:
 * - create thumbnails on the fly, cached for further usage
 * - create resized image versions
 *
 * Add one (or both) of the following parameter to your request for image manipulation:
 *
 * - resizeWidth
 * - resizeHeight
 * - crop (boolean, default: false)
 * - quality (default: 100)
 *
 * If you submit only one value the other one will be automatically calculated from the original image dimensions.
 *
 * If you use the deprecated parameter "resize", it will be handled like "resizeWidth".
 *
 * For example:
 *
 * - /bigace/image/10/image.jpg?resizeWidth=100
 * - /bigace/image/10/image.jpg?resizeHeight=100
 * - /bigace/image/10/image.jpg?resizeWidth=100&resizeHeight=200
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.command
 */

/**
 * @deprecated use _PARAM_IMAGE_NEW_WIDTH instead!
 */
define('_PARAM_IMAGE_OLD_WIDTH',    'resize');

/**
 * Constant for the Width Parameter.
 */
define('_PARAM_IMAGE_NEW_WIDTH',    'resizeWidth');
/**
 * Constant for the Height Parameter.
 */
define('_PARAM_IMAGE_NEW_HEIGHT',   'resizeHeight');

/**
 * @access private
 */
define('_IMAGE_RESIZE_MODE_HEIGHT', 'heightResize');
/**
 * @access private
 */
define('_IMAGE_RESIZE_MODE_WIDTH',  'widthResize');
/**
 * @access private
 */
define('_IMAGE_RESIZE_MODE_BOTH',   'bothResize');
/**
 * @access private
 */
define('_IMAGE_RESIZE_MODE_NONE',   'noResize');

// load all required classes
import('classes.core.FileCache');
import('classes.image.Image');
import('classes.item.ItemService');
import('classes.right.RightService');

$RIGHT_SERVICE = new RightService();
$ITEM_SERVICE  = new ItemService();
$ITEM_SERVICE->initItemService( _BIGACE_ITEM_IMAGE );

$itemid     = $GLOBALS['_BIGACE']['PARSER']->getItemID();
$languageid = $GLOBALS['_BIGACE']['PARSER']->getLanguageFromRequest();

// fallback cause ItemService uses an empty String to define a NONE Language dependend item call
if ($languageid == NULL) $languageid = '';

$ITEM_RIGHT = $RIGHT_SERVICE->getItemRight( _BIGACE_ITEM_IMAGE , $itemid, $GLOBALS['_BIGACE']['SESSION']->getUserID());

if ($ITEM_RIGHT->canRead())
{
    $fileCache = new FileCache();
    $FILE = $ITEM_SERVICE->getClass($itemid, ITEM_LOAD_FULL, $languageid);
    $bild = $FILE->getFullURL();

    $resizeHeight   = (isset($_GET['h']) ? $_GET['h'] : extractVar(_PARAM_IMAGE_NEW_HEIGHT, null));
    $resizeWidth    = (isset($_GET['w']) ? $_GET['w'] : extractVar(_PARAM_IMAGE_NEW_WIDTH, extractVar(_PARAM_IMAGE_OLD_WIDTH, null)));
    $zoom_crop      = (isset($_GET['c']) ? (bool)$_GET['c']: true);
    $quality        = (isset($_GET['q']) ? intval($_GET['q']) : 100);

    if (!is_null($resizeHeight) || !is_null($resizeWidth))
    {
        // create cache file name
        $cacheArray = array(
                        'c' => $zoom_crop,
                        'q' => $quality,
                        'w' => $resizeWidth,
                        'h' => $resizeHeight,
                        'l' => $languageid
        );
        $cacheName = $fileCache->_createItemCacheName($FILE->getItemType(), $FILE->getID(), $cacheArray);

        if (!file_exists($cacheName))
        {
            // Calculate new sizes
            $size = getimagesize($bild);

            // for cropping
	        $width = $size[0];
	        $height = $size[1];

            // check if image support is given by php
            if (function_exists("imagecreatetruecolor"))
            {
            	@ini_set('memory_limit', '50M');

                $image = open_image($size[2], $bild);
                if(!is_null($image) && $image !== false)
                {
                    $new_height      = $resizeHeight;
                    $new_width       = $resizeWidth;

	                // don't allow new width or height to be greater than the original
	                if( $new_width > $width ) { $new_width = $width; }
	                if( $new_height > $height ) { $new_height = $height; }

	                // generate new w/h if not provided
	                if( $new_width && !$new_height ) {
		                $new_height = $height * ( $new_width / $width );
	                }
	                elseif($new_height && !$new_width) {
		                $new_width = $width * ( $new_height / $height );
	                }
	                elseif(!$new_width && !$new_height) {
		                $new_width = $width;
		                $new_height = $height;
	                }

	                // create a new true color image
	                $canvas = imagecreatetruecolor( $new_width, $new_height );

	                if( $zoom_crop ) {
		                $src_x = $src_y = 0;
		                $src_w = $width;
		                $src_h = $height;

		                $cmp_x = $width  / $new_width;
		                $cmp_y = $height / $new_height;

		                // calculate x or y coordinate and width or height of source
		                if ( $cmp_x > $cmp_y ) {
			                $src_w = round( ( $width / $cmp_x * $cmp_y ) );
			                $src_x = round( ( $width - ( $width / $cmp_x * $cmp_y ) ) / 2 );
		                }
		                elseif ( $cmp_y > $cmp_x ) {
			                $src_h = round( ( $height / $cmp_y * $cmp_x ) );
			                $src_y = round( ( $height - ( $height / $cmp_y * $cmp_x ) ) / 2 );
		                }
		                imagecopyresampled( $canvas, $image, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h );
	                }
	                else {
		                // copy and resize part of an image with resampling
		                imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	                }

                    switch($size[2]) {
                        case 1: // GIF
		                    if(($res = imagegif($canvas, $cacheName)) === false) {
                				$GLOBALS['LOGGER']->logError("Could not write cached gif image file to: " . $cacheName);
		                    	imagegif($canvas);
		                    	return;
		                    }
                            break;
                        case 2: // JPG
		                    if(($res = imagejpeg($canvas, $cacheName, $quality)) === false) {
                				$GLOBALS['LOGGER']->logError("Could not write cached jpg image file to: " . $cacheName);
		                    	imagejpeg($canvas);
		                    	return;
		                    }
                            break;
                        case 3: // PNG
		                    if(($res = imagepng($canvas, $cacheName)) === false) {
                				$GLOBALS['LOGGER']->logError("Could not write cached png image file to: " . $cacheName);
		                    	imagepng($canvas);
		                    	return;
		                    }
                            break;
                    }

                    // cache entry could be created, so switch image name
                    $bild = $cacheName;

                    // remove image from memory
                    imagedestroy($canvas);
                }

                unset ($cacheName);
            }
        }
        else
        {
            // Cache Entry already exists
            $bild = $cacheName;
            unset ($cacheName);
        }
    }

	$ITEM_SERVICE->increaseViewCounter($FILE->getID(),$FILE->getLanguageID());

    show_image($bild, $FILE->getMimetype(), $FILE->getName());
}

exit;

// sends an image to the browser
function show_image($cache_file, $mime_type, $filename) {

	if (file_exists($cache_file)) {

		$gmdate_mod = gmdate( 'D, d M Y H:i:s', filemtime( $cache_file ) );
		if (strstr($gmdate_mod, 'GMT') === FALSE) {
			$gmdate_mod .= " GMT";
		}

		$fileSize = filesize( $cache_file );
		$etag = $cache_file . $fileSize . $gmdate_mod;

		// check for updates since last cache call
	    if (isset($_SERVER[ "HTTP_IF_MODIFIED_SINCE" ])) {
			$if_modified_since = preg_replace('/;.*$/', '', $_SERVER[ "HTTP_IF_MODIFIED_SINCE" ]);

			if ( $if_modified_since == $gmdate_mod ) {
				header("HTTP/1.1 304 Not Modified");
				exit;
			}
		}

		// send header before displaying image
		header("Content-Type: " . $mime_type);
        header("Content-Disposition: inline; filename=".urlencode($filename));
		header("Accept-Ranges: bytes");
		header("Last-Modified: " . $gmdate_mod);
		header("Content-Length: " . $fileSize);
		header("Cache-Control: must-revalidate, proxy-revalidate, private");
		header("Etag: " . md5($etag));
/*
		header( "Cache-Control: max-age=9999, must-revalidate" );
        if(!is_null($gmdate_mod))
		header( "Etag: " . md5($fileSize . $gmdate_mod) );
		header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 9999 ) . "GMT" );
*/
		readfile($cache_file);
        flush();
		exit;
	}

}


function open_image($mime_type, $src) {
    switch($mime_type) {
        case 1: // GIF
            if (function_exists("imagecreatefromgif") && function_exists("imagegif")) {
                return imagecreatefromgif($src);
            }
            break;
        case 2: // JPG
		    @ini_set('gd.jpeg_ignore_warning', 1);
		    return imagecreatefromjpeg($src);
        case 3: // PNG
		    return imagecreatefrompng($src);
    }
    return null;
}

?>