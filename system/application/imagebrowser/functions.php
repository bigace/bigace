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
 * @package bigace.application
 */

function showError($msg)
{
    echo '<h3 class="error">'.$msg.'</h3>';
}

function createImageBrowserLink($params)
{
    $link = new CMSLink();
    $link->setCommand('application');
    $link->setItemID($GLOBALS['_BIGACE']['PARSER']->getItemID());
    $link->setAction('images');
    if(!isset($params[IMAGE_PARAM_JAVASCRIPT]))
        $params[IMAGE_PARAM_JAVASCRIPT] = JS_FUNCTION;
    return LinkHelper::getUrlFromCMSLink($link, $params);
}

function showCategoryItems($categoryID)
{
    $items = array();
    $imgService = new ImageService();
    $catService = new CategoryService();
    $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_IMAGE, $categoryID);
    for($i=0; $i < $catEnum->count(); $i++)
    {
        $temp = $catEnum->next();
        $items[] = $imgService->getClass($temp['itemid']);
    }
    return $items;
}

function loadPlainListing($language = '')
{
    $images = array();

    $imgService = new ImageService();
    $imageWalker = $imgService->getTree(_BIGACE_TOP_LEVEL, 'name');

    //$request = new ItemRequest(_BIGACE_ITEM_IMAGE, _BIGACE_TOP_LEVEL);
    //if($language != '')
    //    $request->setLanguageID($language);
    //$request->setOrderBy('name');
    //$imageWalker = new SimpleItemTreeWalker($request);

    $a = $imageWalker->count();

    for ($i=0; $i < $a; $i++)
    {
        $temp = $imageWalker->next();
    	$images[] = $temp;
    }
    return $images;
}

/**
 * Gets a preconfigured instance of the TemplateService.
 */
function getTemplateService()
{
    $STYLE_SERVICE = new AdminStyleService();
    $style = $STYLE_SERVICE->getConfiguredStyle();

    $ts = new TemplateService();
    $ts->addTemplateDirectory(dirname(__FILE__));
    setBigaceTemplateValue('ADDON_DIR', _BIGACE_DIR_ADDON_WEB);
    setBigaceTemplateValue('PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB);
    setBigaceTemplateValue('BIGACE_VERSION', _BIGACE_ID);
    setBigaceTemplateValue('STYLE_DIR', $style->getWebDirectory());
    return $ts;
}

function showListing($images)
{
    if(!is_array($images) || count($images) == 0)
    {
        echo '<b>'.getTranslation('no_images_available').'</b>';
        return;
    }
    $ts = getTemplateService();
    $tpl = $ts->loadTemplatefile("ItemListing.tpl.html", false, true);
    $tpl->setVariable("LISTING_WIDTH", '100%');

    $cssClass = "row1";

    foreach($images AS $image)
    {
		$filename = $image->getOriginalName();
		if (strlen($filename) > 25)
			$filename = substr($filename,0,22) . '...';

		$imgName = $image->getName();
		if (strlen($imgName) > 25)
			$imgName = substr($imgName,0,22) . '...';

        $tpl->setCurrentBlock("row");
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("IMAGE_URL", LinkHelper::getURLFromCMSLink(LinkHelper::getCMSLinkFromItem($image)));
        $tpl->setVariable("IMAGE_ID", $image->getID());
        $tpl->setVariable("IMAGE_NAME", prepareJSName($imgName));
        $tpl->setVariable("IMAGE_FILENAME", $filename);
        $tpl->setVariable("IMAGE_MIMETYPE", strtolower(getFileExtension($image->getOriginalName())));
        $tpl->parseCurrentBlock("row");

        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
    }

    $tpl->show();
}

function showUploadFormular($data)
{
    import('classes.language.LanguageEnumeration');
    import('classes.util.formular.CategorySelect');
    import('classes.util.html.Option');

    if (!isset($data['name'])        ) $data['name']            = '';
    if (!isset($data['description']) ) $data['description']     = '';
    if (!isset($data['langid'])      ) $data['langid']          = $GLOBALS['_BIGACE']['SESSION']->getLanguageID();
    if (!isset($data['category'])    ) $data['category']        = _BIGACE_TOP_LEVEL;

    $ts = getTemplateService();
    $tpl = $ts->loadTemplatefile("UploadFormular.tpl.html", false, true);

    $tpl->setCurrentBlock("uploadForm");
    $tpl->setVariable("ACTION_LINK", createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_FILE)));
    $tpl->setVariable("MAX_FILE_SIZE", ConfigurationReader::getConfigurationValue('admin', 'max.upload.size'));
    $tpl->setVariable("DATA_NAME", $data['name']);
    $tpl->setVariable("DATA_DESCRIPTION", $data['description']);
    $tpl->setVariable("ITEMTYPE", _BIGACE_ITEM_IMAGE);

    $s = new CategorySelect();
    $s->setID('data[category]');
    $s->setName('data[category]');
    $s->setIsMultiple();
    $s->setSize(5);
    // calculate the Category Tree
    $e = new Option();
    $e->setText(getTranslation('upload_empty_category'));
    $e->setIsSelected();
    $s->addOption($e);
    $s->setStartID(_BIGACE_TOP_LEVEL);
    $tpl->setVariable("CATEGORY_SELECTOR", $s->getHtml());

    $langEnum = new LanguageEnumeration();
    for ($i = 0; $i < $langEnum->count(); $i++)
    {
        $temp = $langEnum->next();
        $languages[$temp->getName()] = $temp->getID();
        $tpl->setVariable("LANGUAGE_ID", $temp->getID());
        $tpl->setVariable("LANGUAGE_NAME", $temp->getName());
        $selected = '';
        if ($GLOBALS['_BIGACE']['DEFAULT_LANGUAGE'] == $temp->getID())
            $selected = ' selected';
        $tpl->setVariable("LANGUAGE_SELECTED", $selected);
        $tpl->setCurrentBlock("language");
        $tpl->parseCurrentBlock("language");
    }

    $tpl->parseCurrentBlock("uploadForm");

    $tpl->show();
}

