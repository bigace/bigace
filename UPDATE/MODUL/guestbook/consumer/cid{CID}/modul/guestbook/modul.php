<?php
/**
* The BIGACE Guestbook
*
* This script needs some CSS classes!
*
* #guestbook .gbheader { margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid #999999; }
* #guestbook .pagelink { margin:6px;padding:2px 5px 2px 0px;border:1px solid #000000;background-color:#F6F6F6; }
* #guestbook .pagelinkSelected { margin:6px;padding:2px 5px 2px 0px;border:1px solid #000000;color:#000000;background-color:#ffffff; }
* #guestbook .imglink { padding-right:5px; }
* #guestbook .homelink { border-width:0px; }
* #guestbook .gbpages { margin:20px 0px 10px 0px;text-align:center; }
* #guestbook .gbEntry { width:500px;border:1px solid #C6C6CC;background-color:#F6F6F6;padding:5px;margin-bottom:10px }
* #guestbook .gbEntryTitle { font-weight:bold;margin-top:0px; }
* #guestbook .gbEntryComment { margin-bottom:0px; }
*
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
* @package bigace.modul
*/

import('classes.guestbook.Guestbook');
import('classes.guestbook.GuestbookEnumeration');
import('classes.guestbook.GuestbookAdminService');
import('classes.util.html.FormularHelper');
import('classes.modul.ModulService');
import('classes.modul.Modul');

// property names
define('GB_PROP_NAME_BOTTOM', 'guestbook_show_bottom_links');
define('GB_PROP_NAME_TOP', 'guestbook_show_top_links');
define('GB_PROP_NAME_LIMIT', 'guestbook_entrys_on_page');
define('GB_PROP_NAME_CSS', 'guestbook_use_own_css');
define('GB_PROP_NAME_BLACKLIST', 'guestbook_blacklist_words');
define('GB_PROP_NAME_CAPTCHAS', 'guestbook_use_catchas');
define('GB_PROP_NAME_EMAIL', 'guestbook_send_email');

$config = array(
			GB_PROP_NAME_BOTTOM 		=> false,
			GB_PROP_NAME_TOP 			=> true,
			GB_PROP_NAME_LIMIT 			=> 5,
			GB_PROP_NAME_CSS 			=> true,
			GB_PROP_NAME_CAPTCHAS 		=> true,
			GB_PROP_NAME_EMAIL 			=> true,
			'guestbook_show_disclaimer' => true,
			'guestbook_show_email' 		=> false,
			'guestbook_no_follow' 		=> true,
			);

