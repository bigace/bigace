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
 * @subpackage comments
 */

import('classes.item.Itemtype');
import('classes.util.IdentifierHelper');
import('classes.email.TextEmail');
import('classes.comments.CommentService');

define('COMMENT_IS_SPAM', -1);
define('COMMENT_DUPLICATE', -2);
define('COMMENT_NOT_CREATED', false);
define('COMMENT_CREATED', true);

/**
 * Class used for administrating "Comments"
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage comments
 */
class CommentAdminService
{
	private $itemtype = _BIGACE_ITEM_MENU;
	
    /**
    * Instantiates a CommentAdminService.
    */
    function CommentAdminService($itemtype = null)
    {
    	if(!is_null($itemtype))
    		$this->itemtype = $itemtype;
    }
    
    function checkIsSpam($apiKey, $itemid, $language, $name, $comment, $email = '', $homepage = '')
    {
		import('classes.item.ItemService');
    	$is = new ItemService($this->itemtype);
    	$item = $is->getItem($itemid, ITEM_LOAD_FULL, $language);
    	 
		require_once(BIGACE_PLUGINS.'comments/akismet.class.php');
	 	$comment = array(
            'author'    => $name,
            'email'     => $email,
            'website'   => $homepage,
            'body'      => $comment,
            'permalink' => LinkHelper::getUrlFromCMSLink( LinkHelper::getCMSLinkFromItem($item) )
         );
         
         //print_r($comment);
	     $akismet = new Akismet($GLOBALS['_BIGACE']['DOMAIN'], trim($apiKey), $comment);
	 
	     if($akismet->errorsExist()) {
	     	$errs = $akismet->getErrors();
	     	$err = "";
	     	foreach($errs AS $errName => $errMsg) {
	     		$err .= " [".$errName."] " . $errMsg;
	     	}
		    $GLOBALS['LOGGER']->logError('Could not connect to Askimet Server: ' . $err);
	     } else {
	     	return $akismet->isSpam();
	     }
	     return false;
    }
    
    function createComment($itemid, $language, $name, $comment, $email = '', $homepage = '', $type = 'comment')
    {
    	$activate = false;
		if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
			$activate = ConfigurationReader::getConfigurationValue("comments", "auto.activate.unregistered", false);
		else
			$activate = ConfigurationReader::getConfigurationValue("comments", "auto.activate.registered", false);

		$apiKey = ConfigurationReader::getConfigurationValue("comments", "akismet.wordpress.api.key", "");
		if(strlen(trim($apiKey)) > 0) {
			$autoDelete = ConfigurationReader::getConfigurationValue("comments", "akismet.auto.delete.negative", false);
			$autoActivate = ConfigurationReader::getConfigurationValue("comments", "akismet.auto.activate.positive", true);
			if($autoActivate || $autoDelete) {
				$spam = $this->checkIsSpam($apiKey, $itemid, $language, $name, $comment, $email, $homepage);
				if($spam) {
					$activate = false;
					if($autoDelete) {
				        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_spam_increase');
				        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array(), true);
				        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
						return COMMENT_IS_SPAM; 
					}
				}
				else {
					if($autoActivate)
						$activate = true;
				}
			}
		}
		
		$cs = new CommentService();
		if ($cs->checkCommentDuplicate($this->itemtype,$itemid,$language,$name,$comment))
			return COMMENT_DUPLICATE;

		// insert entry
       	$values = array(
    				'ITEMTYPE' 		=> $this->itemtype, 
    				'ITEMID' 		=> $itemid, 
    				'LANGUAGE' 		=> $language, 
    				'NAME' 			=> $name, 
    				'EMAIL' 		=> $email, 
    				'HOMEPAGE' 		=> $homepage, 
    				'IP' 			=> $_SERVER['REMOTE_ADDR'], 
       				'COMMENT' 		=> $comment, 
    				'TIMESTAMP' 	=> time(),
    				'ACTIVE'		=> $activate,
       				'TYPE'			=> $type,
       				'ANONYMOUS'		=> $GLOBALS['_BIGACE']['SESSION']->isAnonymous());
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_create');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        
		if(!$activate) {
			if(ConfigurationReader::getConfigurationValue("comments", "email.send.moderator", true)) {
				$temp = ConfigurationReader::getConfigurationValue("comments", "email.recipient.moderator");
				$this->_sendModeratorEmail($itemid, $language, $temp, getTranslation('comment.email.moderator.subject'), getTranslation('comment.email.moderator.text'), $values);
			} 
		}
		else {
			if(ConfigurationReader::getConfigurationValue("comments", "email.send.posting", true)) {
				$temp = ConfigurationReader::getConfigurationValue("comments", "email.recipient.posting");
				$this->_sendModeratorEmail($itemid, $language, $temp, getTranslation('comment.email.posting.subject'), getTranslation('comment.email.posting.text'), $values);
			} 
		}

