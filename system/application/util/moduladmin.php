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
 * Edit your Pages Modul settings with this Tool.
 */

define('MODUL_ADMIN_PARAM_MODE',        'mode');        // url parameter defining the mode
define('MODUL_ADMIN_PARAM_TEXT',        'text_');		// project text values
define('MODUL_ADMIN_PARAM_NUM',         'num_');		// project numeric values

define('MODUL_ADMIN_MODE_SAVE',         'save');        // save modul configuration
define('MODUL_ADMIN_MODE_NORMAL',       'normal');      // show modul administration

define('_PUBLIC_IMAGE_DIR', _BIGACE_DIR_PUBLIC_WEB.'system/images/');

// ------------------- used classes -------------------
import('classes.modul.ModulService'); // at least for config type constants
import('classes.modul.Modul');
import('classes.menu.MenuService');
import('classes.util.LinkHelper');
import('classes.util.links.ModulAdminLink');
import('classes.util.links.MenuChooserLink');
import('classes.item.ItemProjectService');
import('classes.item.ItemAdminService');
// -----------------------------------------------------


// -------------------- environment --------------------

// load all needed translations
loadLanguageFile('moduladmin', _ULC_);

// Load the requested Menu and set it as global variable
$MENU_SERVICE = new MenuService();
$MENU = $MENU_SERVICE->getMenu( $GLOBALS['_BIGACE']['PARSER']->getItemID(), $GLOBALS['_BIGACE']['PARSER']->getLanguage() );

// edit modul for the following item
define('MODUL_ITEM_ID', $MENU->getID());
define('MODUL_LANGUAGE_ID', $MENU->getLanguageID());

$LANGUAGE = new Language(MODUL_LANGUAGE_ID);
header( "Content-Type:text/html; charset=" . $LANGUAGE->getCharset() );


$modulID = $MENU->getModulID();
if(isset($_GET['modul']))
	$modulID = $_GET['modul'];
if(isset($_POST['modul']))
	$modulID = $_POST['modul'];


$MODUL = new Modul($modulID);
define('MODUL_IS_ADMIN', $MODUL->isModulAdmin());

$mdl = new ModulAdminLink($MODUL->getID());
$mdl->setItemID($MENU->getID());
$mdl->setLanguageID($MENU->getLanguageID());

$mode = extractVar(MODUL_ADMIN_PARAM_MODE, MODUL_ADMIN_MODE_NORMAL);

$projectService = new ItemProjectService(_BIGACE_ITEM_MENU);
// -----------------------------------------------------

