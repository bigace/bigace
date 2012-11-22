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
 * @package bigace.classes
 * @subpackage template
 */

loadClass('template', 'HtmlTemplate');

$_BIGACE['TEMPLATES']['AUTOVALUE'] = array();

/**
 * Adds a Template value that will be set automatically.
 * All variables inside Templates following the Naming:
 * <code>{AUTO_xx}</code>
 * are filled with settings from this method.
 * 
 * For example, you provide a variable:
 * <code>setBigaceTemplateValue('foo', 'dummy')</code>
 * and use this in your Template: 
 * <code>This is a {AUTO_foo} test!</code>
 *  
 * The result would look like this:
 * <code>This is dummy test!</code>.
 * 
 * @param String key the Key for this AutoValue
 * @param String value the Value of this AutoValue
 */
function setBigaceTemplateValue($key, $value) {
  $GLOBALS['_BIGACE']['TEMPLATES']['AUTOVALUE'][$key] = $value;
}

/**
 * Returns a previous added Template Auto-Value.
 * 
 * @param String name the Name of the requested AutoValue
 * @return String the Value that represents the given key
 */
function getBigaceTemplateValue($name) {
  return $GLOBALS['_BIGACE']['TEMPLATES']['AUTOVALUE'][$name];
}

/**
 * This file is used for easier handling of Templates with useful 
 * callback functions for Templates!
 * 
 * The TemplateService is able to search for files in different directorys, 
 * to provide fallback Templates which are not overwritten by other Styles.
 * 
 * @package bigace.classes
 * @subpackage template
 */
class TemplateService 
{

	/**
	 * @access private
	 */
	var $templateDirs = array();
    
	/**
	 * Default constructor trys to find Style settings within the Environment and
	 * adds the Style Directory settings to the Directory Pool. 
	 */
	function TemplateService() 
  	{
  		if (isset($GLOBALS['_BIGACE']['style']['class'])) {
  			$this->addTemplateDirectory($GLOBALS['_BIGACE']['style']['class']->getTemplateDirectory());
  		}
  	}

	/**
	 * Adds a Directory to the internal Pool.
	 * @param String directory the Directory where Templates will be searched 
	 */
	function addTemplateDirectory($directory) {
		if (!isset($this->templateDirs[$directory]))
			array_push($this->templateDirs, $directory);
	}
  
	/**
	 * Loads the Template from the mentioned Directorys.
	 * We search till the first occurence of the file and return this.
	 * @param String filename the Template Filename to load
	 * @param boolean removeUnknownVariables true to remove unknown variables, false to keep them untouched
	 * @param boolean $removeEmptyBlocks true to remove empty blocks, false to keep them untouched
	 */
	function loadTemplatefile( $filename, $removeUnknownVariables = true, $removeEmptyBlocks = true ) 
	{
	    $dir = '';
	
		foreach(array_reverse($this->templateDirs) AS $dirToCheck) 
		{
		    $pos = strrpos($dirToCheck, '/');
		    
		    if ($pos < strlen($dirToCheck)-1) {
		        $dirToCheck .= '/';
		    }
		    
		    if (file_exists($dirToCheck.$filename)) {
		        $dir = $dirToCheck;
		    }
		}
	
		$tpl = new HtmlTemplate( $dir );
		$tpl->setCallbackBlockname('AUTO', 'getBigaceTemplateValue');
		$tpl->setCallbackBlockname('TRANSLATION', 'getTranslation');
	
		$tpl->loadTemplatefile($filename, $removeUnknownVariables, $removeEmptyBlocks);
		return $tpl;
	}
  
}