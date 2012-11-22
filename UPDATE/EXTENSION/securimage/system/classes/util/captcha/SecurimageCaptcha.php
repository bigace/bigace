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
 * @subpackage util.captcha
 */

import('api.util.Captcha');

/**
 * This implementation of a BIGACE Captcha uses the
 * Securimage Captcha.  
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.captcha
 */
class SecurimageCaptcha implements Captcha
{
	private $captcha;
	
	function __construct() {
		if(!bigace_session_started())
			bigace_session_start();
		
		require_once(_BIGACE_DIR_ADDON.'securimage/securimage.php');
		$this->captcha = new Securimage();
	}

    /**
     * This validates the Users Captcha test.
     * First parameter is the submitted code, second (if available) the image name to 
     * validate the code for.
     * 
     * @return boolean whether the code was correct or not
     */
    function validate($code, $image = null){
		return $this->captcha->check($code);
    }
    
    /**
     * Returns the image url.
     * 
     * @return string the full image url
     */
    function get() {
    	return _BIGACE_DIR_ADDON_WEB . "securimage/show_captcha.php?sid=" . md5(uniqid(time()));
    }

}
