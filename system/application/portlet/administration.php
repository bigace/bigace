<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @version $Id$
 * @author Kevin Papst
 */

if (!defined('_BIGACE_ID')) {
    die('Script not runnable alone');
}

if($GLOBALS['_BIGACE']['SESSION']->isAnonymous())
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
    return;
}

/**
 * Edit your Portlet settings with this Tool.
 */

define('PORTLET_SPACER_WIDTH',  '10px');
define('PORTLET_SELECT_WIDTH',  '170px');
define('PORTLET_IFRAME_WIDTH',  '100%');
define('PORTLET_BOX_HEIGHT',    '160px');

define('PORTLET_DIRECTORY',     'system/classes/portlets/');
define('PORTLET_IFRAME_NAME',   'portletAdminIframe');

define('PORTLET_JS_PARAM_DELIM',  '||');
define('PORTLET_JS_VALUE_DELIM',  '=');
define('PORTLET_JS_TOKEN_NAME', 'parameterName');
define('PORTLET_JS_TOKEN_VALUE','parameterValue');

define('PORTLET_PARAM_PORTLET', 'portlet');
define('PORTLET_COLUMN_FORM',   'column');

define('PORTLET_PARAM_MODE',    'mode');
define('PORTLET_PARAM_TYPE',    'type');

define('PORTLET_MODE_NEW',      'new');         // edit a fresh portlet
define('PORTLET_MODE_EDIT',     'edit');        // edit an already configured portlet
define('PORTLET_MODE_SAVE',     'save');        // save portlet configuration
define('PORTLET_MODE_NORMAL',   'normal');      // show portlet administration

define('PORTLET_ADMIN_FRIGHT',  'edit.portlet.settings'); // fright for the administration

define('_PUBLIC_IMAGE_DIR', _BIGACE_DIR_PUBLIC_WEB.'system/images/');

// edit portlets for the following item
define('PORTLET_ITEM_ID', $GLOBALS['_BIGACE']['PARSER']->getItemID());
define('PORTLET_LANGUAGE_ID', $GLOBALS['_BIGACE']['PARSER']->getLanguage());

// ------------------- used classes -------------------
import('api.portlet.PortletService');
import('classes.util.ApplicationLinks');
import('classes.fright.FrightService');
import('classes.right.RightService');
import('classes.menu.MenuService');
import('classes.util.IOHelper');
import('classes.util.LinkHelper');
import('classes.util.links.MenuChooserLink');

// load all needed translations
loadLanguageFile('portlet_admin', _ULC_);

// ----------------------------------- [START right check] -----------------------------------
// calculate if user is really allowed to administrate portlets
$FRIGHTSERVICE = new FrightService();
define('PORTLET_IS_ADMIN', $FRIGHTSERVICE->hasFright($GLOBALS['_BIGACE']['SESSION']->getUserID(), PORTLET_ADMIN_FRIGHT));
unset($FRIGHTSERVICE);

// users without the proper functional right get kicked
if(!PORTLET_IS_ADMIN)
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(PORTLET_ITEM_ID)) );
    return;
}

// now check the menu rights
$RIGHT_SERVICE = new RightService();
$CHECK_RIGHT = $RIGHT_SERVICE->getItemRight(_BIGACE_ITEM_MENU, PORTLET_ITEM_ID, $GLOBALS['_BIGACE']['SESSION']->getUserID());
$canWrite = $CHECK_RIGHT->canWrite();
unset($CHECK_RIGHT);
unset($RIGHT_SERVICE);
if(!$canWrite)
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'MissingRightException');
    ExceptionHandler::processCoreException( new MissingRightException('403', 'No authorization to write Item!', createMenuLink(PORTLET_ITEM_ID)) );
    return;
}
unset($canWrite);
// ----------------------------------- [END right check] -----------------------------------
// FETCH THE CONFIGURED PORTLET SERVICE
$services = ServiceFactory::get();
$parser = $services->getService('portlet');
$parser->setIgnoreDisplaySetting(true);
unset($services);

