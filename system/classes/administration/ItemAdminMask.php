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
 * @package bigace.classes
 * @subpackage administration
 */

import('classes.item.Itemtype');
import('classes.item.ItemService');
import('classes.item.ItemAdminService');
import('classes.right.RightAdminService');
import('classes.language.LanguageEnumeration');
import('classes.workflow.WorkflowService');
import('classes.category.CategoryService');
import('classes.category.CategoryList');
import('classes.util.CMSLink');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');
import('classes.util.html.JavascriptHelper');
import('classes.group.Group');
import('classes.group.GroupService');
import('classes.group.GroupEnumeration');
import('classes.core.ServiceFactory');
import('classes.administration.AdminBoxes');

/**
 * Defines a Admin Item Link to be a separator.
 */
define('ADMIN_LINK_TYPE_SEPARATOR', 'separator');
/**
 * Defines a Admin Item Link to be a Link.
 */
define('ADMIN_LINK_TYPE_LINK', 'adminLink');
/**
 * Defines a Admin Item Link to be a Link.
 */
define('ADMIN_LINK_TYPE_POPUP', 'adminPopup');

/**
 * This class defines methods for the Item Administration Masks.
 * Is not mentioned for public usage!
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class ItemAdminMask
{
    /**
     * @access private
     */
    var $type               = _BIGACE_ITEM_MENU;
    /**
     * @access private
     */
    var $adminService       = NULL;
    /**
     * @access private
     */
    var $rightAdminService  = NULL;
    /**
     * @access private
     */
    var $itemService        = NULL;
    /**
     * @access private
     */
    var $largeMode          = true;

    var $MODE_EDIT_ITEM     = 'editItem';
    var $MODE_EDIT_CATEGORY = 'editCategorys';
    var $MODE_EDIT_RIGHTS   = 'editRights';
    var $MODE_EDIT_VERSIONS = 'editVersions';

    function ItemAdminMask($itemtype)
    {
        $this->init($itemtype);
    }

    function init($itemtype)
    {
        $this->type = new Itemtype($itemtype);
        $this->itemService = new ItemService($itemtype);
    }

    function setLargeMode($isLarge = true) {
        if (is_bool($isLarge))
            $this->largeMode = $isLarge;
    }

    function isLargeMode() {
        return $this->largeMode;
    }

    function getItemType()
    {
        return $this->type;
    }

    function getItemTypeID()
    {
        $t = $this->type;
        return $t->getItemTypeID();
    }

    /**
     * @access private
     */
    function getItemService()
    {
        return $this->itemService;
    }

    /**
     * @access private
     */
    function getItemAdminService()
    {
        if ($this->itemAdminService == NULL) {
            $this->itemAdminService = new ItemAdminService($this->getItemTypeID());
        }
        return $this->itemAdminService;
    }

    /**
     * @access private
     */
    function getRightAdminService()
    {
        if ($this->rightAdminService == NULL) {
            $this->rightAdminService = new RightAdminService($this->getItemTypeID());
        }
        return $this->rightAdminService;
    }

    /**
     * @access private
     */
    function getItem($id, $langid)
    {
        $s = $this->getItemService();
        return $s->getClass($id,ITEM_LOAD_FULL,$langid);
    }

    function getRightService()
    {
        return $GLOBALS['RIGHT_SERVICE'];
    }

    /**
     * @access private
     */
    function getItemRight($id)
    {
        $rservice = $this->getRightService();
        return $rservice->getItemRight($this->getItemTypeID(), $id, $GLOBALS['_BIGACE']['SESSION']->getUserID());
    }

    function getHeader($item) {
        ?>
        <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/toolbar.js"></script>
        <script type="text/javascript">
        var styleImageDir = "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>";
        <?php
        if ($this->getItemTypeID() == _BIGACE_ITEM_MENU) {
			import('classes.util.links.EditorLink');
            ?>
			function editorByName(editorName) {
				try {
				    <?php
				        $editorLink = new EditorLink();
				        $editorLink->setItemID($item->getID());
				        $editorLink->setLanguageID($item->getLanguageId());
				        $editorLink->setEditor('"+editorName+"');
				    ?>
				    location.href = "<?php echo LinkHelper::getUrlFromCMSLink($editorLink); ?>";
				} catch(ex) {
				    alert('Error in editorByName: ' + ex);
				}
			}

        <?php
		}

            ?>

            function setMenu(id, language, name)
            {
                document.getElementById('parentid').value = id;
                document.getElementById('parentName').innerHTML = name;
                document.getElementById('moveButton').disabled = false;
            }
        </script>

        <?php
            $link = new MenuChooserLink();
            $link->setItemID(_BIGACE_TOP_LEVEL);
            echo JavascriptHelper::createJSPopup('parentSelector', 'SelectParent', '400', '350', LinkHelper::getUrlFromCMSLink($link), array('formName', 'hideTree'), 'yes');
        echo "\n";
    }

    /**
     * Return whether the current Itemtype is Tree-based or not.
     */
    function isTree() {
        return FALSE;
    }

    function supportUpload() {
        return TRUE;
    }

    function getTableWidth() {
        return ADMIN_MASK_WIDTH_LARGE;
    }

    function displayBackLink($item, $text = '')
    {
        echo createBackLink($GLOBALS['MENU']->getID(), array('data[id]' => _BIGACE_TOP_LEVEL), $text);
    }

    function createLink($params) {
        if($this->isLargeMode())
            return createAdminLink($GLOBALS['MENU']->getID(), $params);
        else
            return createAdminLink($GLOBALS['MENU']->getID(), $params, '', 'simple');
    }

    function getAdminLinkArray($item)
    {
        import('classes.util.links.ItemInfoLink');
        import('classes.item.ItemHistoryService');
        $link = new ItemInfoLink();
        $link->setInfoItemtype($item->getItemType());
        $link->setInfoItemID($item->getID());
        $link->setInfoItemLanguage($item->getLanguageID());
        $itemInfoLink = LinkHelper::getUrlFromCMSLink($link);

        $tempLanguage = new Language($item->getLanguageID());
        $links = array();
/*
        array_push($links, array(
                         'type'     => ADMIN_LINK_TYPE_LINK,
                         'name'     => getTranslation('admin'),
                         'image'    => 'languages/'. $tempLanguage->getLocale() . '.gif',
                         'link'     => $this->createLink(array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), _PARAM_ADMIN_MODE => _MODE_EDIT_ITEM))
        ));
*/
        if ($item->getItemTypeID() == _BIGACE_ITEM_MENU)
        {
            if($GLOBALS['FRIGHT_SERVICE']->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'edit.portlet.settings'))
            {
                import('classes.util.links.PortletAdminLink');
                $link = new PortletAdminLink();
                $link->setItemID($item->getID());
                $link->setLanguageID($item->getLanguageID());
                $menuPortletLink = LinkHelper::getUrlFromCMSLink($link);

                array_push($links, array(
                                 'type'     => ADMIN_LINK_TYPE_POPUP,
                                 'name'     => 'Portlets',
                                 'image'    => 'portlets.png',
                                 'link'     => $menuPortletLink,
								 'width'	=> '700',
								 'height'	=> '500',

                ));
            }
        }
        array_push($links, array(
                         'type'     => ADMIN_LINK_TYPE_LINK,
                         'name'     => getTranslation('item_category'),
                         'image'    => 'category_link.png',
                         'link'     => $this->createLink(array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), _PARAM_ADMIN_MODE => 'changecategory'))
        ));
        array_push($links, array(
                         'type'     => ADMIN_LINK_TYPE_LINK,
                         'name'     => getTranslation('rights'),
                         'image'    => 'rights.png',
                         'link'     => $this->createLink(array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), _PARAM_ADMIN_MODE => 'showUserRights'))
        ));
        $ihs = new ItemHistoryService($item->getItemtypeID());
        if ($ihs->countHistoryVersions($item->getID(), $item->getLanguageID()) > 0) {
            array_push($links, array(
                     'type'     => ADMIN_LINK_TYPE_LINK,
                     'name'     => getTranslation('history_versions_link'),
                     'image'    => 'history.png',
                     'link'     => $this->createLink(array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), _PARAM_ADMIN_MODE => _MODE_DISPLAY_HISTORY))
            ));
        }
        array_push($links, array(
                         'type'     => ADMIN_LINK_TYPE_POPUP,
                         'name'     => getTranslation('object_infos_link'),
                         'image'    => 'info.png',
                         'link'     => $itemInfoLink,
						 'width'	=> '600',
						 'height'	=> '420',
                        )
        );
        // preview link
        $previewLink = LinkHelper::getCMSLinkFromItem($item);
        if ($item->getItemTypeID() != _BIGACE_ITEM_MENU)
            $previewLink->setFileName($item->getOriginalName());

        array_push($links, array(
                         'type'     => ADMIN_LINK_TYPE_LINK,
                         'name'     => getTranslation('preview'),
                         'image'    => 'preview.png',
                         'link'     => LinkHelper::getUrlFromCMSLink($previewLink)
                        )
        );

        return $links;
    }

    function displayBackLink2($item)
    {
        if ($this->getItemTypeID() == _BIGACE_ITEM_MENU) {
            $id = $item->getParentID();
            if ($item->getID() == _BIGACE_TOP_LEVEL || $item->hasChildren())
                $id = $item->getID();
            echo createBackLink( $GLOBALS['MENU']->getID(), array('data[id]' => $id, 'data[langid]' => $item->getLanguageID()) );
        } else {
            echo createBackLink( $GLOBALS['MENU']->getID(), array('data[id]' => _BIGACE_TOP_LEVEL, 'data[langid]' => $item->getLanguageID()) );
        }
    }

    function getToolbar($item) {
        $lang = array();
        $s = $this->getItemService();
        $ile = $s->getItemLanguageEnumeration($item->getID());

        ?>
            <table class="toolbarTable" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="center">
                        <script type="text/javascript">
                        <!--
                            var toolbar = new TreeActionToolbar();
            <?php

        for ($i=0; $i < $ile->count(); $i++)
        {
            $tempLanguage = $ile->next();
            // for later use in create language drop down
            array_push($lang, $tempLanguage->getID());
            ?>
                toolbar.add( new TreeActionLink('adminMenuLang<?php echo $tempLanguage->getID(); ?>','languages/<?php echo $tempLanguage->getLocale(); ?>.gif','location.href=\'<?php echo createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE=>_MODE_EDIT_ITEM, 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'data[id]' => $item->getID())); ?>\'', '<?php echo getTranslation('preferences') . ': '.$tempLanguage->getName(ADMIN_LANGUAGE); ?>') );
            <?php
            if ($item->getLanguageID() != $tempLanguage->getID()) { ?>
                toolbar.add( new TreeActionLink('removeMenuLang<?php echo $tempLanguage->getID(); ?>','delete.png','if (confirm(\'<?php echo getTranslation('confirm_delete_item_language'); ?>\')) { location.href=\'<?php echo createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_LANGUAGE, 'data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), 'data[langidtodelete]' => $tempLanguage->getID())); ?>\' } ', '<?php echo getTranslation('delete') . ': '.$tempLanguage->getName(ADMIN_LANGUAGE); ?>') );
            <?php
            }

            echo "\ntoolbar.add( new TreeActionSpacerHorizontal() );\n";

        }

        // alle action links
            $i = 0;
            foreach($this->getAdminLinkArray($item) AS $linkDef) {
                if($linkDef['type'] == ADMIN_LINK_TYPE_LINK) {
                    echo "
                    toolbar.add( new TreeActionLink('adminMenu".$i."','".$linkDef['image']."','location.href=\'".$linkDef['link']."\'', '".$linkDef['name']."') );
                    ";
                    $i++;
                }
				else if($linkDef['type'] == ADMIN_LINK_TYPE_POPUP) {
                    echo "
                    toolbar.add( new TreeActionLink('adminMenu".$i."','".$linkDef['image']."','popup(\'".$linkDef['link']."\',\'adminMenu".$i."\',\'".$linkDef['width']."\',\'".$linkDef['height']."\')', '".$linkDef['name']."') );
                    ";
                    $i++;
				}
            }

        // create language
        $languages = array();
        $langEnum = new LanguageEnumeration();
        for ($i=0;$i<$langEnum->count();$i++)
        {
            $tempLang = $langEnum->next();
            if (!in_array($tempLang->getID(), $lang)) {
                $languages[$tempLang->getName()] = $tempLang->getID();
            }
        }

        if (count( $languages ) > 0 )
        {
            $tempName = (isset($data['name'])) ? $data['name'] : $item->getName();
            $elem = '<form id="createLangForm" style="display:inline" method="POST" action="'.createAdminLink($GLOBALS['MENU']->getID()).'">
                    <input type="hidden" name="mode" value="'._MODE_CREATE_LANGUAGE.'">
                    <input type="hidden" name="data[id]" value="'.$item->getID().'">
                    <input type="hidden" name="data[copyLangID]" value="'.$item->getLanguageID().'">
                    <input type="hidden" name="data[name]" value="'.$this->prepareTextInputValue($tempName).'">'.createSelectBox('langid', $languages, '').'
                    </form>';
            ?>
            toolbar.add( new TreeActionSpacerHorizontal() );
            toolbar.add( new TreeActionHtmlElement('<?php echo str_replace("\n", '', $elem); ?>', '<?php echo getTranslation('create'); ?>') );
            toolbar.add( new TreeActionLink('createLangVersion','item_<?php echo $this->getItemTypeID(); ?>_new.png','document.getElementById(\'createLangForm\').submit()', '<?php echo getTranslation('create'); ?>') );
            <?php
        }

        if ($this->getItemTypeID() == _BIGACE_ITEM_MENU)
		{
			import('classes.administration.EditorHelper');

            echo "\ntoolbar.add( new TreeActionSpacerHorizontal() );\n";
            $editElem = getTranslation('edit') . ':
			<select name="editor" onChange="editorByName(this.options[this.selectedIndex].value)">
				<option value=""></option>';
			$editors = bigace_get_all_editor();
			foreach($editors AS $ee) {
				$editElem .= '<option value="'.$ee.'">'.$ee.'</option>';
			}
			$editElem .= '</select>';
			?>
            toolbar.add( new TreeActionHtmlElement('<?php echo str_replace("\n", '', $editElem); ?>', '<?php echo getTranslation('edit'); ?>') );
			<?php
		}

        if ($item->getParentID() != _BIGACE_TOP_PARENT)
        {
            $parent = $GLOBALS['_SERVICE']->getClass($item->getParentID());
            import('classes.util.html.JavascriptHelper');

            echo "\ntoolbar.add( new TreeActionSpacerHorizontal() );\n";

            $moveElem = '
            <form style="display:inline" name="moveMenuForm" id="moveMenuForm" action="' . createAdminLink($GLOBALS['MENU']->getID()) . '" method="POST">
            <input type="hidden" name="mode" value="' .  _MODE_MOVE_ITEM . '">
            <input type="hidden" name="data[langid]" value="' . $item->getLanguageID() . '">
            <input type="hidden" name="data[id]" value="' . $item->getID() . '">
            <input type="hidden" name="data[parentid]" id="parentid" value="">
            <span style="color:#666666;font-style:italic;width:110px" id="parentName">
            ' . getTranslation('please_choose') . '</span> <img src="' . $GLOBALS['_BIGACE']['style']['DIR'] . 'folder_in.png" value="' . getTranslation('choose') . '" title="' . getTranslation('submenu_of') . '" onclick="parentSelector(\\\'moveMenuForm\\\', \\\'' . $parent->getID() . '\\\')" style="cursor:pointer;border-width:0px;">
                                <input type="submit" id="moveButton" disabled="disabled" value="' . getTranslation('move') . '">
            </form>';
            ?>
            toolbar.add( new TreeActionHtmlElement('<?php echo str_replace("\n", '', $moveElem); ?>', '<?php echo getTranslation('move'); ?>') );
            <?php
        }

