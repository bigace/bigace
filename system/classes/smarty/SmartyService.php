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
 * @subpackage template
 */

import('classes.smarty.SmartyTemplate');
import('classes.smarty.SmartyDesign');
import('classes.smarty.SmartyStylesheet');
import('classes.item.ItemHelper');

/**
 * The SmartyService is capable for writing and reading Smarty Objects. 
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage smarty
 */
class SmartyService
{
	/**
	 * @access private
	 */
	function deleteHistory($type, $name)
	{
		$values = array('NAME' 		=> $name,
						'CID' 		=> _CID_,
						'TYPE'		=> $type);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_history_delete');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);		
	}

	/**
	 * @access private
	 */
	function createHistory($type, $name, $content)
	{
		$values = array('NAME' 		=> $name,
						'CID' 		=> _CID_,
						'CONTENT'	=> $content,
						'TIMESTAMP' => time(),
						'USERID'	=> $GLOBALS['_BIGACE']['SESSION']->getUserID(),
						'TYPE'		=> $type
				  );
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_history_insert');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);		
	}

	/**
	 * Counts how often a Template is used in Designs.
	 * @return int the amount of how often the Template is in use 
	 */
	function countTemplateUsage($name)
	{
		$values = array('TEMPLATE' => $name,
						'CID'      => _CID_);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_count_usage');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
		$tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		$tpl = $tpl->next();
		return $tpl['amount'];		
	}

	/**
	 * Counts how often a Stylesheet is used in Designs.
	 * @return int the amount of how often the Stylesheet is in use 
	 */
	function countStylesheetUsage($name)
	{
		$values = array('STYLESHEET' => $name,
						'CID'      	=> _CID_);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_count_usage');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
		$tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		$tpl = $tpl->next();
		return $tpl['amount'];		
	}	
	/**
	 * Counts how often a Design is used by Menus.
	 * @return int the amount of how often the Design is in use 
	 */
	function countDesignUsage($name)
	{
		$values = array('DESIGN' => $name,
						'CID'    => _CID_);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_count_usage');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
		$tpl = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		$tpl = $tpl->next();
		return $tpl['amount'];		
	}
	
	/**
	 * Creates a valid (and useful) Filename from any String.
	 * It cuts off all Character except:
	 * a - z
	 * A - Z
	 * 0 - 9
	 * and the two separator character '_' and '-'. 
	 */
	function parseName($name) {
		return trim(str_replace(" ", "", preg_replace("/[^a-zA-Z0-9_-\\s]/", "", $name)));
	}
	
	/**
	 * Creates a new Design.
	 */
	function createDesign($name,$description,$template,$stylesheet,$portlets) 
	{
		$values = array('TEMPLATE' 		=> $template,
						'STYLESHEET'	=> $stylesheet,
						'NAME'			=> $name,
						'DESCRIPTION'	=> $description,
						'PORTLETS'		=> ($portlets == true ? 1 : 0) ); 
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_insert');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);		
	}
	
	/**
	 * Creates a new Template.
	 * The Filename is built by calling <code>SmartyService::parseName($name)</code>.
	 */
	function createTemplate($name,$description,$content,$include = false,$inwork= true) 
	{
		$values = array('INWORK' 		=> ((is_bool($inwork) && $inwork) ? '1' : '0'),
						'INCLUDE'		=> ((is_bool($include) && $include) ? '1' : '0'),
						'NAME'			=> $name,
						'DESCRIPTION'	=> $description,
						'FILENAME'		=> urlencode( $this->parseName($name) . '.tpl' ), 
						'CONTENT'		=> $content,
						'TIMESTAMP' 	=> time(),
						'USERID'		=> $GLOBALS['_BIGACE']['SESSION']->getUserID()
						);
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_insert');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);		
	}

	/**
	 * Creates a new Stylesheet.
	 * The Filename is built by calling <code>SmartyService::parseName($name)</code>.
	 */
	function createStylesheet($name,$description,$content,$editorcss) 
	{
		$filename = $this->parseName($name);
		$filename = urlencode($filename.'.css');	
		$fullUrl = $GLOBALS['_BIGACE']['DIR']['stylesheets'] . $filename;

		$values = array('NAME'			=> $name,
						'DESCRIPTION'	=> $description,
						'FILENAME'		=> $filename,
						'EDITORCSS'		=> $editorcss); 

        if (ItemHelper::checkFile($fullUrl)) {
            if (ItemHelper::saveContent($fullUrl, $content)) {
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_insert');
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
				return $GLOBALS['_BIGACE']['SQL_HELPER']->insert($sqlString);		
            } else {
	        	$GLOBALS['LOGGER']->logError('Check your access rights, File ('.$fullUrl.') could not be written!');
            }
        } else {
        	$GLOBALS['LOGGER']->logError('Check your access rights, File ('.$fullUrl.') is not writeable!');
        }
        return false;
	}

	/**
	 * Name is not updateable yet!
	 */
	function updateTemplate($name,$description,$content,$inwork,$include) 
	{
		$template = new SmartyTemplate($name);	

		$this->createHistory('template', $name, $template->getContent());

		$values = array('INWORK' 		=> ((is_bool($inwork) && $inwork) ? '1' : '0'),
						'INCLUDE'		=> ((is_bool($include) && $include) ? '1' : '0'),
						'NAME'			=> $name,
						'DESCRIPTION'	=> $description,
						'CONTENT'		=> $content,
						'TIMESTAMP' 	=> time(),
						'USERID'		=> $GLOBALS['_BIGACE']['SESSION']->getUserID()
					); 
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_update');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);		
	}

