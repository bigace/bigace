<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @package bigace.application
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

/**
 * This script displays a list of all Images within your CMS.
 */

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

import('classes.util.IOHelper');
import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.util.Translations');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.language.Language');
import('classes.category.CategoryService');
import('classes.image.Image');
import('classes.image.ImageService');
import('classes.fright.FrightService');
import('classes.template.TemplateService');
import('classes.administration.AdminStyleService');

// script constants
define('IMAGE_MODE_LISTING', 'listing'); // shows plain list of all available images
define('IMAGE_MODE_CATEGORIES', 'categories'); // shows listing of catgeories with amount of linked images
define('IMAGE_MODE_SEARCH', 'search'); // shows search frame
define('IMAGE_MODE_CATEGORY_LISTING', 'showLinkedItems'); // shows all images linked to the given category
define('IMAGE_MODE_UPLOAD', 'uploadImage'); // shows the formular to upload a new image
define('IMAGE_MODE_FILE', 'performUpload'); // perform the upload and shows the formular to upload a new image

define('CATEGORY_START_ID', _BIGACE_TOP_LEVEL); // start ID for the Category ID

define('IMAGE_PARAM_MODE', 'browserMode');
define('IMAGE_PARAM_CATEGORY_ID', 'categoryID');
define('IMAGE_PARAM_JAVASCRIPT', 'jsFunc');
define('IMAGE_PARAM_JAVASCRIPT_INFOS', 'imgInfos');

Translations::loadGlobal('imagebrowser');

$mode = extractVar(IMAGE_PARAM_MODE, IMAGE_MODE_LISTING);
$imagesToList = array();

$FRIGHTSERVICE = new FrightService();
define('ALLOWED_TO_UPLOAD', $FRIGHTSERVICE->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), _BIGACE_FRIGHT_ADMIN_ITEMS));
unset($FRIGHTSERVICE);

include_once(dirname(__FILE__) . '/functions.php');

// fetch the javascript function to be called
define('JS_FUNCTION', extractVar(IMAGE_PARAM_JAVASCRIPT, 'SetUrl'));
define('JS_FUNCTION2', extractVar(IMAGE_PARAM_JAVASCRIPT_INFOS, 'SetImageInfos'));
// dump the HTML Head
showHtmlHead(JS_FUNCTION,JS_FUNCTION2);

if($mode == IMAGE_MODE_FILE)
{
    $data = extractVar('data', array());

    if (isset($_FILES['userfile']) && isset($_FILES['userfile']['name']) && trim($_FILES['userfile']['name']) != '')
    {
        if(!isset($data['name']) || strlen(trim($data['name'])) == 0)
            $data['name'] = $_FILES['userfile']['name'];

        import('classes.image.ImageAdminService');
        $ias = new ImageAdminService();
        $data['parentid'] = strtok(extractVar('id'), '_');
        $result = $ias->registerUploadedFile($_FILES['userfile'], $data);
        if ($result->isSuccessful())
        {
            if (isset($data['category']))
            {
                if (!is_array($data['category'])) {
                    $data['category'] = array( $data['category'] );
                }

                foreach($data['category'] AS $catid) {
                    if ($catid != _BIGACE_TOP_LEVEL) {
                        $ias->addCategoryLink($result->getID(), $catid);
                    }
                }
            }
        }
        else
        {
            showError( getTranslation('upload_error_register') );
        }
    }
    else
    {
        if(!isset($_FILES['userfile']) || !isset($_FILES['userfile']['name']) || trim($_FILES['userfile']['name']) == '')
            showError( getTranslation('upload_error_no_file') );
        else
            showError( getTranslation('upload_error_unknown') );
    }
    unset($data);
}