function prepareJSName($str) {
    $str = htmlspecialchars($str);
    $str = str_replace('"', '&quot;', $str);
    //$str = addSlashes($str);
    //$str = str_replace("'", '\%27', $str);
    $str = str_replace("'", '&#039;', $str);
    return $str;
}

function showHtmlHead($jsFunctionName, $jsFunctionName2)
{
    $lang = new Language($GLOBALS['_BIGACE']['SESSION']->getLanguageID());

    $ts = getTemplateService();
    $tpl = $ts->loadTemplatefile("BrowserHtmlHeader.tpl.html", false, true);
    $tpl->setVariable("LANGUAGE_CHARSET", $lang->getCharset());
    // FIXME use dynamic function Name
    $tpl->setVariable("JAVASCRIPT_FUNCTION", $jsFunctionName);
    $tpl->setVariable("JAVASCRIPT_FUNCTION_INFOS", $jsFunctionName2);
    $tpl->setVariable("PREVIEW_URL", createCommandLink(_BIGACE_CMD_IMAGE, "' + id + '", array(), "' + name + '"));
    $tpl->setVariable("PREVIEW_HEIGHT", '225');
    $tpl->show();
}

function browseCategories($categoryID)
{
    $catService = new CategoryService();
    $ts = getTemplateService();
    $tpl = $ts->loadTemplatefile("CategoryMenu.tpl.htm", true, true);
    $tpl->setVariable("LISTING_WIDTH", '100%');

    $entrys = array();
    $category = $catService->getCategory($categoryID);

	$cssClass = "row1";

    // Current Category
    $name = '<b>'.$category->getName().'</b>';
    $parent = $category->getParent();
    if ($category->getID() != CATEGORY_START_ID)
    {
      $name = '<a href="'.createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_CATEGORIES, IMAGE_PARAM_CATEGORY_ID => $parent->getID())).'" title="'.$parent->getName().'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/arrow_up.gif" border="0" alt="'.$parent->getName().'">&nbsp;' . $name.'</a>';
    }

    $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_IMAGE, $category->getID());

    $tlink = '';
    if($catEnum->count() > 0)
        $tlink = '<a href="'.createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_CATEGORY_LISTING, IMAGE_PARAM_CATEGORY_ID => $category->getID())).'">'.getTranslation('show_linked').'</a>';

    $tpl->setCurrentBlock("row");
    $tpl->setVariable("CSS", $cssClass);
    $tpl->setVariable("CATEGORY_NAME", $name);
    $tpl->setVariable("ACTION_LINKED", $tlink);
    $tpl->setVariable("AMOUNT", $catEnum->count());
    $tpl->parseCurrentBlock("row");

    $enum = $category->getChilds();
    $val = $enum->count();

    for ($i = 0; $i < $val; $i++)
    {
		$cssClass = ($cssClass == "row1") ? "row2" : "row1";

        $temp = $enum->next();
        $name = $temp->getName();
        if ($temp->hasChilds()) {  // category
            $name = '<a href="'.createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_CATEGORIES, IMAGE_PARAM_CATEGORY_ID => $temp->getID())).'" title="'.$temp->getName().'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/arrow_down.gif" border="0" alt="'.$name.'">&nbsp;' . $name . '</a>';
        }

        $catEnum = $catService->getItemsForCategory(_BIGACE_ITEM_IMAGE, $temp->getID());

        $tlink = '';
        if($catEnum->count() > 0)
            $tlink = '<a href="'.createImageBrowserLink(array(IMAGE_PARAM_MODE => IMAGE_MODE_CATEGORY_LISTING, IMAGE_PARAM_CATEGORY_ID => $temp->getID())).'">'.getTranslation('show_linked').'</a>';

        $tpl->setCurrentBlock("row");
        $tpl->setVariable("CSS", $cssClass);
        $tpl->setVariable("CATEGORY_NAME", $name);
        $tpl->setVariable("ACTION_LINKED", $tlink);
        $tpl->setVariable("AMOUNT", $catEnum->count());
        $tpl->parseCurrentBlock("row");
    }

    $tpl->show();
}