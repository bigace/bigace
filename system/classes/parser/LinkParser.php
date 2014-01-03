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
 * @subpackage parser
 */

import('classes.item.Itemtype');

/**
 * Class used for parsing any BIGACE Link. 
 * It can handle both, Rewriten URLs and normal URLs with GET Parameter.
 * It is also used for splitting the Request into its BIGACE specific parts.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage parser
 */
class LinkParser 
{
    private $link = '';
    private $cmd;
    private $id = _BIGACE_TOP_LEVEL;
    private $name = '';
    private $ext;
    private $extDefinition = '';
    private $extKey = '';
    private $extLang = null;
    private $language = null;
    private $sanitized = false;
    
    function LinkParser($li)
    {
        $this->link = $li;
        $this->cmd = extractVar('cmd', null);

		$id = extractVar('id', null);
		if($id == null || trim($id) == '') {
	        $this->splitID( _BIGACE_TOP_LEVEL, false );
			$this->cmd = _BIGACE_CMD_MENU;
		} else {
	        $this->splitID( extractVar('id', _BIGACE_TOP_LEVEL), ($this->cmd == null) );
		}

		// only set language if it was passed and not empty
		if(strlen($this->extLang) > 0)
			$this->language = $this->extLang;

        // ---------------------- [SANITIZE] ----------------------
        // sanitize command
        if(!is_null($this->cmd)) {
            if(strcmp($this->cmd, preg_replace('/[^a-z0-9]/', '', $this->cmd)) !== 0) {
                $this->cmd = preg_replace('/[^a-z0-9]/', '', $this->cmd);
                $this->sanitized = true;
            }
        }        
		
		// sanitize id
		if(strcmp($this->id, preg_replace('/[^a-zA-Z0-9-.]/', '', strip_tags($this->id))) !== 0) {
    		$this->id = preg_replace('/[^a-zA-Z0-9-.]/', '', strip_tags($this->id));
            $this->sanitized = true;
		}
        // --------------------------------------------------------
    }

    /**
     * Checks whether this request needed sanitization.
     * @return boolean
     */
    function isSanitized() {
        return $this->sanitized;
    }
    
    /**
     * Splits the given ID and sets the inner class variables with the splitted values.
     */
    function splitID($idToParse, $findUniqueID = false) {

            if($findUniqueID) {
                import('classes.seo.UniqueNameService');
                $this->id = $idToParse;

                // if the given url starts with a leading slash (fastcgi) we try to remove it first
                if (strlen($idToParse) > 1 && $idToParse[0] == '/') {
                    $tempId = substr($idToParse,1);
                    $res = bigace_unique_name_raw($tempId);
                    if (!is_null($res)) {
                        $idToParse = $tempId;
                        $this->id = $idToParse;
                    }
                }

                // the leading slash was no problem, check given id
                if(is_null($res)) {
                    $res = bigace_unique_name_raw($idToParse);
                }

                // if the given url does not exist, check if only trailing slash is missing
                if(is_null($res)) {
                    $t = strrpos($idToParse, '/');
                    if($t === false || $t < (strlen($idToParse)-1)) {
                        $this->id = $idToParse . '/';
                        $res = bigace_unique_name_raw($this->id);
                        if(!is_null($res)) {
                            // it was a trailing slash problem, send header
                            header("HTTP/1.1 301 Moved Permanently");
                            header('Location: ' . LinkHelper::url($this->id));
                            exit;
                        }
                    }
                }

                // if the given url does not exist, try add trailing slash
                if(is_null($res)) {
					$t = strrpos($idToParse, '/');
    				if($t == (strlen($idToParse)-1)) {
	        			$this->id = substr_replace($idToParse, '', -1, 1);
        				$res = bigace_unique_name_raw($this->id);
        				if(!is_null($res)) {
            			// 	it was a trailing slash problem, send header
            				header("HTTP/1.1 301 Moved Permanently");
            				header('Location: ' . LinkHelper::url($this->id));
            				exit;
        				}
    				}
				}

                // found unique name - fetch values from result
                if(!is_null($res)) {
                    $itemtype = new Itemtype($res['itemtype']);
                    $this->id = $res['itemid'];
                    $this->cmd = $itemtype->getCommand();
                    $this->extLang = $res['language'];
                }
            }
            else {
	            $temp = explode ("_", $idToParse);
	            $this->id = array_shift($temp);
	            $this->ext = array();
	            for($i=0; $i<count($temp); $i++) 
	            {
                    $a = substr($temp[$i],0,1);
                    $this->ext[$a] = substr($temp[$i],1);
    
                    if ($a == 't') {
                        $this->extDefinition = $this->ext[$a];
                    }
                    else if ($a == 'k') {
                        $this->extKey = $this->ext[$a];
                    }
                    else if ($a == 'l') {
                        $this->extLang = $this->ext[$a];
                    }
	            }
	            //TODO send redirect header to unique url if possible 
            }

			unset($temp);
    }
    
    /**
     * Returns the full - unparsed - Link that was requested.
     */
    function getLink() {
        return $this->link;
    }

    /**
     * Returns the requested Filename.
     */
    function getFileName() {
        return $this->name;
    }

    /**
     * Returns the requested Command.
     */
    function getCommand() {
        return $this->cmd;
    }

    /**
     * Returns the requested ItemID.
     */
    function getItemID() {
        return $this->id;
    }

    /**
     * Returns the array with all requested Extensions as key-value Pairs.
     */
    function getExtensions() {
        return $this->ext;
    }

    /**
     * Returns the requested Definition.
     * @deprecated use getAction() instead
     */
    function getDefinition() {
        return $this->getAction();
    }

    /**
     * Gets the called Action.
     * @return String the Action Name
     */
    function getAction() {
        return $this->extDefinition;
    }

    /**
     * Returns the requested Sub-Action.
     * @return String the Subaction Name
     */
    function getSubAction() {
        return $this->extKey;
    }

    /**
     * Returns the requested key within the Definition.
     * @deprecated use getSubAction() instead
     */
    function getDefinitionKey() {
        return $this->getSubAction();
    }
    
    /**
     * Returns the requested language or null, if none was set.
     * @return String a locale
     */
    function getLanguage() {
    	return $this->language;
    }
    
    function setLanguage($lang) {
    	$this->language = $lang;
    }

    /**
     * Returns the real requested Language ID or NULL.
     * @return String the Language ID or NULL
     */
    function getLanguageFromRequest() {
    	return $this->extLang;
    }
    
}