		return ($res->isError() ? COMMENT_NOT_CREATED : COMMENT_CREATED);
    }
    
    
	function _sendModeratorEmail($itemid, $language, $recipients, $subject, $content, $values)
	{
		$from = ConfigurationReader::getConfigurationValue("comments", "email.from");
		if(is_null($from) || strlen(trim($from)) < 3) // 3 = a@a
			$from = ConfigurationReader::getConfigurationValue("community", "contact.email", '');
		
		$itemTypeObj = new Itemtype($this->itemtype);
		$myLink = new CMSLink();
		$myLink->setItemID($itemid);
		$myLink->setLanguageID($language);
		$myLink->setCommand($itemTypeObj->getCommand());
		
		$values['URL'] = LinkHelper::getUrlFromCMSLink( $myLink );
		
		foreach($values AS $key => $val) {
			$content = str_replace("{".$key."}",$val,$content);
			$subject = str_replace("{".$key."}",$val,$subject);
		}
		
		$recipients = explode(",", trim($recipients));
		for($i = 0; $i < count($recipients); $i++) {
			$recipient = $recipients[$i];
			
			if(strlen(trim($recipient)) > 3) { // 3 = a@a
				$email = new TextEmail();
				$email->setTo( $recipient );
		        $email->setFromEmail( $from );
		        $email->setSubject( '[BIGACE] '. $subject );
		        $email->setContent( $content );
		        // TODO set character set dynamically
		        //$email->setCharacterSet($LANGUAGE->getCharset());
		        $didMail = $email->sendMail();
		        if(!$didMail)
		        	$GLOBALS['LOGGER']->logError('Could not send comment email to ' . $recipient);
			}
		}
	}
    
    /**
     * Deletes the Comment entry with the given ID.
     */
    function delete($id) 
    {
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_delete');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }

    /**
     * Marks the Entry as Spam and deletes the Comment entry with the given ID.
     */
    function deleteSpam($id) 
    {
		$apiKey = ConfigurationReader::getConfigurationValue("comments", "akismet.wordpress.api.key", "");
		if(strlen(trim($apiKey)) > 0) {
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_select');
	        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
	        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
	        $res = $res->next();
        
			require_once(BIGACE_PLUGINS.'comments/akismet.class.php');
		 	$comment = array(
	            'author'    => $res['name'],
	            'email'     => $res['email'],
	            'website'   => $res['homepage'],
	            'body'      => $res['comment']
	        );
		    $akismet = new Akismet($GLOBALS['_BIGACE']['DOMAIN'], trim($apiKey), $comment);
		    if(!$akismet->errorsExist()) {
	    		$akismet->submitHam();
		    }
		    else {
		     	$errs = $akismet->getErrors();
		     	$err = "";
		     	foreach($errs AS $errName => $errMsg) {
		     		$err .= " [".$errName."] " . $errMsg;
		     	}
			    $GLOBALS['LOGGER']->logError('Failed submitting ham to Askimet Server: ' . $err);
		    }
		}
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_spam_increase');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array(), true);
        $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
		return $this->delete($id);
    }
    
    /**
     * Updates the Comment with the given ID.
     */
    function update($id, $name, $comment, $email = '', $homepage = '') 
    {
       	$values = array(
    				'ID' 			=> $id, 
    				'NAME' 			=> $name, 
    				'EMAIL' 		=> $email, 
    				'HOMEPAGE' 		=> $homepage, 
       				'COMMENT' 		=> $comment
       	);
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_update');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
    }
    
    /**
     * Deletes all Comments for the ItemID and Language.
     * If $language is null, all comments for this item will be deleted.
     */
    function deleteAll($itemid, $language = null) 
    {
    	$values = array(
    			'ITEMTYPE' => $this->itemtype,
    			'ID' => $itemid
    	);
    	
    	if(!is_null($language)) {
            $values['LANGUAGE'] = $language;
    		$sql = "DELETE FROM {DB_PREFIX}comments WHERE itemtype = {ITEMTYPE} AND itemid = {ID} AND language = {LANGUAGE} AND cid = {CID}";
    	} else {
    		$sql = "DELETE FROM {DB_PREFIX}comments WHERE itemtype = {ITEMTYPE} AND itemid = {ID} AND cid = {CID}";
        }
    	
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql, $values, true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
    }
    
	function activate($id) {
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->loadStatement('comment_activate');
        $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, array('ID' => $id), true);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
        return !$res->isError();
	}
}

?>