function getPortletParameterAsJSString($portlet)
{
    $params = $portlet->getAllParameter();
    $html = $portlet->getIdentifier();

    foreach($params AS $key => $value)
    {
        $html .= PORTLET_JS_PARAM_DELIM;
        $html .= $key . '=' . $value;
    }
    return $html;
}

/**
 * Returns an instance of the Portlet or null if this is not a valid Portlet for the Menu Layout.
 */
function getPortletObject($portlettype)
{
    if (class_exists($portlettype))
        return new $portlettype();

    $ms = new MenuService();
    $MENU = $ms->getMenu(PORTLET_ITEM_ID, PORTLET_LANGUAGE_ID);
    $ALL_PORTLETS = getAvailablePortlets($MENU);
    foreach($ALL_PORTLETS AS $portlet) {
        if(strcasecmp($portlettype, get_class($portlet)) == 0)
            return $portlet;
        if(strcasecmp($portlettype, $portlet->getIdentifier()) == 0)
            return $portlet;
    }

    return null;
}

function getDisplayName($portlet) {
    return $portlet->getTitle();
}

function getAvailablePortlets($MENU)
{
    $dir = _BIGACE_DIR_ROOT . '/' . PORTLET_DIRECTORY;
    $ALL_PORTLETS = array();
    $temp = IOHelper::getFilesFromDirectory($dir, 'php', false);
    foreach($temp AS $portletName)
    {
        if (!class_exists($portletName)) {
            include_once($dir . $portletName);
        }

        $portletName = str_replace('.php','',$portletName);
        if (class_exists($portletName)) {
            $ALL_PORTLETS[] = new $portletName();
        }
    }
/*
 // ------ OLD CODE BEFORE 2.1 -----------
    $MENU_LAYOUT = new Layout($MENU->getLayoutName());
    $temp = $MENU_LAYOUT->getPortletNames();
    foreach($temp AS $portletName)
    {
        if (!class_exists($portletName))
            loadClass('portlets', $portletName);

        if (class_exists($portletName))
            $ALL_PORTLETS[] = new $portletName();
    }
*/
    return $ALL_PORTLETS;
}

