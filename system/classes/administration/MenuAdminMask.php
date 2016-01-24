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

import('classes.administration.ItemAdminMask');
import('classes.modul.ModulService');
import('classes.layout.LayoutService');
import('classes.util.formular.DesignSelect');

/**
 * This class defines methods for the Menu Administration Masks.
 * It overrides some methods from the general Item Administration and extends it with menu specific functions.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class MenuAdminMask extends ItemAdminMask
{

    function MenuAdminMask()
    {
        $this->init(_BIGACE_ITEM_MENU);
    }

    function isTree() {
        return TRUE;
    }

    function supportUpload() {
        return FALSE;
    }

    function displayBackLink($item, $text = '')
    {
        $backLinkId = _BIGACE_TOP_LEVEL;
        // switch back link id if we are a menu
        if ($item->hasChildren() || $item->getID() == _BIGACE_TOP_LEVEL) {
            $backLinkId = $item->getID();
        } else {
            $backLinkId = $item->getParentID();
        }

        echo createBackLink($GLOBALS['MENU']->getID(), array('data[id]' => $backLinkId));
        unset($backLinkId);
    }


    function getHiddenOrShown($isHidden)
    {
    	return 
    		createRadioButton('num_3', FLAG_NORMAL, !$isHidden, 'hiddenMenuOff') .
    		' <label for="hiddenMenuOff" style="cursor:pointer">' . getTranslation('display_menu') . '</label> &nbsp;&nbsp; ' . 
            createRadioButton('num_3', FLAG_HIDDEN, $isHidden, 'hiddenMenuOn') .
    		' <label for="hiddenMenuOn" style="cursor:pointer">'.getTranslation('hidden_menu') . '</label> '
    		;
    }

	/**
	 * Returns the HTML Code for a Sleect Bo to choose the Design. 
	 * Switches the return value, depending on the Systems setting (Smarty enabled or not). 
	 */
    function createLayoutSelectBox($name, $preselect, $disabled = false)
    {
        if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
            return $this->createSmartySelectBox($name, $preselect, $disabled);
        }
        return $this->createTemplateSelectBox($name, $preselect, $disabled);
    }

	/**
	 * @access private
	 */
    function createTemplateSelectBox($name, $preselect, $disabled = false)
    {
        $val = "";
        // ------------ Create Select Box ------------
        $temp = array();
        $TEMP_SERVICE = new LayoutService();
        $names = $TEMP_SERVICE->getDefinitionNames();
        $a = count($names);
        for($i = 0; $i < $a; $i++)
        {
            $temp_mod = new Layout($names[$i]);
            if ($temp_mod->isPublic()) {
                $temp[htmlentities($temp_mod->getTitle())] = $temp_mod->getName();
            }
        }
        unset ($TEMP_SERVICE);
        $temp = array_flip($temp);
        arsort($temp);
        reset($temp);
        $temp = array_flip($temp);
        if (count($temp) > 1) {
            $val = createSelectBox($name, $temp, $preselect,'',$disabled);
        } else {
            foreach($temp AS $key => $val) {
                $val = '<input type="hidden" name="'.$name.'" value="'.$val.'"><p>'.$key.'</p>';
            }
        }
        unset ($temp);
        // ----------------------------------------------------
        return $val;
    }
    
	/**
	 * @access private
	 */
    function createSmartySelectBox($name, $preselect, $disabled = false)
    {
        $selector = new DesignSelect();
        $selector->setPreselected($preselect);
        $selector->setName('data['.$name.']');
        return $selector->getHtml();
    }

    /**
     * Still used by the classical menu administration.
     * Will be removed further or later.
     *
     * @deprecated 
     */
    function displayEditItemFormular($item)
    {
        $new = array();

        $tempLanguage       = new Language($item->getLanguageID());
        $new['language']    = $tempLanguage->getName() . ' <img alt="'.$tempLanguage->getName().'" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$tempLanguage->getLocale() . '.gif"> ';
        unset ( $tempLanguage );

        // we do not need the position anymore, it is calculated automatically
        //$new['position']    = createTextInputType('num_4', $item->getPosition() , 50);

        $hiddenOrShown = $this->getHiddenOrShown($item->isHidden());
        $displayInfo = ' <img onmouseover="tooltip(\''.getTranslation('hidden_description').'\')" onMouseOut="nd();" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'info.png" border="0">';

        // the modul select box
        import('classes.util.formular.ModulSelect');
        $modSelect = new ModulSelect();
        $modSelect->setModulLanguage(ADMIN_LANGUAGE);
        $modSelect->setPreSelectedID($item->getModulID());
        $modSelect->setName('data['._BIGACE_COLUMN_MODUL_ID.']');
        $modSelect->setShowPreselectedIfDeactivated(true);

        $layout = $this->createLayoutSelectBox(_BIGACE_COLUMN_LAYOUT_ID, $item->getLayoutName());

        $config = array(
                    'size'          =>  array('left' => '170px'),
                    'title'         =>  $new['language'], //getTranslation('edit_page_settings'),
                    'form_name'     =>  'MenuValues',
                    'form_action'   =>  createAdminLink($GLOBALS['MENU']->getID()),
                    'form_method'   =>  'post',
                    'form_hidden'   =>  array(
                                            'mode'              => _MODE_SAVE_ITEM,
                                            'data[id]'          => $item->getID(),
                                            'data[langid]'      => $item->getLanguageID(),
                                            'data[parentid]'    => $item->getParentID()
                                    ),
                    'entries'       =>  array(
                                            //getTranslation('language')      => $new['language'],
                                            //'&nbsp;'                        => 'empty',
                                            getTranslation('name')          => createTextInputType('name', $this->prepareTextInputValue($item->getName()), 250),
                                            getTranslation('unique_name')   => createTextInputType('unique_name', $this->prepareTextInputValue($item->getUniqueName()), 250),
                                            getTranslation('catchwords')    => createTextInputType('catchwords', $this->prepareTextInputValue($item->getCatchwords()), 200),
                                            getTranslation('description')   => createTextArea('description', $this->prepareTextInputValue($item->getDescription()), 5, 40),
                                            'empty'                         => '',
                                            getTranslation('modul')         => $modSelect->getHtml(),
                                            getTranslation('layout')        => $layout,
                                            getTranslation('menu_workflow') => $this->createWorkflowSelectBox('data[workflow]', $item->getWorkflowName()),
                                            '&nbsp;'                         => 'empty',
                                            getTranslation('display_state') . $displayInfo => $hiddenOrShown,
                                    ),
                    'form_submit'   =>  true,
                    'submit_label'  =>  getTranslation('save')
        );
        echo createTable($config);
    }
    
}