?>
	<div align="center" style="margin:10px 0px">
		<a class="btn" href="<?php echo createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_LISTING)); ?>"><?php echo getTranslation('image_link_listing'); ?></a>
		<a class="btn" href="<?php echo createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_CATEGORIES)); ?>"><?php echo getTranslation('image_link_categories'); ?></a>
		<?php /*
		<a href="<?php echo createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_SEARCH)); ?>"><?php echo getTranslation('image_link_search'); ?></a>
		*/
		if (ALLOWED_TO_UPLOAD)
		{
		    ?>
		<a class="btn" href="<?php echo createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_UPLOAD)); ?>"><?php echo getTranslation('image_link_upload'); ?></a>
		    <?php
		}
		?>
	</div>
	<table height="100%" cellspacing="0" cellpadding="5" width="100%" border="0">
		<tr>
			<td valign="top" align="center">
			<?php

		    $categoryID = extractVar(IMAGE_PARAM_CATEGORY_ID, CATEGORY_START_ID);

			if($mode == IMAGE_MODE_CATEGORIES)
			{
		        browseCategories($categoryID);
			}
			else if($mode == IMAGE_MODE_UPLOAD && ALLOWED_TO_UPLOAD)
			{
			    $data = extractVar('data', array());
			    showUploadFormular($data);
			}
			else
			{
				?>
				<table border="0" cellspacing="5" cellpadding="0" width="100%">
				<col width="50%"/>
				<col width="50%"/>
					<tr>
						<td valign="top" align="left">
						<?php
		                switch($mode)
		                {
		                    case IMAGE_MODE_CATEGORY_LISTING:
		                        $catService = new CategoryService();
		                        $category = $catService->getCategory($categoryID);
								echo '<b>'.getTranslation('select_images_category').' "'.$category->getName().'"</b>';
		                        break;
		                    default:
								echo '<b>'.getTranslation('select_images_listing').':</b>';
		                        break;
		                }
					    ?>
						</td>
						<td valign="top" align="left">
							<input type="checkbox" name="preview" id="preview" onclick="javascript:checkPreview()" checked> <?php echo getTranslation('show_preview'); ?>
							<button id="scalePreview" onClick="scaleToPreview();return false;"><?php echo getTranslation('scale_to_preview'); ?></button>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<?php
		                    switch($mode)
		                    {
		                        case IMAGE_MODE_CATEGORY_LISTING:
		                            showListing( showCategoryItems($categoryID) );
		                            break;
		                        case IMAGE_MODE_LISTING:
		                            showListing( loadPlainListing(_BIGACE_TOP_LEVEL) );
		                            break;
		                        case IMAGE_MODE_SEARCH:
		                            // TODO implement the search dialog
		                            showListing( loadPlainListing(_BIGACE_TOP_LEVEL) );
		                            break;
		                        default:
		                            showListing( loadPlainListing(_BIGACE_TOP_LEVEL) );
		                            break;
		                    }
							?>
						</td>
						<td valign="top" align="left">
		                    <div class="imgPreviewDiv">
							    <img src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/images/spacer.gif" id="imgPreview" name="imgPreview">
							</div>
							<div id="choosenImage">
							    <table width="100%" border="0">
							    <tr>
    							    <td valign="top"><?php echo getTranslation('choosen_image'); ?>:<br/><span id="curImgName"></span></td>
    							    <td align="right">
        							    <input class="imgChooserBtn" type="button" value="<?php echo getTranslation('image_button_select'); ?>" onclick="ok();">
        							</td>
        					    </tr>
        					    </table>
							</div>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top">

						</td>
						<td align="right"  valign="top">
							<input class="imgChooserBtn" type="button" value="<?php echo getTranslation('image_button_select'); ?>" onclick="ok();">
							<input type="button" value="<?php echo getTranslation('image_button_cancel'); ?>" onclick="window.close();">
		                </td>
					</tr>
				</table>
				<?php
		        unset($categoryID);
			}
			?>
			</td>
		</tr>
	</table>
</BODY>
</HTML>
<?php

    include_once(_BIGACE_DIR_LIBS . 'footer.inc.php');