if(MODUL_IS_ADMIN)
{
    if($mode == MODUL_ADMIN_MODE_SAVE) {
        $ADMIN_SERVICE = new ItemAdminService(_BIGACE_ITEM_MENU);
        foreach($_POST AS $key => $value) {
//                echo $key . '='. $value.'<br>';
            if(strpos($key,MODUL_ADMIN_PARAM_TEXT) !== FALSE) {
                $key = str_replace(MODUL_ADMIN_PARAM_TEXT, '', $key);
                if (!$ADMIN_SERVICE->setProjectText($MENU->getID(), $MENU->getLanguageID(), $key, $value)) {
                    $GLOBALS['LOGGER']->logError('Could not save Modules value (' . $value .') for Project Text (' . $key .')');
                }
            } else if(strpos($key,MODUL_ADMIN_PARAM_NUM) !== FALSE) {
                $key = str_replace(MODUL_ADMIN_PARAM_NUM, '', $key);
                if (!$ADMIN_SERVICE->setProjectNum($MENU->getID(), $MENU->getLanguageID(), $key, $value)) {
                    $GLOBALS['LOGGER']->logError('Could not save Modules value (' . $value .') for Project Num (' . $key .')');
                }
            }
        //if ( $ADMIN_SERVICE->setProjectNm($MENU->getID(),$MENU->getLanguageID(),MODUL_GALLERY_PROJECT_NUM,$data['category']) )
        }
    }

?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title><?php echo getTranslation('modul_admin_title'); ?></title>
    	<meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>">
	    <meta name="robots" content="noindex,nofollow">
    	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $LANGUAGE->getCharset(); ?>">
        <link rel="stylesheet" href="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/css/admin.css" type="text/css">
        <script type="text/javascript">
        <!--
            var enterPositiveNumeric = '<?php echo getTranslation('modul_js_pos_numeric'); ?>';
            var enterValidNumeric    = '<?php echo getTranslation('modul_js_valid_numeric'); ?>';
            var enterValidMenuID     = '<?php echo getTranslation('modul_js_valid_menuid'); ?>';
            var valueCanBeEmpty      = '<?php echo getTranslation('modul_js_optional_empty'); ?>';

            this.focus();

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
                /*
                $i=0;
                foreach($params AS $key => $type)
                {
                    switch($type)
                    {
                        case MODUL_PROJECT_TYPE_INTEGER:
                            echo "if(!isInt(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidNumeric);\n";
                            break;
                        case MODUL_PROJECT_TYPE_CATEGORY:
                            echo "if(!isIntOptional(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidNumeric);\n";
                            break;
                        case MODUL_PROJECT_TYPE_STRING:
                            echo "if(!isPositiveInt(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterPositiveNumeric);\n";
                            break;
                        case MODUL_PROJECT_TYPE_BOOLEAN:
                            echo "if(!isMenuID(document.getElementById('$key').value)) return wrong(document.getElementById('$key'), enterValidMenuID);\n";
                            break;
                        default:
                            break;
                    }
                    $i++;
                }
                */
                ?>
                return true;
            }

            function chooseMenuID(jsfunc, inputid)
            {
                <?php
                $link = new MenuChooserLink();
                $link->setItemID($MENU->getID());
                $link->setJavascriptCallback('"+jsfunc');
                ?>
                fenster = open("<?php echo LinkHelper::getUrlFromCMSLink($link); ?>,"SelectParent","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
                bBreite=screen.width;
                bHoehe=screen.height;
                fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
            }

            // -->
        </script>
    </head>
    <body style="margin:10px;">

        <h1><?php echo getTranslation('modul_admin_title'); ?></h1>

        <table border="0" cellspacing="2" cellpadding="0">
		<colgroup>
			<col width="80" />
			<col width="" />
		</colgroup>
        <tr>
			<td><b><?php echo getTranslation('modul_name'); ?>:</b></td>
            <td> <?php echo $MODUL->getName(); ?></td>
		</tr>
        <tr>
			<td><b><?php echo getTranslation('modul_page_name'); ?>:</b></td>
            <td> <a href="<?php echo createMenuLink($MENU->getID()); ?>" target="_blank"><?php echo $MENU->getName() . ' (ID: '.$MENU->getID().')'; ?></a></td>
		</tr>
        <tr>
			<td><b><?php echo getTranslation('modul_page_language'); ?>:</b></td>
            <td> <?php echo $LANGUAGE->getName(); ?></td>
		</tr>
        </table>


        <form onSubmit="return checkParameter()" name="modulDetailForm" id="modulDetailForm" action="<?php echo LinkHelper::getUrlFromCMSLink($mdl); ?>" method="POST">
        <input type="hidden" name="<?php echo MODUL_ADMIN_PARAM_MODE; ?>" value="<?php echo MODUL_ADMIN_MODE_SAVE; ?>">

        <?php
        $conf = $MODUL->getConfiguration();

        // load properties from ini file
        $properties = array();
        if(isset($conf['properties'])) {
        	$properties = explode(',', $conf['properties']);
        }

        if(count($properties) > 0)
        {
            ?>
            <table border="0" style="margin-top:10px;border:1px solid #000000;" width="100%" cellspacing="1" cellpadding="0">
            <tr>
                <th align="left"><?php echo getTranslation('modul_th_key'); ?></th>
                <th align="left"><?php echo getTranslation('modul_th_value'); ?></th>
            </tr>
            <?php
            foreach($properties AS $propName)
            {
            	if(isset($conf[$propName]) && is_array($conf[$propName]))
            	{
	            	$settings = $conf[$propName];	// all settings of this property
	            	$save_key = null; 				// type of property (text/numeric)
	                $value = null;					// value of this property
	                $key = $propName;				// name of this property

            		// is this properties a text or numeric one?
            		switch($settings[MODUL_SETTINGS_TYPE])
            		{
	            		case MODUL_PROJECT_TYPE_BOOLEAN:
            			case MODUL_PROJECT_TYPE_INTEGER:
	            		case MODUL_PROJECT_TYPE_CATEGORY:
			                $save_key = MODUL_ADMIN_PARAM_NUM;
			                break;
	            		case MODUL_PROJECT_TYPE_STRING:
	            		case MODUL_PROJECT_TYPE_TEXT:
						case MODUL_PROJECT_TYPE_SQL_LIST:
			            case MODUL_PROJECT_SMARTY_TPL:
			            default:
			                $save_key = MODUL_ADMIN_PARAM_TEXT;
			                break;
            		}

                    if (isset($settings[MODUL_SETTINGS_DEFAULT]))
                        $value = $settings[MODUL_SETTINGS_DEFAULT];

	                if($save_key == MODUL_ADMIN_PARAM_NUM) {
	                    if($projectService->existsProjectNum($MENU->getID(), $MENU->getLanguageID(), $key)) {
	                        $value = $projectService->getProjectNum($MENU->getID(), $MENU->getLanguageID(), $key);
	                    }
	                } else if($save_key == MODUL_ADMIN_PARAM_TEXT){
	                    if($projectService->existsProjectText($MENU->getID(), $MENU->getLanguageID(), $key)) {
	                        $value = $projectService->getProjectText($MENU->getID(), $MENU->getLanguageID(), $key);
	                    }
	                }

	                echo "<tr>\n";
	                echo "<td valign=\"top\"><b>" . $settings[MODUL_SETTINGS_NAME] . ":</b></td>\n";
	                echo "<td>\n";
	                switch($settings[MODUL_SETTINGS_TYPE])
	                {
	                    case MODUL_PROJECT_TYPE_INTEGER:
	                        echo '<input type="text" id="'.$save_key.$key.'" name="'.$save_key.$key.'" value="'.$value.'">';
	                        break;
	                    case MODUL_PROJECT_TYPE_CATEGORY:
	                        import('classes.util.formular.CategorySelect');
	                        import('classes.util.html.Option');
	                        $s = new CategorySelect();
	                        $s->setName($save_key.$key);
	                        $e = new Option();
	                        $e->setText( getTranslation('modul_choose_category') );
	                        if($value != null) {
	                            $s->setPreSelectedID($value);
	                        } else {
	                            $e->setIsSelected();
	                        }
	                        $s->addOption($e);
	                        $s->setStartID(_BIGACE_TOP_LEVEL);
	                        echo $s->getHtml();
	                        break;
	                    case MODUL_PROJECT_SMARTY_TPL:
	                        import('classes.util.formular.TemplateSelect');
					        $selector = new TemplateSelect();
					        $selector->setPreselected($value);
					        $selector->setShowIncludes(true);
					        $selector->setShowDeactivated(false);
					        $selector->setShowPreselectedIfDeactivated(true);
					        $selector->setShowSystemTemplates(true);
					        $selector->setName($save_key.$key);
					        echo $selector->getHtml();
	                        break;
	                    case MODUL_PROJECT_TYPE_STRING:
	                        echo '<input type="text" id="'.$save_key.$key.'" name="'.$save_key.$key.'" value="'.$value.'">';
	                        break;
	                    case MODUL_PROJECT_TYPE_TEXT:
	                        echo '<textarea rows="5" id="'.$save_key.$key.'" name="'.$save_key.$key.'">'.$value.'</textarea>'."\n";
	                        break;
	                    case MODUL_PROJECT_TYPE_BOOLEAN:
	                        echo '<select id="'.$save_key.$key.'" name="'.$save_key.$key.'">';
	                        echo '<option value="1"';
	                        if ($value == true)
	                            echo ' selected';
	                        echo '>'.getTranslation('modul_boolean_true').'</option>';
	                        echo '<option value="0"';
	                        if ($value == false)
	                            echo ' selected';
	                        echo '>'.getTranslation('modul_boolean_false').'</option>';
	                        echo "</select>\n";
	                        break;
						case MODUL_PROJECT_TYPE_SQL_LIST:
							if(!isset($settings[MODUL_SETTINGS_SQL])) {
								$GLOBALS['LOGGER']->logError('Missing "sql" attribute for modul key "'.$key.'"');
							}
							else {
								$sqlString = $settings[MODUL_SETTINGS_SQL];
								$sqlValues = array('ID' => $MENU->getID(), 'LANGUAGE' => $MENU->getLanguageID());
							    $sqlString = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sqlString, $sqlValues);
								$sqlResult = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($sqlString);
			                    echo '<select id="'.$save_key.$key.'" name="'.$save_key.$key.'">';
								if($settings['optional']) {
									echo '<option value=""></option>';
								}
			                    for($i=0; $i < $sqlResult->count(); $i++) {
									$sqlTemp = $sqlResult->next();
			                    	echo '<option value="'.$sqlTemp[0].'"';
			                    	if($sqlTemp[0] == $value)
			                    		echo ' selected';
			                    	echo '>'.$sqlTemp[1].'</option>';
								}
			                    echo "</select>\n";
							}
	                        break;
	                    default:
	                        echo '<input type="text" id="'.$save_key.$key.'" name="'.$save_key.$key.'" value="'.$value.'">';
	                        break;
                	}
                echo "</td>\n";
                echo "</tr>\n";
            	}
            	else {
					$GLOBALS['LOGGER']->logError('Configured Modules "'.$MODUL->getName().'" Property "'.$propName.'" is invalid, check Ini File!');
            	}
            } // foreach
            ?>
            <tr><td colspan="2" align="right"><button type="submit"><?php echo getTranslation('modul_save'); ?></button></td></tr>
            </table>
            <?php
        }
        else
        {
        	echo '<br/><br/><i><b>'.getTranslation('modul_no_properties').'</b></i>';
        }
        ?>
        </form>
    </body>
</html>
<?php
}
else
{
    loadClass('exception', 'ExceptionHandler');
    loadClass('exception', 'NoFunctionalRightException');
    ExceptionHandler::processCoreException( new NoFunctionalRightException('Protected Area. You are not allowed to enter!', createMenuLink(_BIGACE_TOP_LEVEL)) );
}
?>