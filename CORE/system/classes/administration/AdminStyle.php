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

/**
 * Represents a single Administration Style.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage administration
 */
class AdminStyle
{
	private $directory;
	private $name;
	private $css;
	private $mimetypes = array();
	
	function AdminStyle($name) 
	{
		$this->setName($name);
		$this->setDirectory($name);
		$this->setCSS( $this->getWebDirectory() . 'style.css' );
	}
	
	function setCSS($css) 
	{
		$this->css = $css;
	}

	/**
	 * Returns the full qualified Style File Name, including the Directory.
	 * @return String the full qulaified File Name 
	 */
	function getCSS() 
	{
		return $this->css;
	}
	
	function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns the Name of this Style.
	 */
	function getName()
	{
		return $this->name;
	}
	
	function setDirectory($dir)
	{
		$this->directory = $dir;
	}
	
	/**
	 * Returns the Directory Name of this Style.
	 */
	function getDirectory() 
	{
		return $this->directory;
	}
	
	/**
	 * Returns the Include Name of this Style.
	 */
	function getIncludeName() 
	{
		return $this->getFileSystemDirectory() . $this->getName() . '.php';
	}

	/**
	 * Returns the Directory of this Style as URL, usable in HTML scripts.
	 */
	function getWebDirectory() 
	{
		return _BIGACE_DIR_PUBLIC_WEB.'system/style/'.$this->getDirectory().'/';
	}
    
	/**
	 * Returns the Directory Name of this Style usable in PHP Scripts.
	 */
	function getFileSystemDirectory() 
	{
		return _BIGACE_DIR_PUBLIC.'system/style/'.$this->getDirectory().'/';
	}

	/**
	 * Returns the Directory Name where the Styles Templates resist.
	 */
	function getTemplateDirectory() 
	{
		return $this->getFileSystemDirectory().'templates/';
	}

	/**
	 * Returns an Image URL for the given Item, representing its Mimetype.
	 * If none could be found, it uses a default Image.
	 */
	function getMimetypeImageURL($extension) 
	{
		$extension = strtolower($extension);
		if (!isset($this->mimetypes[$extension]))
		{
			if (file_exists(_BIGACE_DIR_PUBLIC.'system/images/mimetype/'.$extension.'.gif'))
				$this->mimetypes[$extension] = _BIGACE_DIR_PUBLIC_WEB.'system/images/mimetype/'.$extension.'.gif';
			else {
                $this->mimetypes[$extension] = _BIGACE_DIR_PUBLIC_WEB.'system/images/mimetype/default.icon.gif';
            }
		}
		return $this->mimetypes[$extension];
	}
	
    
	
}

?>