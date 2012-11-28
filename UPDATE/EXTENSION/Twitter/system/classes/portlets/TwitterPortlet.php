<?php
/*
 * @author Andy
 * @package bigace.classes
 * @subpackage portlets
 */
import('api.portlet.Portlet');

class TwitterPortlet extends Portlet
{
    protected static $_counter = 0;
    protected static $_seen = false;
    private $counter = 0;

	function TwitterPortlet() {
        $this->setParameter( 'user', '' );
		$this->setParameter( 'amount', '5' );
		$this->counter = self::$_counter++;
    }

	function getIdentifier() {
        return 'TwitterPortlet';
    }

	function getTitle() {
    	if($this->getParameter('user') != '')
            return "Twitter: " . $this->getParameter('user');
        else
            return "Twitter";
    }

	function needsJavascript() {
        return true;
    }

    function getJavascript() {
        $js = '';
        if(self::$_seen === false) {
            $js .= '<script type="text/javascript" src="'.BIGACE_URL_ADDON.'blogger.js"></script>'."\n";
            self::$_seen = true;
        }
        $js .= '<script type="text/javascript">
function myTweet'.$this->counter.'(twitters) {
    twitterCallback2(twitters,"twitter_update_list'.$this->counter.'");
}
</script>';
        return $js;
    }

    function getHtml() {
        return '<ul id="twitter_update_list'.$this->counter.'"></ul>
				<script type="text/javascript" src="https://api.twitter.com/1/statuses/user_timeline/'.$this->getParameter('user').'.json?callback=myTweet'.$this->counter.'&amp;count='.$this->getParameter('amount').'"></script>';
    }

	function getParameterName($key) {
        switch($key) {
            case 'user':
                return 'Twitter Username';
			case 'amount':
                return 'No. of updates to show';
        }
    }
}