	/**
	 * Name is not updateable yet!
	 */
	function updateStylesheet($name,$description,$content,$editorcss) 
	{
		$design = new SmartyStylesheet($name);	
		$fullUrl = $design->getFullFilename();

		$this->createHistory('stylesheet', $name, file_get_contents($fullUrl));

        if (ItemHelper::checkFile($fullUrl)) {
            if (ItemHelper::saveContent($fullUrl, $content)) {
				$values = array('NAME'			=> $name,
								'DESCRIPTION'	=> $description,
								'EDITORCSS'		=> $editorcss); 
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_update');
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
				return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);		
            }
        }
       	$GLOBALS['LOGGER']->logError('Check your access rights, File ('.$fullUrl.') is not writeable!');
        return false;
	}
	
	/**
	 * Name is not updateable!
	 */
	function updateDesign($name,$description,$template,$stylsheet,$portlets) 
	{
		$values = array('STYLESHEET' 	=> $stylsheet,
						'TEMPLATE'		=> $template,
						'NAME'			=> $name, 
						'DESCRIPTION'	=> $description, 
						'PORTLETS'		=> ($portlets == true ? 1 : 0)
		); 

		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_update');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);		
	}

	/**
	 * Delete the given Template.
	 */
	function deleteTemplate($name)
	{
		if($this->countTemplateUsage($name) == 0) {
			$template = new SmartyTemplate($name);
			$this->deleteHistory('template', $template->getName());
			$values = array('NAME' 		=> $name,	
							'CID' 		=> _CID_);
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_delete');
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
			return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		} else {
	       	$GLOBALS['LOGGER']->logError('Template "'.$name.'" cannot be deleted, in use!');
		}
		return false;	
	}

	/**
	 * Delete the given Stylesheet.
	 */
	function deleteStylesheet($name)
	{
		if($this->countStylesheetUsage($name) == 0) {
			$style = new SmartyStylesheet($name);
			if(unlink($style->getFullFilename())) {	
				$this->deleteHistory('stylesheet', $style->getName());
				$values = array('NAME' 		=> $name,	
								'CID' 		=> _CID_);
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_delete');
				$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
				return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
			} else {
		       	$GLOBALS['LOGGER']->logError('Check your access rights, File ('.$style->getFullFilename().') could not be deleted!');
			}
		} else {
	       	$GLOBALS['LOGGER']->logError('Stylesheet "'.$name.'" cannot be deleted, in use!');
		}
		return false;	
	}
	
	/**
	 * Delete the given Design.
	 */
	function deleteDesign($name)
	{
		if($this->countDesignUsage($name) == 0) {
			$template = new SmartyDesign($name);
			$values = array('NAME' 		=> $name,	
							'CID' 		=> _CID_);
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_delete');
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values,true);
			return $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		} else {
	       	$GLOBALS['LOGGER']->logError('Design "'.$name.'" cannot be deleted, in use!');
		}
		return false;	
	}
	
	function getAllTemplates($showIncludes = true) 
	{
		$all = array();
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_template_load_all');
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array());
	    $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    for($i=0; $i < $entrys->count(); $i++) {
	    	$temp = new SmartyTemplate();	
    		$temp->setArray($entrys->next());
    		if(!$temp->isInclude() || $showIncludes)
    			$all[] = $temp;
	    }
		return $all;
	}

	/**
	 * Fetches all Designs as an array of SmartyDesign Objects.
	 * @return array all available Designs 
	 */
	function getAllDesigns() 
	{
		$all = array();
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_design_load_all');
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array());
	    $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    for($i=0; $i < $entrys->count(); $i++) {
	    	$temp = new SmartyDesign();	
    		$temp->setArray($entrys->next());
   			$all[] = $temp;
	    }
		return $all;
	}

	/**
	 * Fetches all Stylesheets as an array of SmartyStylesheet Objects.
	 * @return array all available Designs 
	 */
	function getAllStylesheets() 
	{
		$all = array();
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('smarty_stylesheet_load_all');
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array());
	    $entrys = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	    for($i=0; $i < $entrys->count(); $i++) {
	    	$temp = new SmartyStylesheet();	
    		$temp->setArray($entrys->next());
   			$all[] = $temp;
	    }
		return $all;
	}		

	/**
	 * Sets which portlet columns are availbale for the given design 
	 *
	 * @param string $design
	 * @param array $columnNameArray
	 */
	function setPortlets($design, $columnNameArray) {
		$values = array('DESIGN' => $design); 
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('design_portlets_delete');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		$GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

		foreach($columnNameArray AS $name) {
			$values = array('DESIGN' => $design, 'NAME' => $name); 
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('design_portlets_insert');
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
			$GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		}
	}

	/**
	 * Sets which contents are available for a design.
	 *
	 * @param string $design
	 * @param array $columnNameArray
	 */
	function setContents($design, $columnNameArray) {
		$values = array('DESIGN' => $design); 
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('design_contents_delete');
		$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
		$GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);

		foreach($columnNameArray AS $name) {
			$values = array('DESIGN' => $design, 'NAME' => $name); 
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('design_contents_insert');
			$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
			$GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		}
	}
}
?>