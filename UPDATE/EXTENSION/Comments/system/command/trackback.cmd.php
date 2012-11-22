<?php
/**
 * Handle Trackbacks and Pingbacks sent to BIGACE
 *
 * @package bigace
 */

import('classes.comments.CommentService');
import('classes.comments.CommentAdminService');
import('classes.item.ItemService');


// check general trackback setting
$allowTrackbacks = get_option("comments", "allow.trackbacks", true);

if ( !$allowTrackbacks )
	trackback_response(1, 'Sorry, trackbacks are not allowed.');

/**
 * Safely extracts not more than the first $count characters from html string
 *
 * UTF-8, tags and entities safe prefix extraction. Entities inside will *NOT* be
 * counted as one character. For example &amp; will be counted as 4, &lt; as 3, etc.
 *
 * @param integer $str String to get the excerpt from
 * @param integer $count Maximum number of characters to take
 * @eaturn string the excerpt
 */
function html_excerpt( $str, $count ) {
	$str = strip_tags( $str );
	if(function_exists('mb_strcut'))
		$str = mb_strcut( $str, 0, $count );
	else 
		$str = substr($str, 0, $count);
	// remove part of an entity at the end
	$str = preg_replace( '/&[^;\s]{0,6}$/', '', $str );
	return $str;
}


/**
 * trackback_response() - Respond with error or success XML message
 *
 * @param int|bool $error Whether there was an error or not
 * @param string $error_message Error message if an error occurred
 */
function trackback_response($error = 0, $error_message = '') {
	header('Content-Type: text/xml; charset=UTF-8' );
	if ($error) {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
	} else {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
	exit;
}


$itemtype = _BIGACE_ITEM_MENU; // default itemtype if not overwritten
/*
 // currently only menu trackbacks are allowed	
if(!isset($_GET['itemtype']))
	trackback_response(1, 'I need an itemtype for this to work.');
$itemtype = $_GET['itemtype'];
*/
$ITEM_SERVICE = new ItemService($itemtype);
$ITEM = $ITEM_SERVICE->getItem($GLOBALS['_BIGACE']['PARSER']->getItemID(), ITEM_LOAD_FULL, $GLOBALS['_BIGACE']['PARSER']->getLanguage());

if(!$ITEM->exists())
	trackback_response(1, 'This is not a valid trackback URL.');
	
// If it doesn't look like a trackback at all... probably some clicked the link. Redirect to the original page.
if (!isset($_POST['url']) && !isset($_POST['title']) && !isset($_POST['blog_name'])) {
	$link = LinkHelper::getCMSLinkFromItem($ITEM);
    header("HTTP/1.1 301 Moved Permanently"); 
    header('Location: ' . LinkHelper::getUrlFromCMSLink($link));
	exit;
}

$tb_url  = $_POST['url'];
// These three are stripslashed here so that they can be properly escaped after mb_convert_encoding()
$title     = stripslashes($_POST['title']);
$blog_name = stripslashes($_POST['blog_name']);
$excerpt   = (isset($_POST['excerpt'])) ? stripslashes($_POST['excerpt']) : "";

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	trackback_response(1, 'This is not a valid trackback. Title, Blog URL or Name missing');
}

$charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS';

if (isset($_POST['charset']))
	$charset = strtoupper( trim($_POST['charset']) );

// No valid uses for UTF-7
if ( false !== strpos($charset, 'UTF-7') )
	trackback_response(1, 'We do not support UTF-7.');

// now start to prepare everything for inserting the trackback
$tb_language = $ITEM->getLanguageID();
$tb_id = $ITEM->getID();
$LANGUAGE = new Language($tb_language);
$charsetItem = $LANGUAGE->getCharset();	

// For international trackbacks
if ( function_exists('mb_convert_encoding') ) {
	$title     = mb_convert_encoding($title, $charsetItem, $charset);
	$excerpt   = mb_convert_encoding($excerpt, $charsetItem, $charset);
	$blog_name = mb_convert_encoding($blog_name, $charsetItem, $charset);
}

if ( !empty($tb_url) && !empty($title) ) 
{
	header('Content-Type: text/xml; charset='.$charset);

	if ( !CommentService::activeRemoteComments($itemtype, $tb_id, $tb_language) )
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$title =  html_excerpt( $title, 250 ).'...';
	$excerpt = html_excerpt( $excerpt, 252 ).'...';

	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";

	// required for moderator email
	loadLanguageFile("comments-form"); 
	
	$dupe = CommentService::checkRemoteCommentDuplicate($itemtype, $tb_id, $tb_language, $comment_author_url);

	if ( $dupe ) {
		trackback_response(1, 'We already have a trackback from that URL for this post.');
		exit;
	}

	$cas = new CommentAdminService($itemtype);
	$res = $cas->createComment($tb_id, $tb_language, $comment_author, $comment_content, $comment_author_email, $comment_author_url, 'trackback');
	
	if($res === COMMENT_CREATED)
		trackback_response(0);
	else
		trackback_response(1, 'Trackbacks cannot be added right now.');
}

?>