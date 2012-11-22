<?php
/*
 * Prints or assigns the Javascript Code for Piwik Analytics.
 *
 * Parameter:
 * - path        = STRING - installation path, by default http://yourdomain/addon/piwik/
 * - id          = STRING  - piwik analytics id (UA-xxxxxx-x)
 * - assign      = STRING  - name of tpl variable to assign the javascript code to
 * - track_user  = BOOLEAN - whether logged in user should be tracked or not (default false)
 * - track_links = BOOLEAN - whether link tracking should be enabled (default true)
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */
function smarty_function_piwik($params, &$smarty)
{
    if (!isset($params['id'])) {
        $smarty->trigger_error("piwik: missing 'id' attribute");
        return;
    }

    $linkTracking = isset($params['track_links']) ? (bool) $params['track_links'] : true;
    $loggedIn     = isset($params['track_user']) ? (bool) $params['track_user'] : false;
    $path         = isset($params['path']) ? $params['path'] : BIGACE_URL_ADDON."piwik/";
    $id           = $params['id'];

    if ($loggedIn || $GLOBALS['_BIGACE']['SESSION']->isAnonymous()) {
    	$badge  = "<script type=\"text/javascript\">
var pkBaseURL = ((\"https:\" == document.location.protocol) ? \"".$path."\" : \"".$path."\");
document.write(unescape(\"%3Cscript src='\" + pkBaseURL + \"piwik.js' type='text/javascript'%3E%3C/script%3E\"));
</script><script type=\"text/javascript\">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + \"piwik.php\", ".$id.");
piwikTracker.trackPageView();";
    	if($linkTracking) {
	    	$badge .= "\npiwikTracker.enableLinkTracking();";
    	}
    	$badge .= "
} catch( err ) {}
</script>" . '<noscript><img src="'.$path.'piwik.php?idsite='.$id.'" style="border:0" alt=""/></noscript>';

		if (isset($params['assign'])) {
			$smarty->assign($params['assign'], $badge);
			return;
		}

		return $badge;
    }

    return;
}