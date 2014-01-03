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
 * @subpackage email
 */

require_once(_BIGACE_DIR_ADDON.'phpmailer/class.phpmailer.php');

define('EMAIL_TRANSPORT_MAIL', 'mail');
define('EMAIL_TRANSPORT_SMTP', 'smtp');
define('EMAIL_TRANSPORT_SENDMAIL', 'sendmail');
define('EMAIL_TRANSPORT_QMAIL', 'qmail');

define('EMAIL_TYPE_PLAIN', 'plain');
define('EMAIL_TYPE_HTML', 'html');
define('EMAIL_TYPE_BOTH', 'both');

/**
 * Holds all needed System email settings.
 */

/**
 * Class used for creating an Email.
 * 
 * Uses the configured email settings, if you do not supply different settings.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage email
 */
class email
{
    private $xmailer;						// additional header	- deprecated
    private $mime;							// mime type			- deprecated
    private $charset;						// email charset		- deprecated ? 
    private $reply;							// reply adress			- deprecated ?
	private $errors;						// error address		- deprecated ?
    private $encoding;						// mail encoding		- deprecated ?
	
    private $content_html;					// html content
	private $content_plain;					// plain content
    private $to = '';						// recipient adress
    private $to_name = '';					// recipient name
	private $from_name;						// send email from name
	private $from_email;					// send email from address
	private $server;						// smtp server
    private $subject;						// mail subject
    
    private $type = EMAIL_TYPE_PLAIN;		// the email type to send 
    private $method = EMAIL_TRANSPORT_MAIL; // the method how to send an email
    private $error 	= ""; 					// error string, if send failed!
    
    function email()
    {
        $this->resetSettings();
    }
    
    function resetSettings()
    {
		$this->type       = get_option("email", "content.type");
        $this->server     = get_option("email", "smtp.server");
        $this->charset    = get_option("email", "character.set");
        $this->from_name  = get_option("email", "from.name");
        $this->from_email = get_option("email", "from.address");
        $this->encoding   = get_option("email", "encoding");
        
        $this->to = '';
        $this->content_plain = '';
        $this->content_html = '';
        $this->reply = '';
        $this->subject = '';
        $this->errors = '';
    }
    
    // ------------------------------- DEPRECATED -------------------------------
    function setContentEncoding($val) {
        $this->encoding = $val;
    }
    function setErrorsTo($val) {
        $this->errors = $val;
    }
    /**
     * @deprecated since 2.5
     */
    function setMimeVersion($val) {
        $this->mime = $val;
    }
    function setCharSet($val) {
        $this->charset = $val;
    }
    /**
     * @deprecated since 2.5
     */
    function setXMailer($val) {
        $this->xmailer = $val;
    }
    // ------------------------------- DEPRECATED -------------------------------
    
    function setCharacterSet($val) {
        $this->setCharSet($val);
    }
    function setReplyTo($val) {
        $this->reply = $val;
    }
    
    /**
     * Sets the SMTP server to use.
     * @param string $val
     */
    function setServer($val) {
        $this->server = $val;
    }
    
	/**
	 * Sets whether we send an:
	 * - EMAIL_TYPE_PLAIN (default)
	 * - EMAIL_TYPE_BOTH
	 * - EMAIL_TYPE_HTML
	 */
    function setContentType($val) {
        $this->type = $val;
    }
    
    /**
     * @deprecated use setRecipient() 
     */
    function setTo($val) {
    	$this->setRecipient($val);
    }
    
    /**
     * Sets the recipient adress.
     */
    function setRecipient($val) {
        $this->to = $val;
    }
    
    /**
     * Sets the recipient name.
     */
    function setRecipientName($val) {
        $this->to_address = $val;
    }
    
    /**
     * The emails subject.
     * @param string $val
     */
    function setSubject($val) {
        $this->subject = $val;
    }

    /**
     * The Plain text content.
     * @param string $val
     */
    function setContent($val) {
        $this->content_plain = $val;
    }

    /**
     * The HTML content. 
     * Must be set if you send EMAIL_TYPE_BOTH or EMAIL_TYPE_HTML.
     * @param string $val
     */
    function setHTML($val) {
        $this->content_html = $val;
    }
    
    /**
     * @deprecated use setFromEmail(String)  
     */
    function setFrom($val) {
        $this->setFromEmail($val);
    }
    
    /**
     * Required field.
     * @param String the From Email 
     */
    function setFromEmail($val) {
        $this->from_email = $val;
    }

    /**
     * @param String the Name of the Mail sender  
     */
    function setFromName($val) {
        $this->from_name = $val;
    }

    /**
     * This sends the configured Email.
     * @return boolean whether this Email could be send or not
     */
    function sendMail()
    {
    	$mail = new PHPMailer(); // default is to use mail()
    	if($this->method == EMAIL_TRANSPORT_SENDMAIL) {
    		$mail->IsSendmail(); // telling the class to use SendMail transport	
    	} else if ($this->method == EMAIL_TRANSPORT_SMTP) {
    		$mail->IsSMTP();
    		$mail->Host = $this->server; // SMTP server
    	} else if ($this->method == EMAIL_TRANSPORT_QMAIL) {
    		$mail->IsQmail();
    	}

    	$mail->Encoding  = $this->encoding;
    	$mail->CharSet	 = $this->charset;	
    	 
	    $mail->From      = $this->from_email;
		$mail->FromName  = $this->from_name;
		$mail->Subject   = $this->subject;
		
		if($this->type == EMAIL_TYPE_BOTH) {
			if(strlen(trim($this->content_plain)) > 0)
				$mail->AltBody = $this->content_plain;
			$mail->MsgHTML($this->content_html);
		} 
		else if ($this->type == EMAIL_TYPE_HTML) {
			$mail->MsgHTML($this->content_html);
		}
		else {
			$mail->Body = $this->content_plain;
		}
		
		$mail->AddAddress($this->to, $this->to_name);
		
        if ($this->reply != "")
			$mail->AddReplyTo($this->reply);
		
		$result = true;
		$result = $mail->Send();
		if(!$result) {
			$this->error = $mail->ErrorInfo;
		}
		if($this->method == EMAIL_TRANSPORT_SMTP) {
			$mail->SmtpClose();
		}
		return $result;

		/*
        $headers  = "Content-Transfer-Encoding: " . $this->encoding . "\r\n";
        $headers .= "MIME-Version: " . $this->mime . "\r\n";
        $headers .= "Content-type: " . $this->type . "; charset=" . $this->charset . "\r\n";
        if ($this->errors != "")
            $headers  .= "Errors-to: <".$this->errors.">" . "\r\n";
            $headers  .= "Reply-To: " . $this->reply . "\r\n";
        $headers  .= "X-Mailer: " . $this->xmailer;

        return mail($this->to, $this->subject, $this->text, $headers);
		*/
    }
    
    /**
     * Returns an error message, if send() returned false. 
     * Otherwise an empty string is returned.
     *
     * @return string
     */
    function getError() {
    	return $this->error;
    }

}
