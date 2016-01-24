<?php
//
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Mirror        http://bigace.sourceforge.net/                           |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTBILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+
//

/**
 * This File defines all common Constants.
 *
 * DO NOT modify, all of them are important for the CMS CORE.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.libs
 */

/**
 * Bigace Version ID
 */
define ('_BIGACE_ID' , '2.7.8');
/**
 * Bigace Build ID.
 */
define ('_BIGACE_BUILD_ID' , 'final');
/**
 * Anonymous User ID
 */
define ('_AID_' , '2');
/**
 * Super (Admin) User ID
 */
define ('_BIGACE_SUPER_ADMIN', 1);
/**
 * The ID of the TOP-LEVEL Items for all Itemtypes.
 */
define ('_BIGACE_TOP_LEVEL',  -1);
/**
 * Defines the Parent ID of the TOP-LEVEL Items. Can be used for (as example) checks when reading (recursive) Trees.
 */
define ('_BIGACE_TOP_PARENT', -9999);
/**
 * Value defining that the User has NO rights.
 */
define ('_BIGACE_RIGHTS_NO'    , 0);
/**
 * Value defining that the User has READ rights.
 */
define ('_BIGACE_RIGHTS_READ'  , 1);
/**
 * Value defining that the User has WRITE rights.
 */
define ('_BIGACE_RIGHTS_WRITE' , 2);
/**
 * Value defining that the User has READ + WRITE rights.
 */
define ('_BIGACE_RIGHTS_RW'    , 3);
/**
 * Value defining that the User has DELETE rights.
 */
define ('_BIGACE_RIGHTS_DELETE', 4);
/**
 * Value defining that the User has READ + WRITE + DELETE rights.
 */
define ('_BIGACE_RIGHTS_RWD'   , 7);
/**
 * Smarty Command (Itemtype 1)
 * */
define ('_BIGACE_CMD_SMARTY'   , 'smarty');
/**
 * Menu Command (Itemtype 1)
 * */
//define ('_BIGACE_CMD_MENU'   , 'menu'); // moved to init_session for smarty switch
/**
 * Image Command (Itemtype 4)
 */
define ('_BIGACE_CMD_IMAGE'  , 'image');
/**
 * File Command (Itemtype 5)
 */
define ('_BIGACE_CMD_FILE'   , 'file');
/**
 * Admin Command
 */
define ('_BIGACE_CMD_ADMIN'  , 'admin');
/**
 * Editor Command
 */
define ('_BIGACE_CMD_EDITOR' , 'editor');
/**
 * Itemtype Menu.
 */
define ('_BIGACE_ITEM_MENU'   , 1);
/**
 * Itemtype Image.
 */
define ('_BIGACE_ITEM_IMAGE'  , 4);
/**
 * Itemtype File.
 */
define ('_BIGACE_ITEM_FILE'   , 5);
/**
 * The Fright name that defines the Editor access.
 */
define ('_BIGACE_FRIGHT_USE_EDITOR',        'use_editor');
/**
 * The Fright name that defines the Administration of Menus.
 */
define ('_BIGACE_FRIGHT_ADMIN_MENUS',       'admin_menus');
/**
 * The Fright name that defines the Administration of Items.
 */
define ('_BIGACE_FRIGHT_ADMIN_ITEMS',       'admin_items');
/**
 * This Columns holds the Items Order Position information.
 */
define('ORDER_COLUMN_POSITION', 'num_4');
/**
 * Flag used to identify a full Item request. Example: "SELECT * FROM item_x"
 */
define('ITEM_LOAD_FULL',        'full');
/**
 * Flag used to identify a "light version of an Item. Example: "SELECT id,name,parent FROM item_x"
 */
define('ITEM_LOAD_LIGHT',       'light');
/**
 * Flag indicating that the Item should be hidden within "normal" navigation structures.
 * Unlike administration where all Items will be displayed.
 */
define('FLAG_HIDDEN',           2);
/**
 * Flag indicating that the Item is trashed.
 * Trashed Items are deleted but not physically removed (this function is currently not yet implemented).
 */
define('FLAG_TRASH',            1);
/**
 * Flag indicating that the Item status is normal. This is the default status.
 */
define('FLAG_NORMAL',           0);
/**
 * This Constants defines that all Log Messages will be shown.
 */
define('E_DEBUG', 4096);
/**
 * Setting this Constants, causes the Logger to dump even the executed SQL Commands.
 */
define('E_SQL', 8192);
/**
 * Defines the default Umask to be used when creating or copying Files.
 * Default: <code>0</code>
 */
define('_BIGACE_DEFAULT_UMASK_FILE', 0);
/**
 * Defines the default Umask when creating or copying a Directory.
 * Default: <code>0</code>
 */
define('_BIGACE_DEFAULT_UMASK_DIRECTORY', 0);
/**
 * Defines the default Permissions when creating a Directory.
 * Default: <code>0755</code>
 */
define('_BIGACE_DEFAULT_RIGHT_DIRECTORY', 0777);
/**
 * Defines the default Permissions when creating a File.
 * Default: <code>0755</code>
 */
define('_BIGACE_DEFAULT_RIGHT_FILE', 0777);
/**
 * Logger namespace for authentication.
 */
define('LOGGER_NAMESPACE_AUTHENTICATION', "auth");
/**
 * Logger namespace for audit messages.
 */
define('LOGGER_NAMESPACE_AUDIT', "audit");
/**
 * Logger namespace for core messages.
 */
define('LOGGER_NAMESPACE_SYSTEM', "system");
/**
 * Logger namespace for search messages.
 */
define('LOGGER_NAMESPACE_SEARCH', "search");
/**
 * Loglevel for info messages.
 */
define('LOGGER_LEVEL_INFO', E_USER_NOTICE);
/**
 * Loglevel for error messages.
 */
define('LOGGER_LEVEL_ERROR', E_USER_ERROR);
/**
 * Root Directory of the BIGACE installation .
 */
define ('_BIGACE_DIR_ROOT', realpath( dirname(__FILE__) . '/../../' ));
/**
 * Languages directory, where language definitions and translations are stored.
 */
define('_BIGACE_LANGUAGE_PATH', _BIGACE_DIR_ROOT . '/system/language/' );
/**
 * Admin directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_ADMIN', _BIGACE_DIR_ROOT . '/system/admin/');
/**
 * Addon directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_ADDON', _BIGACE_DIR_ROOT . '/addon/');
/**
 * Plugin directory of the BIGACE installation.
 */
define ('BIGACE_PLUGINS', _BIGACE_DIR_ROOT . '/plugins/');
/**
 * Addon directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_EDITOR', _BIGACE_DIR_ROOT . '/system/editor/');
/**
 * Addon directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_LIBS', _BIGACE_DIR_ROOT . '/system/libs/');
/**
 * Public directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_PUBLIC', _BIGACE_DIR_ROOT . '/public/');
/**
 * Consumer directory of the BIGACE installation.
 */
define ('_BIGACE_DIR_CONSUMER', _BIGACE_DIR_ROOT . '/consumer/');

?>