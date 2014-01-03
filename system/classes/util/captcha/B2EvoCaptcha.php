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
 * The default implementation for a BIGACE Captcha class, using b2_evo_captcha.  
 *  
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util.captcha
 */
class B2EvoCaptcha implements Captcha
{
	private $b2evo;
	
	function __construct() 
	{
		require_once(_BIGACE_DIR_ADDON.'b2evo/b2evo_captcha.class.php');
		
		$tempfolder = _BIGACE_DIR_ADDON . 'b2evo/b2evo_captcha_tmp/';
		//Folder Path (relative to this file + trailing slash!) where your captcha font files are stored, must be readable by the web server 
		$TTF_folder = _BIGACE_DIR_ADDON . 'b2evo/b2evo_captcha_fonts/';
		$minchars = 4; //minimum number of characters to use for the captcha
		$maxchars = 5; //maximum number of characters to use for the captcha
		$minsize = 20; //The minimum character font size to use for the captcha
		$maxsize = 30; //The maximum character font size to use for the captcha
		$maxrotation = 25; //The maximum rotation (in degrees) for each character
		$noise = TRUE; //Use background noise instead of a grid
		$websafecolors = FALSE; //Use web safe colors (only 216 colors)
		$debug = FALSE; //Enable debug messages
		$counter_filename = 'b2evo_captcha_counter.txt'; //Filename of garbage collector counter which is stored in the tempfolder
		$filename_prefix = 'b2evo_captcha_'; //Prefix of captcha image filenames
		$collect_garbage_after = 50;	//Number of captchas to generate before garbage collection is done
		$maxlifetime = 600; //Maximum lifetime of a captcha (in seconds) before being deleted during garbage collection
		$case_sensitive = FALSE; //Make all letters uppercase (does not preclude symbols)
		$validchars = 'abcdefghjkmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ';

		$CAPTCHA_CONFIG = array(
				'tempfolder'=>$tempfolder,
				'TTF_folder'=>$TTF_folder,
				'minchars'=>$minchars,
				'maxchars'=>$maxchars,
				'minsize'=>$minsize,
				'maxsize'=>$maxsize,
				'maxrotation'=>$maxrotation,
				'noise'=>$noise,
				'websafecolors'=>$websafecolors,
				'debug'=>$debug,
				'counter_filename'=>$counter_filename,
				'filename_prefix'=>$filename_prefix,
				'collect_garbage_after'=>$collect_garbage_after,
				'maxlifetime'=>$maxlifetime,
				'case_sensitive'=>$case_sensitive,
				'validchars' => $validchars);
	    
		$this->b2evo = new b2evo_captcha($CAPTCHA_CONFIG);
	}

    /**
     * This validates the Users Captcha test.
     * First parameter is the submitted code, second (if available) the image name to 
     * validate the code for.
     * 
     * @return boolean whether the code was correct or not
     */
    function validate($code, $image = null){
		if($this->b2evo->validate_submit($image,$code) == 1)
			return true;
    	return false;
    }
    
    /**
     * Returns the image url.
     * 
     * @return string the full image url
     */
    function get() {
    	return _BIGACE_DIR_ADDON_WEB . 'b2evo/b2evo_captcha_tmp/' . $this->b2evo->get_b2evo_captcha();
    }

}

?>