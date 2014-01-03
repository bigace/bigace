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
 * @subpackage util
 */

import('classes.util.ApplicationLinks');
import('classes.util.Translations');
import('classes.util.html.JavascriptHelper');

/**
 * Use this Library to create Links to the Default Applications, devlivered with BIGACE.
 * The rendered HTML can be customized with various methods.
 *
 * Use this Class to get Links for
 *
 * - Home   : The Top Level Page for your Consumer
 * - Status : Login or Logoff
 * - Admin  : The Administration Console in a new Window
 * - Search : The Standard Search in a POP-UP Window
 * - Editor : The Editor in a POP-UP Windows, editing the current Page (Editor type depends on the Default Editor settings)
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class applications
{
    /**
     * Constant for the HOME Link.
     */
    var $HOME       = 'home';
    /**
     * Constant for the ADMINISTRATION Link.
     */
    var $ADMIN      = 'admin';
    /**
     * Constant for the SEARCH Link.
     */
    var $SEARCH     = 'search';
    /**
     * Constant for the EDITOR Link.
     */
    var $EDITOR     = 'editor';
    /**
     * Constant for the STATUS Link.
     */
    var $STATUS     = 'status';
    /**
     * Constant for the PORTLET ADMINISTRATION Link.
     */
    var $PORTLETS   = 'portletadmin';

    private $homeID 	= _BIGACE_TOP_LEVEL;
    private $delim      = '';
    private $preDelim   = null;
    private $postDelim  = null;
    private $hide       = array('');
    private $showPic    = true;
    private $showText   = false;
    private $linkclass  = '';
    private $show;
    private $imageWidth = '';
    private $imageHeight = '';
    private $frightService = NULL;
    private $itemRight = NULL;
    private $accessAdmin = NULL;    
    private $accessPortlet = NULL;
    private $accessEditor = NULL;
    private $bundle = NULL;
    private $imageAlign = NULL;

    /**
     * Constructor setting up some default values.
     */
    function applications()
    {
        $this->show = array($this->HOME,
                            $this->SEARCH,
                            $this->ADMIN,
                            $this->EDITOR,
                            $this->PORTLETS,
                            $this->STATUS);

        $this->bundle = Translations::get('bigace', _ULC_, 'app');
    }

    /**
     * @access private
     */
    function getTranslation($key)
    {
        $b = $this->bundle;
        return $b->getString($key);
    }

    /**
     * Adds the given Application to the Hide Array. All submitted Applications will not be
     * added automatically to the Link when calling <code>getAllLink()</code>.
     * This does not affect hte work of the methods like <code>getEditorLink()</code>.
     *
     * @param String the Application name ($HOME,$ADMIN,$SEARCH,$EDITOR,$STATUS)
     */
    function hide($app) {
        array_push($this->hide, $app);
    }

    /**
     * @access private
     */
    function getFrightService() {
        if ($this->frightService == null) {
            import('classes.fright.FrightService');
            $this->frightService = new FrightService();
        }
        return $this->frightService;
    }

    /**
     * @access private
     */
    function getItemRight() {
        if ($this->itemRight == null) {
            import('classes.right.RightService');
            $rs = new RightService();
            $this->itemRight = $rs->getItemRight(_BIGACE_ITEM_MENU, $GLOBALS['_BIGACE']['PARSER']->getItemID(), $GLOBALS['_BIGACE']['SESSION']->getUserID());
            unset($rs);
        }
        return $this->itemRight;
    }

    /**
     * @access private
     */
    function getLinkClass() {
        return $this->linkclass;
    }

    /**
     * Set the CSS Class that will be used for setting up each links.
     * @param String the name of the CSS Class
     */
    function setLinkClass($class) {
        $this->linkclass = $class;
    }

    /**
     * Sets the Image width and height. Pass an empty String(default) to define that
     * the attribute should not be set within the image TAG.
     * @param String the size or an empty String ''
     */
    function setImageDimension($width, $height) {
        $this->imageWidth = $width;
        $this->imageHeight = $height;
    }

    /**
     * Sets the Alignment for Images.
     */
    function setImageAlign($align = null) {
        $this->imageAlign = $align;
    }

    /**
     * @access private
     */
    function getPreDelimiter() {
        return $this->preDelim;
    }

    // ---------- DEPRECATED ----------
    // remove with 2.7
    function getAddPreDelim() {    }
    function getAddPostDelim() {}
    function setAddPreDelim($delim) {    }
    function setAddPostDelim($delim) {    }
    // --------------------------------

    /**
     * @access private
     */
    function getPostDelimiter() {
        return $this->postDelim;
    }

    /**
     * Sets both Delimiter (Pre and Post) to the same value.
     * Those will be used when creating Links.
     * @param String the Delimiter to use (normally some kind of HTML TAG)
     */
    function setDelimiter($delim) {
        $this->setPreDelimiter($delim);
        $this->setPostDelimiter($delim);
    }

    /**
     * Sets the Pre-Link-Delimiter.
     * @param String the Delimiter to use (normally some kind of HTML TAG)
     */
    function setPreDelimiter($delim) {
        $this->preDelim = $delim;
    }

    /**
     * Sets the Post-Link-Delimiter.
     * @param String the Delimiter to use (normally some kind of HTML TAG)
     */
    function setPostDelimiter($delim) {
        $this->postDelim = $delim;
    }

    /**
     * Returns the Delimiter that will be used.
     * @deprecated use getPreDelimiter() and getPostDelimiter() instead!
     */
    function getDelimiter() {
        return $this->getPreDelimiter();
    }

    /**
     * @access private
     */
    function getShowText() {
        return $this->showText;
    }

    /**
     * @access private
     */
    function getShowPicture() {
        return $this->showPic;
    }

    /**
     * Sets whether we show Text within the Links.
     * Make sure to pass a REAL boolean, we perfom a check on the argument and only
     * accept it, if <code>is_bool($val)</code> returns true!
     *
     * @param boolean true for showing text links, false for hiding the textual description
     */
    function setShowText($val) {
        if (is_bool($val))
        $this->showText = $val;
    }

    /**
     * Sets whether we show Pictures within the Links.
     * @param boolean true for showing image links, false for hiding the image
     */
    function setShowPicture($val) {
        $this->showPic = $val;
    }

    function getHomeID() {
        return $this->homeID;
    }

    /**
     * Set the ID used for the Home Link created by <code>getHomeLink()</code>.
     * @param String id the Menu ID for Home
     */
    function setHomeID($id) {
        return $this->homeID = $id;
    }
    // ------------------------------------------------------------
    // --------------------------- HOME ---------------------------

    /**
     * Gets the Home Link.
     *
     * @param String the textual description for the Link (default is a Translation String called 'home')
     * @param String the image filename for the Link (defalt is 'home.gif')
     */
    function getHomeLink($desc = '', $img = '')
    {
        $text = ($desc == '') ? $this->getTranslation('home') : $desc;
        $image = ($img == '') ? 'home.gif' : $img;
        return $this->createConfiguredAppLink(ApplicationLinks::getHomeURL($this->getHomeID()), $text, $image);
    }

    // ------------------------------------------------------------
    // -------------------------- STATUS --------------------------

    /**
     * Gets the Status Link, depending on the Users Status we show a Login or Logoff Link.
     *
     * @param String the text information for the Link
     * @param String the image name for the Link
     */
    function getStatusLink($desc = '', $img = '')
    {
        if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
            $text = ($desc == '') ? $this->getTranslation('login') : $desc;
            $image = ($img == '') ? 'login.gif' : $img;
            $link = ApplicationLinks::getLoginFormURL($GLOBALS['MENU']->getID());
        } else {
            $text = ($desc == '') ? $this->getTranslation('logout') : $desc;
            $image = ($img == '') ? 'logout.gif' : $img;
            $link = ApplicationLinks::getLogoutURL($GLOBALS['MENU']->getID());
        }
        return $this->createConfiguredAppLink($link, $text, $image);
    }

    // ------------------------------------------------------------
    // ---------------------- ADMINISTRATION ----------------------

    /**
     * Returns whether the User has the rights to access the Editor.
     */
    function canAccessAdmin()
    {
    	$values = array( 'CID'		=> _CID_,
    			'USER_ID'	=> $GLOBALS['_BIGACE']['SESSION']->getUserID(),
    	);
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('fright_for_user');
    	$sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values);
    	$res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    	if ($res->next()) {
    		$this->accessAdmin = true;
    	} else {
    		$this->accessAdmin = false;
    	}
    	return $this->accessAdmin;
    }
    
    /**
     * Gets the Administration Link, opening the BIGACE Administration Console.
     *
     * @param String the text information for the Link
     * @param String the image name for the Link
     */
    function getAdminLink($desc = '', $img = '')
    {
    	if ($GLOBALS['_BIGACE']['SESSION']->isAnonymous() || !$this->canAccessAdmin()) {
            return '';
        }
        $text = ($desc == '') ? $this->getTranslation('admin') : $desc;
        $image = ($img == '') ? 'preferences.gif' : $img;
        return $this->createConfiguredAppLink(ApplicationLinks::getAdministrationURL(_BIGACE_TOP_LEVEL), $text, $image);
    }

    // ------------------------------------------------------------
    // -------------------------- EDITOR --------------------------

    /**
     * Returns whether the User has the rights to access the Editor.
     */
    function canAccessEditor()
    {
        if ($this->accessEditor == NULL) {
            import('classes.permission.UseCaseEditContent');
            $ucec = new UseCaseEditContent($GLOBALS['_BIGACE']['PARSER']->getItemID());
            $this->accessEditor = $ucec->isAllowed();
        }
        return $this->accessEditor;
    }

    /**
     * Gets the Link to the Default Editor.
     *
     * @param String the text information for the Link
     * @param String the image name for the Link
     * @return String the Link or an empty String
     */
    function getEditorLink($desc = '', $img = '')
    {
        if (!$this->canAccessEditor()) {
	        return '';
        }
       	$text = ($desc == '') ? $this->getTranslation('editor') : $desc;
        $image = ($img == '') ? 'editor2.gif' : $img;
        return $this->createConfiguredPopupAppLink(ApplicationLinks::getEditorURL(), "javascript:openEditor(); return false;", $text, $image, "");
    }

    /**
     * Returns the Javascript used for opening the Default Editor.
     * @return String the Javascript or an empty String
     */
    function getEditorJS()
    {
        if (!$this->canAccessEditor()) {
	        return '';
        }
        return JavascriptHelper::createJSPopup('openEditor', 'Editor' + $GLOBALS['MENU']->getID(), '800', '470', ApplicationLinks::getEditorURL(),array(),'yes','yes');
    }

    // ------------------------------------------------------------
    // ------------------------- PORTLETS -------------------------

    /**
     * Calculates whether the Portlet Administration Link and/or Javascript will be shown.
     * Uses the Layout (global for this request) and Functional Rights.
     */
    function canAccessPortletAdmin()
    {
        // check write right settings on the page!
        $t = $this->getItemRight();
        if(!$t->canWrite())
            return false;

        // write right are set, check functional rights now
        if ($this->accessPortlet == NULL) {
            $this->accessPortlet = false;
            if (!$GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
                if(isset($GLOBALS['LAYOUT']) && $GLOBALS['LAYOUT']->hasPortletSupport() ||
                   isset($GLOBALS['SMARTY_DESIGN']) && $GLOBALS['SMARTY_DESIGN']->hasPortletSupport())
                   {
                     $frightService = $this->getFrightService();
                     if ($frightService->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), 'edit.portlet.settings')) {
                         $this->accessPortlet = true;
                     }
                }
            }
        }

        return $this->accessPortlet;
    }

    /**
     * Gets the Link to the Portlet Administration.
     * @return String the Link or an empty String
     */
    function getPortletAdminLink($desc = '', $img = '')
    {
        if ($this->canAccessPortletAdmin())
        {
            $text = ($desc == '') ? $this->getTranslation('portlet_admin') : $desc;
            $image = ($img == '') ? 'portlets.gif' : $img;
            return $this->createConfiguredPopupAppLink(ApplicationLinks::getPortletAdminURL($GLOBALS['_BIGACE']['PARSER']->getItemID()), "javascript:portletAdmin();return false;", $text, $image, "");
        }
        return '';
    }

    /**
     * Returns the Javascript used for opening the Portlet Administration.
     * @return String the Javascript or an empty String
     */
    function getPortletAdminJS()
    {
        if ($this->canAccessPortletAdmin())
        {
            return JavascriptHelper::createJSPopup('portletAdmin', 'PortletAdministration', '650', '510', ApplicationLinks::getPortletAdminURL($GLOBALS['_BIGACE']['PARSER']->getItemID()), array(), 'yes');
        }
        return '';
    }

    // ------------------------------------------------------------
    // -------------------------- SEARCH --------------------------

    /**
     * Gets the Link for opening the Default Search.
     *
     * @param String the text information for the Link
     * @param String the image name for the Link
     */
    function getSearchLink($desc = '', $img = '')
    {
        $text = ($desc == '') ? $this->getTranslation('search') : $desc;
        $image = ($img == '') ? 'search.gif' : $img;
        return $this->createConfiguredAppLink(ApplicationLinks::getSearchURL(), $text, $image);
    }

    // ------------------------------------------------------------
    // -------------------------- HELPER --------------------------

    /**
     * Gets the Link for the given Application, or an empty String
     * if the Application is not supported.
     *
     * @param String the Application name ($HOME,$ADMIN,$SEARCH,$EDITOR,$STATUS)
     * @param String the text information for the Link
     * @param String the image name for the Link
     */
    function getLink($name, $desc = '', $img = '')
    {
        if ($name == $this->SEARCH) {
            return $this->getSearchLink($desc, $img);
        } else if ($name == $this->EDITOR) {
            return $this->getEditorLink($desc, $img);
        } else if ($name == $this->ADMIN) {
            return $this->getAdminLink($desc, $img);
        } else if ($name == $this->PORTLETS) {
            return $this->getPortletAdminLink($desc, $img);
        } else if ($name == $this->STATUS) {
            return $this->getStatusLink($desc, $img);
        } else if ($name == $this->HOME) {
            return $this->getHomeLink($desc, $img);
        }
        return '';
    }

    /**
     * Returns the Javascript for the given Application or an empty
     * String if no javascript is used.
     *
     * @param String the Application Name
     */
    function getJavascript($name)
    {
        if ($name == $this->EDITOR) {
            return $this->getEditorJS();
        } else if ($name == $this->PORTLETS) {
            return $this->getPortletAdminJS();
        }
        return '';
    }

    /**
     * Returns all Links that should be shown,
     * hide the ones configured by <code>hide(String)</code> or
     * the ones the User may not see cause of missing Functional rights
     * or missing rights on the actual Menu.
     * @see hide
     */
    function getAllLink()
    {
        $html = '';
        $seenTool = false;
        for($i=0; $i < count($this->show); $i++)
        {
            $key = $this->show[$i];
            $allowed = true;
            for ($a = 0; $a < count($this->hide); $a++)
            {
                if ($this->hide[$a] == $key) {
                    $allowed = false;
                }
            }

            if ($allowed) {
                $link = $this->getLink($key);
                if ($link != '') {
                	if(!is_null($this->getPreDelimiter())) {
                        $html .= $this->getPreDelimiter();
                	}
                    $html .= $link;
			        if (!is_null($this->getPostDelimiter())) {
			            $html .= $this->getPostDelimiter();
			        }
                }
            }
        }
        return $html;
    }

    /**
     * Returns the Javascript Code that has to be set within the HTML File, to make the links work properly.
     * @return String the Javascript to past to the Page
     */
    function getAllJavascript()
    {
        $html = '';
        for($i=0; $i < count($this->show); $i++)  {
            $key = $this->show[$i];
            $allowed = true;
            for ($a = 0; $a < count($this->hide); $a++)
            {
                if ($this->hide[$a] == $key) {
                    $allowed = false;
                }

                if ($allowed) {
                    $html .= $this->getJavascript($key);
                    $allowed = false;
                }
            }
        }
        return $html;
    }

    /**
     * @access private
     */
    function createConfiguredPopupAppLink($link, $onclick, $description, $icon, $target = '')
    {
        return $this->createAppLink($link, $description, $icon, $this->getShowText(), $this->getShowPicture(), $this->getLinkClass(), $target, $this->imageWidth, $this->imageHeight, $onclick, $this->imageAlign);
    }

    function createConfiguredAppLink($link, $description, $icon, $target = '')
    {
        return $this->createAppLink($link, $description, $icon, $this->getShowText(), $this->getShowPicture(), $this->getLinkClass(), $target, $this->imageWidth, $this->imageHeight, '', $this->imageAlign);
    }

    /**
     * @access private
     */
    function createAppLink($link, $description, $icon = '', $showText = FALSE, $showPic = FALSE, $class = '', $target = '', $width = '', $height = '', $onclick='', $imgAlign = null)
    {
        $html = '<a href="' . $link . '" title="'.$description.'"';
        if ($target != '') $html .= ' target="'.$target.'"';
        $html .= ($class == '') ? '' : ' class="'.$class.'"';
        $html .= ($onclick == '') ? '' : ' onclick="'.$onclick.'"';
        $html .= '>';
        if ($showPic && $icon != '') {
            $html .= '<img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/'.$icon.'" title="'.$description.'" alt="'.$description.'"';
            if ($width != '') $html .= ' width="'.$width.'"';
            if ($height != '') $html .= ' height="'.$height.'"';
            if ($imgAlign != NULL) $html .= ' align="'.$imgAlign.'"';
            $html .= ' />';
        }
        if ($showText) {
            $html .= $description;
        }
        $html .= "</a>";
        return $html;
    }

}

?>