?>
                            document.write( toolbar.toString() );

                        // -->
                        </script>
                    </td>
                </tr>
            </table>
        <?php
    }

    /**
     * Used for 1024*768 and larger.
     * @access private
     */
    function editItemSized($id, $langid, $mode, $options = null)
    {
        if ($this->checkWriteRight($id))
        {
            if($options === null) {
                $options = array();
            }
            $item = $this->getItem($id, $langid);
            ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <?php
            if($this->isLargeMode())
            {
                ?>
	            <tr>
	                <td valign="top">
	                    <?php
	                        $this->displayBackLink2($item);
	                    ?>
	                </td>
	            </tr>
	            <?php
            }

            if(isset($options['title']))
            {
                ?>
                <tr>
                    <td valign="top" align="left">
                        <h3><?php echo $options['title']; ?></h3>
                    </td>
                </tr>
                <?php
            }

            if(isset($options['toolbar']) && $options['toolbar'] === true)
            {
            ?>
            <tr>
                <td valign="top" align="left">
                    <?php $this->getToolbar($item); ?>
                </td>
            </tr>
                <?php
            }
            ?>
            <tr>
                <td valign="top" align="left">
                    <?php
                    switch ($mode)
                    {
                        case $this->MODE_EDIT_CATEGORY:
                            $this->categoryForm($item);
                            break;
                        case $this->MODE_EDIT_RIGHTS:
                            $this->showChangeUsersRightMask($item);
                            break;
                        case $this->MODE_EDIT_VERSIONS:
                            $this->createHistoryVersionMask($item);
                            break;
                        default:
                            $boxes = AdminBoxes::get();

                            echo $boxes->getPageHeader();

                            $this->displayEditItemFormular($item);
                            echo '<br/>';

                            if ($this->supportUpload()) {
                                echo '<br/>';
                                $this->displayReplaceWithUploadFormular($item);
                            }

                            $allMeta = Hooks::apply_filters('edit_item_meta', array(), $item);

                            if(is_array($allMeta) && count($allMeta) > 0)
                            {
                                foreach($allMeta as $metaTitle => $boxContent) {
                                    $params = array('title' => $metaTitle, 'closed' => true);
                                    echo $boxes->getBoxHeader($params);
                                    echo $boxContent;
                                    echo $boxes->getBoxFooter($params);
                                }
                            }

                            echo $boxes->getPageFooter();

                            break;
                    }
                    ?>
                </td>
            </tr>
            </table>
            <?php
        }
    }

    /**
     * Pass the mode, for your required mask.
     *
     * Allowed values are:
     * - MODE_EDIT_ITEM
     * - MODE_EDIT_CATEGORY
     * - MODE_EDIT_RIGHTS
     * - MODE_EDIT_VERSIONS
     */
    function editItem($id, $langid, $mode = '', $options = null)
    {
        if($options === null || !is_array($options)) {
            $options = array();
        }
        if(!isset($options['toolbar'])) {
            $options['toolbar'] = true;
        }

        if ($mode == '') {
            $mode = $this->MODE_EDIT_ITEM;
        }

        if ($this->checkWriteRight($id))
        {
            $item = $this->getItem($id, $langid);
            $this->getHeader($item);
            $this->editItemSized($id, $langid, $mode, $options);
        }
    }

    /**
     * Checks if the current User has Write rights on the Item.
     * If not a Exception will be processed and FALSE is returned.
     * Otherwise TRUE is returned.
     */
    function checkWriteRight($id)
    {
        $right = $this->getItemRight($id);
        if ($right->canWrite()) {
            return true;
        }

        import('classes.exception.NoWriteRightException');
        ExceptionHandler::processAdminException( new NoWriteRightException('Missing permission to edit this Item.') );
        return false;
    }

    /**
     * This is the Standard Form where we Edit the Items settings.
     * It can be used for general Items that need no customizing!
     */
    function displayEditItemFormular($item)
    {
        $temp_right = $this->getItemRight($item->getID());
        if ($temp_right->canWrite())
        {
            $tempLanguage       = new Language($item->getLanguageID());
            $new['language']    = $tempLanguage->getName() . ' <img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR']. 'languages/' . $tempLanguage->getLocale() . '.gif" class="langFlag">';
            unset ( $tempLanguage );

            $config = array(
                        'title'         =>  getTranslation('edit_file_data'),
                        'size'          =>  array('left' => '170px'),
                        'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                        'form_method'   =>  'post',
                        'form_hidden'   =>  array(
                                                _PARAM_ADMIN_MODE              => _MODE_SAVE_ITEM,
                                                'data[id]'          => $item->getID(),
                                                'data[langid]'      => $item->getLanguageID(),
                                                'data[workflow]'    => '',
                                        ),
                        'entries'       =>  array(
                                                getTranslation('language')     => $new['language'],
                                                getTranslation('name')         => createTextInputType('name', $this->prepareTextInputValue($item->getName()), 200),
                                                getTranslation('unique_name')  => createTextInputType('unique_name', $this->prepareTextInputValue($item->getUniqueName()), 250),
                                                getTranslation('mimetype')     => createTextInputType('mimetype', $item->getMimetype(), 100),
                                                getTranslation('catchwords')   => createTextInputType('catchwords', $this->prepareTextInputValue($item->getCatchwords()), 200),
                                                getTranslation('description')  => createTextArea('description', $this->prepareTextInputValue($item->getDescription()), 5, 40)
                                            ),
                        'form_submit'   =>  true,
                        'submit_label'  =>  getTranslation('save')
            );
            echo createTable($config);
            unset($config);
        }
        else
        {
            loadClass('exception', 'NoWriteRightException');
            ExceptionHandler::processAdminException( new NoWriteRightException('No sufficient rights to display edit Item Formular!') );
        }
    }


    function displayLanguageVersionFormularLarge($item)
    {
        $lang = array();
        $s = $this->getItemService();
        $ile = $s->getItemLanguageEnumeration($item->getID());

        echo '<table width="100%" cellspacing="1" style="border:1px solid #000000;background-color:#ffffff">';
        echo '<tr><td align="left">';
        echo '<table border="0">';
        echo '<tr><td colspan="2"><b>' . getTranslation('language_versions') . '</b></td></tr>';

        for ($i=0; $i < $ile->count(); $i++)
        {
            $tempLanguage = $ile->next();
            // for later use in create language drop down
            array_push($lang, $tempLanguage->getID());

            echo '<tr><td align="left"';
            if ($item->getLanguageID() == $tempLanguage->getID()) {
                echo ' colspan="2"';
            }
            echo '>';

            $tempName = '<img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale().'.gif" class="langFlag">' . $tempLanguage->getName();

            if ($item->getLanguageID() != $tempLanguage->getID()) {
                $tempName = '<a title="'.getTranslation('preferences').'" href="' . createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE=>_MODE_EDIT_ITEM, 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'data[id]' => $item->getID())) . '">' . $tempName . '</a>';
            }

            echo $tempName . '</td>';

            if ($item->getLanguageID() != $tempLanguage->getID()) {
                echo '<td align="right"><a title="'.getTranslation('delete').'" onclick="return confirm(\''.getTranslation('confirm_delete_item_language', 'Really delete this language version?').'\')" href="'.createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_LANGUAGE, 'data[id]' => $item->getID(), 'data[langidtodelete]' => $tempLanguage->getID())).'">';
                echo '<img alt="'.getTranslation('delete').'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'delete.png"></a></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '</td></tr>';

        $languages = array();
        $langEnum = new LanguageEnumeration();
        for ($i=0;$i<$langEnum->count();$i++)
        {
            $tempLang = $langEnum->next();
            if (!in_array($tempLang->getID(), $lang)) {
                $languages[htmlentities($tempLang->getName())] = $tempLang->getID();
            }
        }

        if (count( $languages ) > 0 )
        {
            echo '<tr><td align="left">';
            echo '<table style="margin:0px;padding:0px;" border="0">';
            $tempName = (isset($data['name'])) ? $data['name'] : $item->getName();
            echo '<form method="POST" action="'.createAdminLink($GLOBALS['MENU']->getID()).'">';
            echo '<input type="hidden" name="mode" value="'._MODE_CREATE_LANGUAGE.'">';
            echo '<input type="hidden" name="data[id]" value="'.$item->getID().'">';
            echo '<input type="hidden" name="data[copyLangID]" value="'.$item->getLanguageID().'">';
            echo '<tr><td>&nbsp;</td></tr>';
            echo '<tr><td><b>'.getTranslation('create').'</b></td></tr>';
            echo '<tr><td><input type="text" name="data[name]" value="'.$this->prepareTextInputValue($tempName).'" style="width:110px" maxlength="200"></td></tr>';
            echo '<tr><td>'.createSelectBox('langid', $languages, '').' <input style="border-width:0px" type="image" title="'.getTranslation('create').'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'item_'.$this->getItemTypeID().'_new.png"></td></tr>';
            echo '</form>';
            echo '</table>';
            echo '</td></tr>';
        }

        echo '</table>';
    }

    /**
     * This function shows the Form where the Gruop Rights for an Item can be changed.
     *
     * @access private
     */
    function showChangeUsersRightMask($item)
    {
        if ($this->checkWriteRight($item->getID()))
        {
	        $smarty = getAdminSmarty();

            $linkCreateRight = createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_CREATE_RIGHT));
            $linkChangeRight = createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_CHANGE_RIGHT));
            $linkDeleteRight = createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_RIGHT));

            $itemtype = $this->getItemTypeID();
            $rightService = $this->getRightService();
            $temp_right_user = $rightService->getItemRight($itemtype, $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());

            // Find out which rights currenty exists
            $right_info = $rightService->getItemRightEnumeration($itemtype, $item->getId());
            $existing_rights = array();
            // build list of user ids for later comparison
            for ($i = 0; $i < $right_info->countRights(); $i++)
            {
                $temp_right = $right_info->getNextRight();
                $temp_group = new Group($temp_right->getGroupID());
                array_push($existing_rights, $temp_group->getID());
            }

            // keep everything inside for smarty usage
            $allPermissions = array();

            // Build user list for all these who currently do not have a right
            $temp_group_info = new GroupEnumeration();
            for ($i=0; $i < $temp_group_info->count(); $i++)
            {
                $current_group = $temp_group_info->next();
                if (!in_array($current_group->getID(), $existing_rights)) {
                    $groupName = $current_group->getName();
                    $groupID = $current_group->getID();

                    $curPerm = array();
                    $curPerm['CREATE_RIGHT_URL'] = $linkCreateRight;

                    $curPerm["IS_NEW"] = true;
                    $curPerm['GROUP_NAME'] = $groupName;
                    $curPerm['GROUP_ID'] = $groupID;
                    $curPerm["ITEM_ID"] = $item->getId();
                    $curPerm["LANGUAGE_ID"] = $item->getLanguageID();
                    $curPerm["RIGHT_VALUE_READ"] = _BIGACE_RIGHTS_READ;
                    $curPerm["RIGHT_VALUE_WRITE"] = _BIGACE_RIGHTS_RW;
                    $curPerm["RIGHT_VALUE_DELETE"] = _BIGACE_RIGHTS_RWD;

                    $curPerm["BUTTON_STYLE_READ"] = 'permoff';
                    $curPerm["BUTTON_STYLE_WRITE"] = ($temp_right_user->canWrite() ? 'permoff': 'permoff permdeactive');
                    $curPerm["BUTTON_STYLE_DELETE"] = ($temp_right_user->canDelete() ? 'permoff': 'permoff permdeactive');

                    $allPermissions["".$groupID] = $curPerm;
                }
            }

            $right_info = $rightService->getItemRightEnumeration($itemtype, $item->getId());

            $groupService = new GroupService();
            $memberships = $groupService->getMemberships($GLOBALS['_BIGACE']['SESSION']->getUser());

            for ($i = 0; $i < $right_info->countRights(); $i++)
            {
                $temp_right = $right_info->getNextRight();
                $temp_group = new Group($temp_right->getGroupID());

                $curPerm = array();
                $curPerm["DELETE_RIGHT_URL"] = createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_RIGHT, "current_menu" => $item->getId(), "data[id]" => $item->getId(), "data[langid]" => $item->getLanguageID(), "data[group]" => $temp_group->getID()));
                $curPerm["CHANGE_RIGHT_URL"] = $linkChangeRight;

                $curPerm["IS_NEW"] = false;
                $curPerm["GROUP_NAME"] = $temp_group->getName();
                $curPerm["GROUP_ID"] = $temp_group->getID();
                $curPerm["ITEM_ID"] = $item->getId();
                $curPerm["LANGUAGE_ID"] = $item->getLanguageID();
                $curPerm["RIGHT_VALUE_READ"] = $temp_right->getValue() == _BIGACE_RIGHTS_NO  ? _BIGACE_RIGHTS_READ : _BIGACE_RIGHTS_NO;
                $curPerm["RIGHT_VALUE_WRITE"] = $temp_right->getValue() == _BIGACE_RIGHTS_NO || $temp_right->getValue() == _BIGACE_RIGHTS_READ ? _BIGACE_RIGHTS_RW : _BIGACE_RIGHTS_READ;
                $curPerm["RIGHT_VALUE_DELETE"] = $temp_right->getValue() == _BIGACE_RIGHTS_RWD ? _BIGACE_RIGHTS_RW : _BIGACE_RIGHTS_RWD;

                $curPerm["BUTTON_STYLE_READ"] = ($temp_right->getValue() == _BIGACE_RIGHTS_NO ? 'permoff': 'permon');
                if($temp_right_user->canWrite()) {
                    $curPerm["BUTTON_STYLE_WRITE"] = (($temp_right->getValue() == _BIGACE_RIGHTS_NO || $temp_right->getValue() == _BIGACE_RIGHTS_READ) ? 'permoff': 'permon');
                }
                else {
                    $curPerm["BUTTON_STYLE_WRITE"] = (($temp_right->getValue() == _BIGACE_RIGHTS_NO || $temp_right->getValue() == _BIGACE_RIGHTS_READ) ? 'permoff permdeactive': 'permon permdeactive');
                }

                if($temp_right_user->canDelete()) {
                    $curPerm["BUTTON_STYLE_DELETE"] = ($temp_right->getValue() == _BIGACE_RIGHTS_RWD ? 'permon': 'permoff');
                }
                else {
                    $curPerm["BUTTON_STYLE_DELETE"] = ($temp_right->getValue() == _BIGACE_RIGHTS_RWD ? 'permon permdeactive': 'permoff permdeactive');
                }

                $allPermissions["".$temp_group->getID()] = $curPerm;
            }

            // sort alphabetical
            ksort($allPermissions);
	        $smarty->assign('USER_PERM', $temp_right_user);
	        $smarty->assign('ALL_PERMS', $allPermissions);

	        $smarty->display('ItemPermission.tpl');
        }
        else
        {
            ExceptionHandler::processAdminException( new NoWriteRightException('You are not allowed to edit permissions for this item.', createAdminLink($GLOBALS['MENU']->getID())) );
        }
    }


    /**
     * Creates the overview table for all history versions of the selected item and language
     */
    function createHistoryVersionMask($item)
    {
        $temp_right = $GLOBALS['RIGHT_SERVICE']->getItemRight($this->getItemTypeID(), $item->getID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());

        if ($temp_right->canRead())
        {
            $historyService = new ItemHistoryService($item->getItemType());
            // Prepare show Item History
            $itemHasHistory = ($historyService->countHistoryVersions($item->getID(), $item->getLanguageID()) > 0);

            // TODO test why this check was implemented
            if($itemHasHistory && $item->getCommand() != '' && $item->getURL() != '') {
                // here was the javascript block
            }

            // -------------------------------------------------------
            // ------------- [START] History version box -------------
            // show only if any history versions is available
            if ($itemHasHistory)
            {
                $historyEnum = $historyService->getHistoryVersions($item->getID(), $item->getLanguageID());

                // load template
                $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("ItemHistoryVersions.tpl.htm", false, true);

                // set global values
                $tpl->setVariable('SHOW_HISTORY_LINK', createCommandLink($item->getCommand(), $item->getID(), array('language' => $item->getLanguageID(), 'historyID' => '')));
                $tpl->setVariable('TABLE_WIDTH', $this->getTableWidth());
                $tpl->setVariable('FORM_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
                $tpl->setVariable('ITEM_ID', $item->getID());
                $tpl->setVariable('ITEM_LANGUAGE_ID', $item->getLanguageID());
                $tpl->setVariable('ADMIN_MODE', _MODE_DELETE_HISTORY);

                $cssClass = "row1";

                // set each history entry
                for ($i=0; $i < $historyEnum->count(); $i++)
                {
                    $temp = $historyEnum->next();
                    $size = file_exists(ItemHelper::getHistoryURLFull($temp)) ? filesize(ItemHelper::getHistoryURLFull($temp)) : (file_exists($temp->getFullURL()) ? filesize($temp->getFullURL()) : 0);

                    $services = ServiceFactory::get();
                    $PRINCIPALS = $services->getPrincipalService();
                    $last_user = $PRINCIPALS->lookupByID($temp->getLastByID());
                    $lastName = ($last_user != null ? $last_user->getName() : '');

                    $tpl->setCurrentBlock("row") ;
                    $tpl->setVariable("CSS", $cssClass) ;
                    $tpl->setVariable("HISTORY_CHECKBOX", createNamedCheckBox('data[modified]['.$temp->getLastDate().']', $temp->getLastDate(), false, false));
                    $tpl->setVariable('HISTORY_SIZE', $size);
                    $tpl->setVariable('HISTORY_NAME', $temp->getName());
                    $tpl->setVariable('HISTORY_DESCRIPTION', $temp->getDescription());
                    $tpl->setVariable('HISTORY_CATCHWORDS', $temp->getCatchwords());
                    $tpl->setVariable('HISTORY_FILENAME', $temp->getURL());
                    $tpl->setVariable('HISTORY_LAST_DATE_FORMAT', date("Y-m-d H:i:s", $temp->getLastDate()));
                    $tpl->setVariable('HISTORY_LAST_NAME', $lastName);
                    $tpl->setVariable('HISTORY_LAST_DATE', $temp->getLastDate());
                    $tpl->setVariable('HISTORY_DELETE_LINK', createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_HISTORY, "data[id]" => $item->getID(), 'data[modified]['.$temp->getLastDate().']' => $temp->getLastDate(), "data[langid]" => $item->getLanguageID(), "data[version]" => $temp->getLastDate())));

                    $recoverLink = '';
                    if ($temp->getFullURL() != $item->getFullURL()) {
                        $recoverLink = '<a href="'.createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_REFRESH_HISTORY_CONTENT, "data[id]" => $item->getID(), "data[langid]" => $item->getLanguageID(), "data[version]" => $temp->getLastDate())).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'history_recover.png" border="0" alt="'.getTranslation('content', 'Content').'" title="'.getTranslation('content', 'Content').'"></a>';
                    }
                    $tpl->setVariable('HISTORY_RECOVER_LINK', $recoverLink);

                    $tpl->parseCurrentBlock("row") ;

                    $cssClass = ($cssClass == "row1") ? "row2" : "row1";
                }

                $tpl->setVariable('LAST_CSS', $cssClass);
                $tpl->show();
            }
            // ------------- [STOP] History version box --------------
            // -------------------------------------------------------
        }
        else
        {
            loadClass('exception', 'NoReadRightException');
            ExceptionHandler::processAdminException( new NoReadRightException('Not allowed to read history Versions of this Item!') );
        }
    }


    /**
     * @access private
     */
    function categoryForm($item)
    {
        if ($this->checkWriteRight($item->getID()))
        {
            $cat_service = new CategoryService();
            $temp = new CategoryList();
            if ($temp->count() > 0)
            {
                $ti = array();
                for ($i=0; $i < $temp->count(); $i++) {
                    $tb = $temp->next();
                    if (!$cat_service->isItemLinkedToCategory($item->getItemType(), $item->getID(), $tb->getID())) {
                        $ti[$tb->getName()] = $tb->getID();
                    }
                }

                if(count($ti) > 0)
                {
                    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("ItemCategoryAdd.tpl.htm", false, true);
                    $tpl->setVariable('CATEGORY_SELECTOR', createSelectBox('newcat', $ti));
                    $tpl->setVariable('FORM_ACTION', createAdminLink($GLOBALS['MENU']->getID()));
                    $tpl->setVariable('ITEM_ID', $item->getID());
                    $tpl->setVariable('ITEM_LANGUAGE_ID', $item->getLanguageID());
                    $tpl->setVariable('PARAM_ADMIN_MODE', _PARAM_ADMIN_MODE);
                    $tpl->setVariable('ADMIN_MODE', '17');
                    $tpl->show();
                    unset($tpl);
                }
                unset ($ti);

                $ICE = new ItemCategoryEnumeration($item->getItemType(), $item->getID());
                if ($ICE->count() > 0)
                {
                    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("ItemCategoryLinked.tpl.htm", false, true);
                    $cssClass = "row1";

                    while ( $ICE->hasNext() )
                    {
                        $temp = $ICE->next();

                        $tpl->setCurrentBlock("row") ;
                        $tpl->setVariable("CSS", $cssClass) ;
                        $tpl->setVariable('CATEGORY_NAME', $temp->getName());
                        $tpl->setVariable('CATEGORY_ID', $temp->getID());
                        $tpl->setVariable('CATEGORY_DELETE_LINK', createAdminLink($GLOBALS['MENU']->getID(), array('data[id]' => $item->getID(), 'data[langid]' => $item->getLanguageID(), 'data[delcat]' => $temp->getID() ,_PARAM_ADMIN_MODE => '17')));
                        $tpl->parseCurrentBlock("row") ;

                        $cssClass = ($cssClass == "row1") ? "row2" : "row1";
                    }
                    $tpl->show();
                    unset($tpl);
                }
                else {
                    displayMessage( getTranslation('no_category_links') );
                }
            }
            else {
                displayMessage( getTranslation('no_categorys') );
            }
        }
        else {
            ExceptionHandler::processAdminException( new NoWriteRightException('No sufficient rights to change Item Categorys!') );
        }
    }

    /**
     * Display Language Version Admin Formular.
     * The ItemID identifies the Item to display language Versions for, the languageID
     * is used as the language that is currently displayed.
     */
    function displayLanguageVersionFormular($item)
    {
            $new = array();
            $lang = array();
            $s = $this->getItemService();
            $ile = $s->getItemLanguageEnumeration($item->getID());
            for ($i=0; $i < $ile->count(); $i++)
            {
                $tempLanguage = $ile->next();
                array_push($lang, $tempLanguage->getID());
                $tempName = '<img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale().'.gif" class="langFlag">' . $tempLanguage->getName();

                if ($item->getLanguageID() != $tempLanguage->getID()) {
                    $tempName = '<a title="'.getTranslation('preferences').'" href="' . createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE=>_MODE_EDIT_ITEM, 'adminCharset' => $tempLanguage->getCharset(), 'data[langid]' => $tempLanguage->getID(), 'data[id]' => $item->getID())) . '">' . $tempName . '</a>';
                }

                $deleteLang = '';
                if ($item->getLanguageID() != $tempLanguage->getID()) {
                    $deleteLang .= ' <a title="'.getTranslation('delete').'" onclick="return confirm(\''.getTranslation('confirm_delete_item_language', 'Really delete this language version?').'\')" href="'.createAdminLink($GLOBALS['MENU']->getID(), array(_PARAM_ADMIN_MODE => _MODE_DELETE_LANGUAGE, 'data[id]' => $item->getID(), 'data[langidtodelete]' => $tempLanguage->getID())).'">';
                    $deleteLang .= '<img alt="'.getTranslation('delete').'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'delete.png"></a>' . "\n";
                }
                $new[$tempName] = $deleteLang;
                unset($tempName);
                unset($deleteLang);
            }

            $languages = array();
            $langEnum = new LanguageEnumeration();
            for ($i=0;$i<$langEnum->count();$i++)
            {
                $tempLang = $langEnum->next();
                if (!in_array($tempLang->getID(), $lang)) {
                $languages[htmlentities($tempLang->getName())] = $tempLang->getID();
                }
                unset($tempLang);
            }
            unset($langEnum);
            $canCreateLanguage = false;
            if (count( $languages ) > 0 )
            {
                $new['empty'] = 'empty';
                $val = createSelectBox('langid', $languages, '');
                $new[getTranslation('language')]    = $val;
                $canCreateLanguage = true;
                $tempName = (isset($data['name'])) ? $data['name'] : $item->getName();
                $new[getTranslation('name')]        = createTextInputType('name', $tempName, 50);
            }
            unset ($languages);
            unset ($lang);
            unset ($tempName);

            $config = array(
                    'width'         =>  '500',
                    'title'         =>  getTranslation('language_versions'),
                    'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                    'form_method'   =>  'post',
                    'form_name'     =>  'languageForm',
                    'form_hidden'   =>  array(
                                            _PARAM_ADMIN_MODE              => _MODE_CREATE_LANGUAGE,
                                            'data[id]'          => $item->getID(),
                                            'data[copyLangID]'  => $item->getLanguageID()
                                    ),
                    'entries'       =>  $new,
                    'submit_label'  =>  getTranslation('create'),
                    'form_submit'   =>  $canCreateLanguage
            );
            echo createTable($config);
            unset($config);
            unset($new);
            unset($canCreateLanguage);
    }

    function displayReplaceWithUploadFormular($item)
    {
        $boxes = AdminBoxes::get();
        $params = array('title' => getTranslation('updatemode_1'), 'closed' => false);

        echo $boxes->getBoxHeader($params);

        $form_hidden = array(
                        _PARAM_ADMIN_MODE   =>  _MODE_UPDATE_WITH_UPLOAD,
                        'data[id]'          =>  $item->getID(),
                        'data[langid]'      =>  $item->getLanguageID(),
                        //'MAX_FILE_SIZE'     =>  UPLOAD_MAX_SIZE
        );

        echo '<form action="'.createAdminLink($GLOBALS['MENU']->getID()).'" method="post" enctype="multipart/form-data">';

        //echo getTranslation('choose_file');
        echo createFileInput('userfile');

        foreach($form_hidden as $k => $v)
            echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />';

        echo ' <button type="submit" class="">'.getTranslation('process_upload').'</button>';

        echo '</form>';

        echo $boxes->getBoxFooter($params);
    }

    function prepareTextInputValue($str) {
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace("'", '&#039;', $str);
        return stripslashes($str);
    }

    /**
     * Creates a Select Box with all avilable Workflows.
     */
    function createWorkflowSelectBox($name, $preselect)
    {
    	loadLanguageFile('workflow', ADMIN_LANGUAGE);
        import('classes.util.formular.WorkflowSelect');
        import('classes.util.html.EmptyOption');
        $wfSelect = new WorkflowSelect();
        $wfSelect->setPreSelectedID($preselect);
        $wfSelect->setName($name);
        $wfSelect->addOption( new EmptyOption() );
    	return $wfSelect->getHtml();
    }

}