function getPortletFromJSString($js)
{
    $pieces = explode (PORTLET_JS_PARAM_DELIM, $js);
    $portletType = $pieces[0];
    $portlet = getPortletObject($portletType);
    if ($portlet != null)
    {
        if(is_subclass_of($portlet, 'Portlet'))
        {
            for($i=1; $i < count($pieces); $i++)
            {
                $params = explode(PORTLET_JS_VALUE_DELIM,$pieces[$i]);
                $key = $params[0];
                $value = $params[1];
                $portlet->setParameter($key, $value);
            }
            return $portlet;
        }
    }
    return null;
}

    $mode = extractVar(PORTLET_PARAM_MODE, PORTLET_MODE_NORMAL);

    /*
     * Save incoming values before we read them
     */
    if ($mode == PORTLET_MODE_SAVE)
    {
        $portletToSave = extractVar(PORTLET_PARAM_PORTLET, array());
        if(count($portletToSave) > 0)
        {
            foreach($portletToSave AS $columnName => $columnPortlets)
            {
                $columnName = substr($columnName, strlen(PORTLET_COLUMN_FORM));
                $allPortlets = array();
                for($i=0; $i < count($columnPortlets); $i++)
                {
                    $tempPortlet = getPortletFromJSString($columnPortlets[$i]);
                    if($tempPortlet != null)
                        $allPortlets[] = $tempPortlet;
                }
                $GLOBALS['LOGGER']->logDebug('Saving '.count($allPortlets).' Portlets for Column: '.$columnName.' and Item: '.PORTLET_ITEM_ID.'/'.PORTLET_LANGUAGE_ID);
                $parser->savePortlets(_BIGACE_ITEM_MENU, PORTLET_ITEM_ID, PORTLET_LANGUAGE_ID, $allPortlets, $columnName);
            }
        }
        else
        {
            // user submitted an empty list, save that!
            $GLOBALS['LOGGER']->logDebug('Saving empty Portlets for Item: '.PORTLET_ITEM_ID.'/'.PORTLET_LANGUAGE_ID);
            $parser->savePortlets(_BIGACE_ITEM_MENU, PORTLET_ITEM_ID, PORTLET_LANGUAGE_ID, array());
        }
    }


    // prepare always needed variables
    $ms = new MenuService();
    $MENU = $ms->getMenu(PORTLET_ITEM_ID, PORTLET_LANGUAGE_ID);
    $ALL_PORTLETS = getAvailablePortlets($MENU);
    if(ConfigurationReader::getConfigurationValue('system', 'use.smarty', true)) {
        import('classes.smarty.SmartyDesign');
        $MENU_LAYOUT = new SmartyDesign($MENU->getLayoutName());
    } else {
        import('classes.layout.Layout');
        $MENU_LAYOUT = new Layout($MENU->getLayoutName());
    }
    $COLUMNS = $MENU_LAYOUT->getPortletColumns();
    unset($MENU_LAYOUT);
    unset($ms);

    if ($COLUMNS == null) {
        $COLUMNS = array(PORTLET_DEFAULT_COLUMN);
    }

    if(!is_array($COLUMNS)) {
        $COLUMNS = array($COLUMNS);
    }

    if(count($COLUMNS) == 0) {
        $COLUMNS = array(PORTLET_DEFAULT_COLUMN);
    }

    $LANGUAGE = new Language($MENU->getLanguageID());
    ?>
    <!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
        <head>
            <title>BIGACE - <?php echo getTranslation('portlet_admin_title'); ?></title>
		    <title>.:: <?php echo $MENU->getName(); ?> ::.</title>
		    <meta name="description" content="<?php echo $MENU->getDescription(); ?>">
		    <meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>">
		    <meta name="robots" content="index,follow">
		    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $LANGUAGE->getCharset(); ?>">
            <style type="text/css">
		        .CopyrightFooter { margin-top:10px; }
		        .copyright { font-size: 10px; color: #444444; letter-spacing: -1px; }
                body, p, td {
                    font-family: Arial;
                    font-size: 11px;
                    line-height: 16px;
                    color: #333;
                }
                th {
                    font-weight: bold;
                    text-decoration: underline;
                    text-align: left;
                    font-family: Arial;
                    font-size: 12px;
                    line-height: 16px;
                    color: #333;
                }
                input, select {
                    font-family: Arial;
                    font-size: 11px;
                    line-height: 14px;
                    color: #333;
                }
                .menuID {
                    width:50px;
                }
            </style>
            <?php

		    unset ($LANGUAGE);

        if($mode == PORTLET_MODE_NEW || $mode == PORTLET_MODE_EDIT)
        {
            /* ----------------------------------------------------------------
             * HTML HEAD FOR:
             *
             * DETAIL VIEW - Edit settings for existing or new Portlets
             * ----------------------------------------------------------------
             */
            $couldLoadPortlet = false;

            $portlettype = extractVar(PORTLET_PARAM_TYPE, '');
            $portlet = null;
            if ($portlettype != '')
            {
                $portlet = getPortletObject($portlettype);

                if ($portlet != null)
                {
                    $couldLoadPortlet = true;
                    $params = $portlet->getAllParameter();
                }
            }
            ?>

            <script type="text/javascript">
            <!--
                var enterPositiveNumeric = '<?php echo getTranslation('portlet_js_pos_numeric'); ?>';
                var enterValidNumeric    = '<?php echo getTranslation('portlet_js_valid_numeric'); ?>';
                var enterValidMenuID     = '<?php echo getTranslation('portlet_js_valid_menuid'); ?>';
                var valueCanBeEmpty      = '<?php echo getTranslation('portlet_js_optional_empty'); ?>';

                String.prototype.trim = function(){
                    return this.replace(/(^\s+|\s+$)/g, "");
                };

                function isMenuID(s)
                {
                    if(s.length > 0)
                        return isFinite(s);
                    return false;
                }

                function isMenuIDOptional(s)
                {
                    if(s.length > 0)
                        return isFinite(s);
                    return true;
                }

                function isIntOptional(s)
                {
                    if(s.length > 0)
                        return isFinite(s);
                    return true;
                }

                function isInt(s)
                {
                    if(s.trim().length == 0)
                        return false;
                    return isFinite(s);
                }

                function isPositiveInt(s) {
                    if (isInt(s)) {
                        var arr = s.match(/\d/g);
                        return (arr.length == s.length);
                    }
                    return false;
                }

                function wrong(field, msg) {
                    alert(msg);
                    field.focus();
                    return false;
                }

                function checkParameter() {
                    <?php
                    if($couldLoadPortlet)
                    {
                        $i=0;
                        foreach($params AS $key => $value)
                        {
                            $type = $portlet->getParameterType($key);
                            switch($type)
                            {
                                case PORTLET_TYPE_INT:
                                    echo "if(!isInt(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidNumeric);\n";
                                    break;
                                case PORTLET_TYPE_INT_OPTIONAL:
                                    echo "if(!isIntOptional(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidNumeric);\n";
                                    break;
                                case PORTLET_TYPE_INT_POSITIVE:
                                    echo "if(!isPositiveInt(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterPositiveNumeric);\n";
                                    break;
                                case PORTLET_TYPE_MENUID:
                                    echo "if(!isMenuID(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidMenuID);\n";
                                    break;
                                case PORTLET_TYPE_MENUID_OPTIONAL:
                                    echo "if(!isMenuIDOptional(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidMenuID + ' ' + valueCanBeEmpty);\n";
                                    break;
                                default:
                                    break;
                            }
                            $i++;
                        }
                    }
                    ?>
                    return true;
                }

                function chooseMenuID(jsfunc, inputid)
                {
                    <?php
                    $link = new MenuChooserLink();
                    $link->setItemID(PORTLET_ITEM_ID);
                    $link->setJavascriptCallback('"+jsfunc');
                    ?>
                    fenster = open("<?php echo LinkHelper::getUrlFromCMSLink($link); ?>,"SelectParent","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
                    bBreite=screen.width;
                    bHoehe=screen.height;
                    fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
                }

            <?php
            if($couldLoadPortlet)
            {
                foreach($params AS $key => $value)
                {
                    $type = $portlet->getParameterType($key);
                    if ($type == PORTLET_TYPE_MENUID || $type == PORTLET_TYPE_MENUID_OPTIONAL)
                    {
                        ?>

                function setMenu<?php echo $key; ?>(id, language, name)
                {
                    document.getElementById('<?php echo $key; ?>').value = id;
                    document.getElementById('<?php echo $key; ?>_name').value = name;
                }

                        <?php
                    }
                }
            }
            ?>


                function getPortletParameter() {
                    var myform = document.getElementById('portletDetailForm');
                    var returnParameterStringArray = new Array();
                    <?php
                    if($couldLoadPortlet)
                    {
                        echo "returnParameterStringArray = new Array(".count($params).");\n";
                        $i=0;
                        foreach($params AS $key => $value)
                        {
                            echo "returnParameterStringArray[$i] = new Array(2);\n";
                            echo "returnParameterStringArray[$i][\"".PORTLET_JS_TOKEN_NAME."\"] = escape('$key');\n";
                            $type = $portlet->getParameterType($key);
                            switch($type)
                            {
                                default:
                                    echo "returnParameterStringArray[$i][\"".PORTLET_JS_TOKEN_VALUE."\"] = escape(document.getElementById('$key').value);\n";
                                    break;
                            }
                            $i++;
                        }
                    }
                    ?>
                    return returnParameterStringArray;
                }

                <?php
                if($couldLoadPortlet && $portlet != null)
                {
                    ?>
                parent.checkParameter       = new Function("", "return checkParameter();");
                parent.getPortletTitle      = new Function("", "return '<?php echo $portlet->getTitle(); ?>';");
                parent.getPortletType       = new Function("", "return '<?php echo $portlet->getIdentifier(); ?>';");
                parent.getPortletParameter  = new Function("", "return getPortletParameter();");
                    <?php
                }
                ?>

            // -->
            </script>
            <?php
        }
        else
        {
            /* ----------------------------------------------------------------
             * HTML HEAD FOR:
             *
             * PORTLET ADMINISTRATION START FRAME - Displays Select Box with
             * all possible and one for all current configured Portlets and
             * an Iframe to diplay Portlet settings.
             * ----------------------------------------------------------------
             */
            require_once(_BIGACE_DIR_ADMIN . 'styling.php');

            define('IMAGE_ARROW_DOWN', _BIGACE_DIR_STYLE.'down.png');  // _PUBLIC_IMAGE_DIR . arrow_down.gif
            define('IMAGE_ARROW_UP', _BIGACE_DIR_STYLE.'up.png');
            define('IMAGE_DELETE', _BIGACE_DIR_STYLE.'delete.png');

            ?>

            <link rel="stylesheet" href="<?php echo $GLOBALS['_BIGACE']['style']['class']->getCSS(); ?>" type="text/css">
            <style type="text/css">
                select {
                    border:1px solid #000000;
                    width:<?php echo PORTLET_SELECT_WIDTH; ?>;
                    height:<?php echo PORTLET_BOX_HEIGHT; ?>;
                }
                #portlets {
                    margin-bottom:5px;
                    border:1px solid #000000;
                }
                iframe {
                    height: <?php echo PORTLET_BOX_HEIGHT; ?>;
                    width: <?php echo PORTLET_IFRAME_WIDTH; ?>;
                    border:1px solid #000000;
                    background-color:#ffffff;
                }
            </style>
            <script type="text/javascript">
            <!--
                window.focus();

                var selectOptionMsg         = '<?php echo getTranslation('portlet_js_choose_portlet'); ?>';
                var addContentFirstMsg      = '<?php echo getTranslation('portlet_js_add_portlet'); ?>';
                var enterRequiredValues     = '<?php echo getTranslation('portlet_js_required_fields'); ?>';

                function savePortlets()
                {
                    var saveForm = document.getElementById('portletSaveForm');
                    for(i=0; i < document.forms.length; i++)
                    {
                        var tempForm = document.forms[i];
                        for(a=0; a < tempForm.elements.length; a++)
                        {
                            var tempSelect = tempForm.elements[a];
                            if(tempSelect.type.indexOf('select') != -1)
                            {
                                if(tempSelect.options.length == 0)
                                {
                                    var mynewHidden = document.createElement("input");
                                    // type hidden
                                    var attType = document.createAttribute("type");
                                    attType.nodeValue = "hidden";
                                    mynewHidden.setAttributeNode(attType);
                                    // name
                                    var attName = document.createAttribute("name");
                                    attName.nodeValue = "<?php echo PORTLET_PARAM_PORTLET; ?>["+tempForm.name+"][]";
                                    mynewHidden.setAttributeNode(attName);

                                    var attValue = document.createAttribute("value");
                                    attValue.nodeValue = "";
                                    mynewHidden.setAttributeNode(attValue);

                                    saveForm.appendChild(mynewHidden);
                                }
                                else
                                {
                                    for(e=0; e < tempSelect.options.length; e++)
                                    {
                                        var tempElem = tempSelect.options[e];
                                        //alert(tempElem.text + "=" + tempElem.value);

                                        // create input
                                        var mynewHidden = document.createElement("input");
                                        // type hidden
                                        var attType = document.createAttribute("type");
                                        attType.nodeValue = "hidden";
                                        mynewHidden.setAttributeNode(attType);
                                        // name
                                        var attName = document.createAttribute("name");
                                        attName.nodeValue = "<?php echo PORTLET_PARAM_PORTLET; ?>["+tempForm.name+"][]";
                                        mynewHidden.setAttributeNode(attName);

                                        var attValue = document.createAttribute("value");
                                        attValue.nodeValue = tempElem.value;
                                        mynewHidden.setAttributeNode(attValue);

                                        saveForm.appendChild(mynewHidden);
                                    }
                                }
                                //alert(tempForm.name + "=" + tempSelect.name + "=" + tempSelect.type);
                            }
                        }
                    }
                    return true;
                }

                function showNewPortlet(portlettype) {
                    var url = "<?php echo ApplicationLinks::getPortletAdminURL(PORTLET_ITEM_ID, array(PORTLET_PARAM_MODE => PORTLET_MODE_NEW, PORTLET_PARAM_TYPE => '"+portlettype')); ?>;
                    document.getElementById('<?php echo PORTLET_IFRAME_NAME; ?>').src = url;
                }

                function addPortlet(select)
                {
                    var myframe = document.getElementById('<?php echo PORTLET_IFRAME_NAME; ?>');

                    if (typeof(getPortletParameter) != "function")
                    {
                        alert(selectOptionMsg);
                    }
                    else
                    {
                        if(checkParameter())
                        {
                            var parameters = getPortletParameter();
                            /*
                            if(parameters == '')
                            {
                                alert(enterRequiredValues);
                            }
                            else
                            {
                            */
                                parameterString = getPortletType();
                                for(i=0;i<parameters.length;i++){
                                    parameterString += "<?php echo PORTLET_JS_PARAM_DELIM; ?>";
                                    parameterString += parameters[i]['<?php echo PORTLET_JS_TOKEN_NAME; ?>'];
                                    parameterString += "<?php echo PORTLET_JS_VALUE_DELIM; ?>";
                                    parameterString += parameters[i]['<?php echo PORTLET_JS_TOKEN_VALUE; ?>'];
                                }

                                 neueOption = new Option(getPortletTitle(),parameterString,false,false);
                                 select.options[select.options.length] = neueOption;
                                 //alert(getPortletTitle() + " = " + parameterString);
                            //}
                        }
                    }
                }

                function remove(selectbox)
                {
                    if ( selectbox.length > 0 && selectbox.selectedIndex != -1 )
                    {
                        bla = selectbox.selectedIndex;
                        selectbox.options[bla] = null;
                        if (selectbox.options.length > bla)
                            selectbox.selectedIndex = bla;
                        else if (selectbox.options.length > bla -1)
                            selectbox.selectedIndex = bla - 1;
                    }
                    else
                    {
                        alert(selectOptionMsg);
                    }
                }

                function moveUp(selectbox){
                    if ( selectbox.length > 1 && selectbox.selectedIndex > 0 ){
                        if ( isIndexSelected(selectbox,selectOptionMsg,addContentFirstMsg) ){
                            storedIndex = selectbox.selectedIndex;
                            itemToBeMoved = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
                            itemToBeSwitched = new Option(selectbox.options[storedIndex-1].text, selectbox.options[storedIndex-1].value);
                            selectbox.options[storedIndex-1] = itemToBeMoved;
                            selectbox.options[storedIndex] = itemToBeSwitched;
                            selectbox.selectedIndex = storedIndex-1;
                        }
                    }
                }

                function moveDown(selectbox){
                    if ( selectbox.length > 1 && selectbox.selectedIndex < selectbox.length-1 ){
                        if ( isIndexSelected(selectbox,selectOptionMsg,addContentFirstMsg) ){
                            storedIndex = selectbox.selectedIndex;
                            itemToBeMoved = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
                            itemToBeSwitched = new Option(selectbox.options[storedIndex+1].text, selectbox.options[storedIndex+1].value);
                            selectbox.options[storedIndex+1] = itemToBeMoved;
                            selectbox.options[storedIndex] = itemToBeSwitched;
                            selectbox.selectedIndex = storedIndex+1;
                        }
                    }
                }
                function isIndexSelected(selectbox,msgSelect,msgNoContent){
                    if ( selectbox.selectedIndex == -1 ){
                        alert(unescape(msgSelect));
                        return false;
                    } else if( selectbox.selectedIndex == 0 && selectbox.options[0].value == "" ){
                        alert(unescape(msgNoContent));
                        return false;
                    } else {
                        return true;
                    }
                }
                function closePortletAdmin() {
/*
//TODO add save before close
                    if(confirm('Wollen Sie speichern bevor Sie beenden?')) {
                        alert('Sorry, aber das wird erst in einer spaeteren Version eingebaut... ;-)');
                    }
*/
                    window.close();
                }
            // -->
            </script>

            <?php
        }
        ?>
        </head>
        <body style="margin:0px;">
        <?php
            if($mode == PORTLET_MODE_NEW || $mode == PORTLET_MODE_EDIT)
            {
                /* ----------------------------------------------------------------
                 * BODY FOR:
                 *
                 * DETAIL VIEW - Edit settings for existing or new Portlets
                 * ----------------------------------------------------------------
                 */
                $portlettype = extractVar(PORTLET_PARAM_TYPE, '');
                if ($portlettype == '')
                {
                    echo '<b>'.getTranslation('portlet_err_missing_type').'</b>';
                }
                else
                {
                    $portlet = getPortletObject($portlettype);
                    if ($portlet == null)
                    {
                        echo '<b>'.getTranslation('portlet_err_unknown_type').' "'.$portlettype.'"</b>';
                    }
                    else
                    {
                        ?>
                        <form name="portletDetailForm" id="portletDetailForm" action="" method="POST">
                        <input type="hidden" name="<?php echo PORTLET_PARAM_TYPE; ?>" value="<?php echo $portlet->getIdentifier(); ?>">
                        <p><b><?php echo getTranslation('portlet_detail_name'); ?>:</b> <?php echo getDisplayName($portlet); ?></p>
                        <?php
                        if(count($portlet->getAllParameter()) > 0)
                        {
                            ?>
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <th><?php echo getTranslation('portlet_detail_key'); ?></th>
                                <th><?php echo getTranslation('portlet_detail_value'); ?></th>
                            </tr>
                            <?php

                            foreach($portlet->getAllParameter() AS $key => $value)
                            {
                                echo "<tr>\n";
                                echo "<td><b>".$portlet->getParameterName($key).":</b></td>\n";
                                echo "<td>\n";
                                $type = $portlet->getParameterType($key);
                                switch($type)
                                {
                                    case PORTLET_TYPE_MENUID:
                                        echo '<input type="text" id="'.$key.'_name" name="'.$key.'_name" value="" disabled="disabled">';
                                        echo '<input class="menuID" type="text" id="'.$key.'" name="'.$key.'" value="" disabled="disabled">';
                                        echo ' <button onclick="chooseMenuID(\'setMenu'.$key.'\', \''.$key.'\'); return false;">'.getTranslation('portlet_choose', 'Choose').'</button>';
                                        break;
                                    case PORTLET_TYPE_MENUID_OPTIONAL:
                                        echo '<input type="text" id="'.$key.'_name" name="'.$key.'_name" value="" disabled="disabled">';
                                        echo '<input class="menuID" type="text" id="'.$key.'" name="'.$key.'" value="">';
                                        echo ' <button onclick="chooseMenuID(\'setMenu'.$key.'\', \''.$key.'\'); return false;"> '.getTranslation('portlet_choose', 'Choose').'</button>';
                                        break;
                                    case PORTLET_TYPE_STRING:
                                        echo '<input type="text" id="'.$key.'" name="'.$key.'" value="'.$value.'">';
                                        break;
                                    case PORTLET_TYPE_BOOLEAN:
                                        echo '<select id="'.$key.'" name='.$key.'">';
                                        echo '<option value="1"';
                                        if ($value == true)
                                            echo ' selected';
                                        echo '>'.getTranslation('portlet_boolean_true').'</option>';
                                        echo '<option value="0"';
                                        if ($value == false)
                                            echo ' selected';
                                        echo '>'.getTranslation('portlet_boolean_false').'</option>';
                                        echo "</select>\n";
                                        break;
                                    default:
                                        echo '<input type="text" id="'.$key.'" name="'.$key.'" value="'.$value.'">';
                                        break;
                                }
                                echo "</td>\n";
                                echo "</tr>\n";
                            }
                            echo "</table>\n";
                        }
                        echo "</form>\n";
                    }
                }
            }
            else
            {
                /* ----------------------------------------------------------------
                 * BODY FOR:
                 *
                 * PORTLET ADMINISTRATION START FRAME - Displays Select Box with
                 * all possible and one for all current configured Portlets and
                 * an Iframe to diplay Portlet settings.
                 * ----------------------------------------------------------------
                 */
                ?>
                <table border="0" width="100%" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table border="0" width="100%" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><b><?php echo getTranslation('portlet_available_box'); ?></b></td>
                                <td align="center" style="width:<?php echo PORTLET_SPACER_WIDTH; ?>">&nbsp;</td>
                                <td><b><?php echo getTranslation('portlet_details_box'); ?></b></td>
                            </tr>
                            <tr>
                                <td>
                                    <select onchange="showNewPortlet(this.options[this.options.selectedIndex].value)" size="10">
                                    <?php
                                        foreach($ALL_PORTLETS AS $portlet)
                                        {
                                            echo '<option value="'.$portlet->getIdentifier().'">'.getDisplayName($portlet).'</option>' . "\n";
                                        }
                                    ?>
                                    </select>
                                </td>
                                <td align="center" style="width:<?php echo PORTLET_SPACER_WIDTH; ?>">&nbsp;</td>
                                <td style="width:100%" align="left" <?php if ($COLUMNS > 1) { echo 'colspan="'.$COLUMNS.'"'; } ?>>
                                    <iframe src="" name="<?php echo PORTLET_IFRAME_NAME; ?>" id="<?php echo PORTLET_IFRAME_NAME; ?>" border="0" frameborder="0"></iframe>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
                            <tr>
                            <?php
                            foreach($COLUMNS AS $COLUMN_NAME)
                            {
                                ?>
                                <td align="center">
                                    <form name="<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>" id="<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>">
                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td colspan="2" align="center">
                                                <a href="#" onclick="addPortlet(document.getElementById('<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>').portlets);return false;"><img src="<?php echo IMAGE_ARROW_DOWN; ?>" border="0"></a><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <b><?php echo getTranslation('portlet_column_box') . ' ' . $COLUMN_NAME; ?></b><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <select name="portlets" id="portlets" size="10">
                                                <?php
                                                    $configuredPortlets = $parser->getPortlets(_BIGACE_ITEM_MENU, PORTLET_ITEM_ID, PORTLET_LANGUAGE_ID, $COLUMN_NAME);
                                                    foreach($configuredPortlets AS $portlet)
                                                    {
                                                        echo '<option value="'.getPortletParameterAsJSString($portlet).'">'.getDisplayName($portlet).'</option>' . "\n";
                                                    }
                                                ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="moveUp(document.forms['<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>'].portlets); return false;"><img src="<?php echo IMAGE_ARROW_UP; ?>" border="0"></a>
                                                <a href="#" onclick="moveDown(document.forms['<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>'].portlets); return false;"><img src="<?php echo IMAGE_ARROW_DOWN; ?>" border="0"></a>
                                            </td>
                                            <td align="right">
                                                <a href="#" onclick="remove(document.forms['<?php echo PORTLET_COLUMN_FORM . $COLUMN_NAME; ?>'].portlets); return false;"><img src="<?php echo IMAGE_DELETE; ?>" border="0"></a>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                </td>
                                <?php
                            }
                            ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <form style="display:inline;" id="portletSaveForm" name="portletSaveForm" action="<?php echo ApplicationLinks::getPortletAdminURL(PORTLET_ITEM_ID); ?>" method="POST">
                            <input type="hidden" name="<?php echo PORTLET_PARAM_MODE; ?>" value="<?php echo PORTLET_MODE_SAVE; ?>">
                            <button type="submit" onclick="return savePortlets();"><?php echo getTranslation('portlet_save', 'Save'); ?></button>
                        </form>
                        &nbsp;<button onclick="closePortletAdmin()"><?php echo getTranslation('portlet_close', 'Close'); ?></button>
                    </td>
                </tr>
                </table>
                <?php
            }

        ?>
        </body>
    </html>