define('GUESTBOOK_PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB . 'cid'._CID_.'/guestbook/');
define('GUESTBOOK_MODE_CREATE', 'createEntry');

$showEntrys = false; // whether the entrys will be shown or not
$mode = extractVar('mode', 'view');
$data = extractVar('data', array());

$modulService = new ModulService();
$modul = new Modul($MENU->getModulID());
$config = $modulService->getModulProperties($MENU, $modul, $config);

// limit of entrys on one page
define('GUESTBOOK_PAGE_ENTRY_LIMIT', $config[GB_PROP_NAME_LIMIT]);

/* #########################################################################
 * ############################   Show CSS Link   ##########################
 * #########################################################################
 */
if ($config[GB_PROP_NAME_CSS])
{
    ?>
    <link rel="stylesheet" href="<?php echo GUESTBOOK_PUBLIC_DIR; ?>guestbook.css" type="text/css">
    <?php
}

echo '<div id="guestbook">'; 

/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin())
{
    import('classes.util.links.ModulAdminLink');
    import('classes.util.LinkHelper');
    $mdl = new ModulAdminLink();
    $mdl->setItemID($MENU->getID());
    $mdl->setLanguageID($MENU->getLanguageID());

    ?>
    <script type="text/javascript">
    <!--
    function openAdmin()
    {
        fenster = open("<?php echo LinkHelper::getUrlFromCMSLink($mdl); ?>","ModulAdmin","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
        bBreite=screen.width;
        bHoehe=screen.height;
        fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
    }
    // -->
    </script>
    <?php

    echo '<div class="modulAdminLink" align="left"><a onClick="openAdmin(); return false;" href="'.LinkHelper::getUrlFromCMSLink($mdl).'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/preferences.gif" border="0" align="top"> '.getTranslation('gb_admin').'</a></div>';
}

/* #########################################################################
 * ##########################  Show create Formular ########################
 * #########################################################################
 */
if ($mode == GUESTBOOK_MODE_CREATE) 
{
    /** 
    * Create new entry with posted data
    */
    $gb_admin = new GuestbookAdminService();
    $error = FALSE;
    $errorMsg = '';

    if (isset($data['name']) &&  $data['name'] != '' &&  isset($data['comment']) && $data['comment'] != '') 
    {
        $blacklist = explode(',',$config[GB_PROP_NAME_BLACKLIST]);
        $blackword = containsBlacklistWord($data['comment'], $blacklist);
		if($blackword === false) {
			
			$create = !$config[GB_PROP_NAME_CAPTCHAS];
			 
			if($config[GB_PROP_NAME_CAPTCHAS]) 
			{
				$captcha = ConfigurationReader::getValue("system", "captcha", null);
				if($captcha == null) 
				{
		        	$GLOBALS['LOGGER']->logError("Captcha failed, wrong configuration: 'system/captcha'");
				}
				else 
				{
					if(isset($data['attempt']) && isset($data['image']) && $captcha->validate($data['attempt'], $data['image'])) {
		   	 			$create = true;
					}
					else {
		    	        $error = TRUE;
		    	        $errorMsg = getTranslation('gb_captcha_failed');
		   	 			$create = false;
					}
				}
			}
			
			if($create)
			{
       	        $res = $gb_admin->createEntry($data['name'], $data['email'], $data['hp'], $data['comment']);
			
				if(strlen(trim($config[GB_PROP_NAME_EMAIL])) > 0) {
					mail($config[GB_PROP_NAME_EMAIL],getTranslation('gb_title_email'),getTranslation('gb_body_email') . createMenuLink($GLOBALS['_BIGACE']['PARSER']->getItemID()));
				}

				$showEntrys = true;
			}
		} 
		else {
	        $error = TRUE;
	        $errorMsg = getTranslation('gb_blackword') . ' <b>' . $blackword . '</b>';
		}
    } 
    else 
    {
        $error = TRUE;
        $errorMsg = getTranslation('gb_msg_enter_values');
    }

    if ($error)
    {
		showGBError($errorMsg);
	    echo '&nbsp;' . editEntry($data,$config[GB_PROP_NAME_CAPTCHAS]) . '&nbsp;';
    } 

    unset ($res);
    unset ($error);
    unset ($gb_admin);
}
else if ($mode == "new") 
{
    echo '&nbsp;' . editEntry($data,$config[GB_PROP_NAME_CAPTCHAS]) . '&nbsp;';
} 
else
{
	$showEntrys = true;
}

if($showEntrys)
{
	// show the listing with the configured amount of entrys
    viewEntrys(extractVar('start', '0'), $config);
}

unset ( $mode );
unset ( $data );

echo '</div>'; 


/**
 * Try to find out, if any of the submitted words in in the Blacklist.
 */
function containsBlacklistWord($stringToParse,$blacklistWords){
    $stringToParse = strtolower($stringToParse);
    foreach($blacklistWords as $blacklistWord){
        if ( strpos($stringToParse,strtolower($blacklistWord)) !== false )
            return $blacklistWord;
    }
    return false;
}

function showGBError($msg) {
	echo '<br><br><p style="color:red" align="center">'.$msg.'</p>';
}

/**
* Show a list of Guestbook Entrys.
*/
function viewEntrys($from, $config) 
{
	$showTopLinks = $config[GB_PROP_NAME_TOP];
	$showBottomLinks = $config[GB_PROP_NAME_BOTTOM];
	$showDisclaimer = $config['guestbook_show_disclaimer']; 
	$showEmail = $config['guestbook_show_email'];
	$useNofollow = $config['guestbook_no_follow'];
	
    $gb_info = new GuestbookEnumeration($from,GUESTBOOK_PAGE_ENTRY_LIMIT);
    $entries = $gb_info->countEntrys();

    ?>
    <div id="guestbook">
        <div class="gbheader">
            <?php if($showDisclaimer) { ?>
            <p class="gbimpressum">
                <img src="<?php echo GUESTBOOK_PUBLIC_DIR . 'title.gif'; ?>" align="left" style="margin-right:10px;" alt="">
                <?php echo getTranslation('gb_impressum'); ?>
            </p>
            <?php
			}

	        echo '<p align="right"><a href="'.createMenuLink($GLOBALS['_BIGACE']['PARSER']->getItemID(), array('mode'=>'new')).'" title="'.getTranslation('gb_new_link').'">';
	        echo '<img src="'.GUESTBOOK_PUBLIC_DIR.'create.gif" style="margin-right:5px;" alt="">';
	        echo getTranslation('gb_new_link');
	        echo '</a></p>';
            ?>
        </div>
        <?php

        if ($showTopLinks || $showBottomLinks)
        {
            $bla = $gb_info->countAllEntrys();
            $iitemp = $bla[0] / GUESTBOOK_PAGE_ENTRY_LIMIT;
            $links = '';
            if ($iitemp > 1 ) {
                for ($i=0; $i < $iitemp; $i++) 
                {
                    $links .= '<a href="'.createMenuLink( $GLOBALS['MENU']->getID(), array('start' => $i*GUESTBOOK_PAGE_ENTRY_LIMIT) ).'" title="" class="';
                    if ($i+1 > ($from / GUESTBOOK_PAGE_ENTRY_LIMIT) && $i < ($from / GUESTBOOK_PAGE_ENTRY_LIMIT)+1) {
                       $links .= 'pagelinkSelected';
                    } else {
                       $links .= 'pagelink';
                    }
                    
                    $links .= '"><img src="'.GUESTBOOK_PUBLIC_DIR.'arrow.gif" class="imglink" alt="'.getTranslation('gb_page').' '.($i+1).'">'.getTranslation('gb_page').' '.($i+1).'</a>';
                }
            }
            
            if ($showTopLinks && $iitemp > 1)
            {
                ?>
                <div class="gbpages">
                <p><?php echo $links; ?></p>
                </div>
                <?php
            }
        }
    
        for ($i = 0; $i < $entries; $i++) 
        {
            $current_gb = $gb_info->getNextEntry();
            
            $name = $current_gb->getName();
            if ($showEmail && $current_gb->getEmail() != "") {
                if ($name != "" && $name != "@") {
                    $name = '<a href="mailto:'.$current_gb->getEmail().'">'.$name.'</a>';
                }
            }
    
            ?>
    
            <div class="gbEntry">
                <p class="gbEntryTitle">
                    <?php 
                    if ($current_gb->getHomepage() != "" && $current_gb->getHomepage() != "http://") {
                    	if($useNofollow) echo '<a rel="nofollow"';
                    	else echo '<a';
                    	
                    	echo ' href="'.$current_gb->getHomepage().'" target="_blank" class="homelink" title="'.$current_gb->getHomepage().'"><img src="'.GUESTBOOK_PUBLIC_DIR.'home.gif" alt="'.getTranslation('gb_hp_link_alt').'"></a>';
                    }
                    echo ' ' . $name . ' ' . getTranslation('gb_wrote') . ' ' . date("d.m.Y", $current_gb->getEntryDate()); 
                    ?> 
                </p>
                <p class="gbEntryComment">
                    <?php echo str_replace("\n", "<br>", $current_gb->getComment()); ?>
                </p>
            </div>
    
            <?php
        }
        
        if($showBottomLinks && $iitemp > 1)
        {
        ?>
        <div class="gbpages">
        <p><?php echo $links; ?></p>
        </div>
        <?php
        }
        ?>

    </div>
    <?php
}


/**
* Edit new Guestbook Entry
*/
function editEntry($data, $useCaptcha)
{
    if ( !isset($data['name']) )    { $data['name'] = ''; }
    if ( !isset($data['hp']) )      { $data['hp'] = 'http://'; }
    if ( !isset($data['email']) )   { $data['email'] = ''; }
    if ( !isset($data['comment']) ) { $data['comment'] = ''; }
    if ( !isset($data['title']) )   { $data['title'] = getTranslation('gb_create_entry'); }
    if ( !isset($data['gbid']) )    { $data['gbid'] = ''; }
    if ( !isset($data['mode']) )    { $data['mode'] = GUESTBOOK_MODE_CREATE; }
    if ( !isset($data['submit']) )  { $data['submit'] = getTranslation('gb_save'); }

	$entries = array(
            getTranslation('gb_name')       => createTextInputType('name', $data['name'], '255'),
            getTranslation('gb_email')      => createTextInputType('email', $data['email'], '255'),
            getTranslation('gb_homepage')   => createTextInputType('hp', $data['hp'], '255'),
            getTranslation('gb_comment')    => createTextArea('comment',$data['comment'],'5','40'),
    );

	if($useCaptcha) {
		$captcha = ConfigurationReader::getValue("system", "captcha", null);
		if($captcha == null) {
        	$GLOBALS['LOGGER']->logError("Captcha failed, wrong configuration: 'system/captcha'");
		}
		else {
		    $ccode = $captcha->get();
		    $captchaImg = '<img src="'.$ccode.'" alt="'.getTranslation('gb_captcha_alt').'" title=""><br>'."\n";
            $entries[getTranslation('gb_captcha')] = $captchaImg . getTranslation('gb_captcha_info') . "<br>" . createTextInputType('attempt', '', '255');
		}
	}

    $configTable = array(
                    'width'         =>  '100%',
                    'valign'        =>  array (
                                            'left'      =>  'top'
                                        ),
                    'align'         =>  array (
                                            'table'     =>  'left',
                                            'left'      =>  'left'
                                        ),
                    'image'         =>  GUESTBOOK_PUBLIC_DIR . 'create.gif',
                    'title'         =>  $data['title'],
                    'form_action'   =>  createMenuLink($GLOBALS['MENU']->getID()),
                    'form_method'   =>  'post',
                    'form_hidden'   =>  array(
                                            'mode'         =>  $data['mode'],
                                            'data[image]'  =>  $ccode,
                                            'data[gbid]'   =>  $data['gbid']
                                    ),
                    'entries'       =>  $entries,
                    'form_submit'   =>  true,
                    'submit_label'  =>  $data['submit']
    );
    unset($data);
    return createTable($configTable);
}

?>