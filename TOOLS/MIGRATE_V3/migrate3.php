<?php

/**
 * Please make sure, that the script has complete permissions on all folder and files.
 *
 * - This script belongs into the Bigace root folder
 * - DO NOT EXECUTE MORE THAN ONCE
 * - Copy all error messages into an editor for later work
 *
 * 1. Copy this script and the bigace_3.0.zip file into your Bigace root folder
 * 2. Execute this script in your browser, write down all errors
 * 3. Execute this script again (2nd time), write down all errors
 * 3a. If no error occured and the script could be extracted, go on with 4.
 * 3b. If an error occured, extract the ZIP manually and copy everything into the Bigace root folder
 * 4. Execute the script a last (3rd) time.
 *
 * You should be able to use Bigace again.
 *
 * TODOS:
 * =======================================================================================
 *
 * News - ???
 * Comments - ??? z.b. config rss.latest.template
 *
 * $Id$
 */


define('MIGRATE_CID', 'cid{CID}');
define('MIGRATE_BACKUP', '__backup/');
define('MIGRATE_DIR_PERM', 0777);
define('MIGRATE_FILE_PERM', 0777);
define('MIGRATE_ROOT', dirname(__FILE__).'/');
define('MIGRATE_DEBUG', true);
define('MIGRATE_SELF', basename(__FILE__));
define('MIGRATE_VALIDTO', 1924902000);

function initMigration()
{
    // load bigace environment
    error_reporting(-1);
    set_error_handler('myErrorHandler');

    // this is nasty, but we need this hack nevertheless ;)
    $GLOBALS['LOGGER'] = new MigrateLogger();
}

/**
 * A simple error handler to show script bugs.
 */
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (stripos($errstr, 'chmod(): Operation not permitted') === false) {
        echo "<br><b>ERROR</b> [$errno] $errstr => ";
        echo "   in line $errline of file $errfile";
    }
}

class MigrateLogger {
    function logError($msg, $stacktrace = true) {
        trigger_error($msg);
    }
    function finalize() {}
}

/**
 * Migrates a bigace configuration file to the new format.
 */
function migrateConfig()
{
    $confFile = MIGRATE_ROOT.'application/bigace/configs/bigace.php';
    if (file_exists($confFile)) {
        return array();
    }

    $config = array(
        'database' =>
        array (
            'type'    => 'Mysqli',
            'host'    => 'localhost',
            'name'    => 'bigace',
            'user'    => 'root',
            'pass'    => '',
            'prefix'  => 'cms_',
            'charset' => 'utf8',
        ),
        'ssl'     => false,
        'rewrite' => false
    );

    if (defined('BIGACE_URL_REWRITE') && BIGACE_URL_REWRITE === 'true') {
        $htaccess = 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ index.php [NC,L]
';

        if ( (file_exists(MIGRATE_ROOT.'public/.htaccess') && is_writable(MIGRATE_ROOT.'public/.htaccess')) ||
             (!file_exists(MIGRATE_ROOT.'public/.htaccess') && is_writable(MIGRATE_ROOT.'public/')) ) {
                $bytes = file_put_contents(MIGRATE_ROOT.'public/.htaccess', $htaccess);

                if ($bytes > 0) {
                    $config['rewrite'] = true;
                }
        }

        if ($config['rewrite'] !== true) {
            $errors[] = 'Could not reactivate URL rewriting, probably file permissions are missing.';
        }
    }

    if (defined('BIGACE_USE_SSL') && BIGACE_USE_SSL === true) {
        $config['ssl'] = true;
    }

    $errors = array();
    if (!isset($GLOBALS['_BIGACE']['db'])) {
        $errors[] = 'Could not find global database configuration to migrate';
    } else {
        $config['database']['host'] = $GLOBALS['_BIGACE']['db']['host'];
        $config['database']['name'] = $GLOBALS['_BIGACE']['db']['name'];
        $config['database']['user'] = $GLOBALS['_BIGACE']['db']['user'];
        $config['database']['pass'] = $GLOBALS['_BIGACE']['db']['pass'];
        $config['database']['prefix'] = $GLOBALS['_BIGACE']['db']['prefix'];
    }

    $export   = var_export($config, true);
    $export = '<?php
return ' . $export . ';
';
    file_put_contents($confFile, $export);
    chmod($confFile, MIGRATE_FILE_PERM);

    return $errors;
}

function createFolder()
{
    $folder = array(
        MIGRATE_BACKUP,
        MIGRATE_BACKUP . 'public/',
        MIGRATE_BACKUP . 'public/.cache/',
        MIGRATE_BACKUP . 'consumer/',
        'application/',
        'application/bigace/',
        'application/bigace/configs/',
        'sites/',
        'storage/',
        'library/',
        'public/'
    );

    $errors = array();
    foreach($folder as $name)
    {
        $dir = MIGRATE_ROOT.$name;
        if (file_exists($dir)) {
            if(defined('MIGRATE_DEBUG'))
                $errors[] = 'Directory already existing: ' . $dir;
            continue;
        }

        if (!mkdir($dir, MIGRATE_DIR_PERM)) {
            $errors[] = 'Could not create directory: ' . $dir;
        }

        $chRes = baChmod($dir, MIGRATE_DIR_PERM);
        if ($chRes !== true)  {
            $errors[] = $chRes;
        }
    }
    return $errors;
}

/**
 * Renames folder to their equivalent brothers in v3.
 * Also backups folder that we do not need any longer.
 *
 * Probably the migration script will be executed more than once...
 * so if a file or folder doe not exist, we skip it silently.
 */
function moveFilesAndFolder()
{
    $folder = array(
        'system/config/consumer.ini'  => 'application/bigace/configs/consumer.ini',
        'consumer/'.MIGRATE_CID.'/'   => MIGRATE_BACKUP . 'consumer/'.MIGRATE_CID.'/',
        'consumer/'                   => 'sites/',
        'addon/'                      => MIGRATE_BACKUP . 'addon/',
        'plugins/'                    => MIGRATE_BACKUP . 'plugins/',
        'misc/'                       => MIGRATE_BACKUP . 'misc/',
        'public/'.MIGRATE_CID.'/'     => MIGRATE_BACKUP . 'public/'.MIGRATE_CID.'/',
        'public/system/'              => MIGRATE_BACKUP . 'public/system/',
        'public/markitup/'            => MIGRATE_BACKUP . 'public/markitup/',
        'system/'                     => MIGRATE_BACKUP . 'system/'
    );

    $errors = array();
    $dir = MIGRATE_ROOT;
    foreach($folder as $oldName => $newName)
    {
        $oldPath = $dir . $oldName;
        $newPath = $dir . $newName;

        if (!file_exists($oldPath)) {
            continue;
        }

        if (!rename($oldPath, $newPath)) {
            $errors[] = 'Could not rename: <b>' . $oldPath . '</b> => <b>' . $newPath . '</b>';
            continue;
        }

        $chRes = baChmod($newPath, MIGRATE_DIR_PERM);
        if ($chRes !== true)  {
            $errors[] = $chRes;
        }

        /*
        if (stripos($oldPath, MIGRATE_CID) !== false) {
            foreach($communities as $id) {
                $tmpOld = str_replace(MIGRATE_CID, 'cid' . $id, $oldPath);
                $tmpNew = str_replace(MIGRATE_CID, 'cid' . $id, $newPath);
                if (!rename($tmpOld, $tmpNew)) {
                    $errors[] = '&nbsp;Could not rename directory: <b>' . $oldName . '</b> => <b>' . $newName . '</b>';
                    continue;
                }
                if (!chmod($tmpNew, MIGRATE_DIR_PERM))  {
                    $errors[] = '&nbsp;- Could not change permissions on: ' . $tmpNew;
                }
            }
        }
        * */
    }
    return $errors;
}

// export all smarty templates nito the filesystem
function baExportSmarty()
{
    $errors = array();

    $sql = 'SELECT cid, filename, content FROM {DB_PREFIX}template';
    $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($stmt);

    $count = $res->count();

    if ($count > 0)
    {
        $dir = MIGRATE_ROOT . 'sites/'.MIGRATE_CID.'/views/smarty/';
        for ($i = 0; $i < $count; $i++)
        {
            $temp = $res->next();
            $path = str_replace(MIGRATE_CID, 'cid' . $temp['cid'], $dir);

            if (!file_exists($path))
            {
                if (!mkdir($path, MIGRATE_DIR_PERM)) {
                    $errors[] = 'Could not export templates, because smarty directory could not be created: ' . $path;
                    continue;
                }

                $chRes = baChmod($path, MIGRATE_DIR_PERM);
                if ($chRes !== true)  {
                    $errors[] = $chRes;
                }
            }

            $file = $path . $temp['filename'];

            $bytes = file_put_contents($file, $temp['content']);

            $chRes = baChmod($file, MIGRATE_FILE_PERM);
            if ($chRes !== true)  {
                $errors[] = $chRes;
            }
        }
    }

    return $errors;
}

function fixUniqueName()
{
    $errors = array();
    $itemtypes = array(1,4,5);

    $connection = $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection();

    foreach ($itemtypes as $type) {
        $sql = 'SELECT cid,id,language,name,unique_name FROM `{DB_PREFIX}item_'.$type.'` WHERE unique_name = NULL OR unique_name = ""';
        $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql);
        $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($stmt);

        $count = $res->count();

        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $uid  = uniqid();
                $temp = $res->next();
                $uurl = $temp['name'] . '_' . $uid . '.html';
                $uurl = preg_replace("/[^a-zA-Z0-9.,%\/_-\\s]/", '', $uurl);

                $sql2 = 'UPDATE `{DB_PREFIX}item_'.$type.'` SET unique_name = "'.$uurl.'" WHERE cid="' . $temp['cid'] . '" AND id="' . $temp['id'] . '" AND language="' . $temp['language'] . '"';

                $stmt2 = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql2);
                $res2 = $connection->sql($stmt2);
                if ($res2->isError()) {
                    $errors[] = 'SQL failed <b>'.$stmt2.'</b>';
                } else {
                    $error = $connection->getError();
                    if ($error !== null) {
                        $errors[] = 'SQL ERROR: ' . $error->getMessage() . ' in <b>'.$stmt2.'</b>';
                    } else {
                        $sql3 = 'INSERT INTO `{DB_PREFIX}unique_name` (cid,itemtype,itemid,language,name) VALUES ("'.$temp['cid'].'","'.$type.'","'.$temp['id'].'","'.$temp['language'].'","'.$uurl.'")';

                        $stmt3 = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql3);
                        $res3 = $connection->sql($stmt3);
                        if ($res3->isError()) {
                            $errors[] = 'SQL failed <b>'.$stmt3.'</b>';
                        }
                    }
                }
            }
        }
    }
    return $errors;
}


function baImportContent()
{
    $errors = array();

    $sql = 'SELECT cid, id, language, modifieddate, text_1 FROM {DB_PREFIX}item_1';
    $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($sql);
    $res = $GLOBALS['_BIGACE']['SQL_HELPER']->execute($stmt);

    $count = $res->count();

    if ($count > 0) {
        $path = MIGRATE_ROOT.'sites/';
        $con = $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection();
        $prefix = $GLOBALS['_BIGACE']['db']['prefix'];

        for ($i = 0; $i < $count; $i++) {
            $temp = $res->next();

            $dir  = $path . 'cid' . $temp['cid'] . '/items/html/';
            $file = $dir . $temp['text_1'];
            $cnt  = '';

            if (file_exists($file)) {
                $cnt = file_get_contents($file);
                $cnt = $con->escape($cnt);
            } else {
                $errors[] = 'Content file does not exist <b>'.$file.'</b>';
                continue;
            }

            $insert = 'INSERT INTO '.$prefix.'content (cid,id,language,name,cnt_type,state,position,valid_from,valid_to,content) VALUES (';
            $insert .= "'".$con->escape($temp['cid'])."',";
            $insert .= "'".$con->escape($temp['id'])."',";
            $insert .= "'".$con->escape($temp['language'])."',";
            $insert .= '"default",';
            $insert .= '"html",';
            $insert .= '"R",';
            $insert .= '"1",';
            $insert .= "'".$con->escape($temp['modifieddate'])."',";
            $insert .= '"'.MIGRATE_VALIDTO.'",';
            $insert .= "'".$cnt."'";
            $insert .= ');';

            $res2 = $con->insert($insert);
            if ($res2 === false) {
                $errors[] = 'SQL ERROR in <b>'.htmlentities($insert).'</b>';
            }
        }
    }

    return $errors;
}

// instead of only fetching all active communities, we take all in filesystem existing ones
function baGetAllCids()
{
    $dir = MIGRATE_ROOT . 'sites/';

    $all = glob($dir.'*', GLOB_ONLYDIR);
    $ids = array();

    foreach ($all as $name) {
        $name = str_replace($dir, '', $name);
        if ($name != 'cid{CID}' && $name != 'all' ) {
            $ids[] = substr($name, 3);
        }
    }
    return $ids;
}

// executed before export of smarty templates
function changeCommunities()
{
    $errors = array();

    $communities = baGetAllCids();
    $dir = MIGRATE_ROOT;

    foreach ($communities as $id) {
        $moves = array(
            'sites/'.MIGRATE_CID.'/modul/'       => 'sites/'.MIGRATE_CID.'/modules/',
            'sites/'.MIGRATE_CID.'/language/'    => 'sites/'.MIGRATE_CID.'/i18n/',
            'sites/'.MIGRATE_CID.'/items/file/'  => 'sites/'.MIGRATE_CID.'/files/',
            'sites/'.MIGRATE_CID.'/items/image/' => 'sites/'.MIGRATE_CID.'/images/',
        );

        foreach ($moves as $old => $new) {
            $oldPath = $dir . str_replace(MIGRATE_CID, 'cid'.$id, $old);
            $newPath = $dir . str_replace(MIGRATE_CID, 'cid'.$id, $new);

            if (!file_exists($oldPath)) {
                $errors[] = 'Not found for moving: <b>' . $oldPath . '</b>';
                continue;
            }


            if (!rename($oldPath, $newPath)) {
                $errors[] = 'Could not rename: <b>' . $oldPath . '</b> => <b>' . $newPath . '</b>';
                continue;
            }

            $chRes = baChmod($newPath, MIGRATE_DIR_PERM);
            if ($chRes !== true) {
                $errors[] = $chRes;
            }
        }

        $modulesPath = $dir . str_replace(MIGRATE_CID, 'cid'.$id, 'sites/'.MIGRATE_CID.'/modules/');
        $renameModules = glob($modulesPath . '*', GLOB_ONLYDIR);
        foreach ($renameModules as $module) {
            $old = $module . '/modul.php';
            $new = $module . '/modul.phtml';

            if (!file_exists($old)) {
                $errors[] = 'Module not found for renaming: <b>' . $old . '</b>';
                continue;
            }


            if (!rename($old, $new)) {
                $errors[] = 'Could not rename: <b>' . $old . '</b> => <b>' . $new . '</b>';
                continue;
            }

            $chRes = baChmod($new, MIGRATE_FILE_PERM);
            if ($chRes !== true) {
                $errors[] = $chRes;
            }
        }

        $deletes = array(
            'public/'.MIGRATE_CID.'/editor/',
            'public/'.MIGRATE_CID.'/spring_flavour/',
            'sites/'.MIGRATE_CID.'/items/html/',
            'sites/'.MIGRATE_CID.'/items/',
            'sites/'.MIGRATE_CID.'/php/',
            'sites/'.MIGRATE_CID.'/import/',
            'sites/'.MIGRATE_CID.'/smarty/',
            'sites/'.MIGRATE_CID.'/include/',
            'sites/'.MIGRATE_CID.'/presentation/',
            'sites/'.MIGRATE_CID.'/uninstall/',
            'sites/'.MIGRATE_CID.'/modules/displayContent/',
            'sites/'.MIGRATE_CID.'/config/config.system.php',
            'sites/'.MIGRATE_CID.'/config/user_attributes.ini',
        );

        foreach ($deletes as $name) {
            $path = $dir . str_replace(MIGRATE_CID, 'cid'.$id, $name);

            $errors = array_merge($errors, baDeleteRecurse($path));
        }

        $creates = array(
            'sites/'.MIGRATE_CID.'/updates/',
            'sites/'.MIGRATE_CID.'/search/',
            'sites/'.MIGRATE_CID.'/credits/',
            'sites/'.MIGRATE_CID.'/plugins/',
            'sites/'.MIGRATE_CID.'/views/',
            'sites/'.MIGRATE_CID.'/views/smarty/',
            'sites/'.MIGRATE_CID.'/views/scripts/',
            'sites/'.MIGRATE_CID.'/views/layouts/',
        );

        foreach ($creates as $name) {
            $path = $dir . str_replace(MIGRATE_CID, 'cid'.$id, $name);

            if (file_exists($path)) {
                if(defined('MIGRATE_DEBUG'))
                    $errors[] = 'Directory already existing: ' . $path;
                continue;
            }

            if (!mkdir($path, MIGRATE_DIR_PERM)) {
                $errors[] = 'Could not create directory: ' . $path;
                continue;
            }

            $chRes = baChmod($path, MIGRATE_DIR_PERM);
            if ($chRes !== true) {
                $errors[] = $chRes;
            }
        }

        $sqls = array(
            'INSERT INTO `{DB_PREFIX}frights` (`cid` ,`name` ,`defaultvalue` ,`description`) VALUES ("{CID}", "media.permission", "N", "Allows to edit default permissions of images and files.")',
            'INSERT INTO `{DB_PREFIX}frights` (`cid` ,`name` ,`defaultvalue` ,`description`) VALUES ("{CID}", "media.image", "N", "Allows access to image administration (create, edit and delete are based on item permissions).")',
            'INSERT INTO `{DB_PREFIX}frights` (`cid` ,`name` ,`defaultvalue` ,`description`) VALUES ("{CID}", "media.import", "N", "Allows to upload and import new files.")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "20", "media.permission")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "40", "media.permission")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "20", "media.image")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "40", "media.image")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "20", "media.import")',
            'INSERT INTO `{DB_PREFIX}group_frights` (`cid` ,`group_id` ,`fright`) VALUES ("{CID}", "40", "media.import")',

            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-LOGIN"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-REGISTER"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-PASSWORD"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-FOOTER"',

            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-ACTIVATE"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "MAINTENANCE"',

            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "AUTH-HEADER"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "APPLICATION-SEARCH"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "APPLICATIONS-HEADER"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "APPLICATIONS-FOOTER"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and system = 1',

            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "News-Entry"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "News-Listing"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "News-Header"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "News-Footer"',

            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "Comments-Listing-Form"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "Comments-RSS-Latest"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "News-RSS-Latest"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "BLIX (Footer Applications)"',
            'DELETE FROM `{DB_PREFIX}template` WHERE cid={CID} and name = "BLIX (Header Applications)"',

            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "News"',
            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "Comments"',
            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "Sitemap 2"',
            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "APPLICATION-SEARCH"',
            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "Fotogallery"',
            'DELETE FROM `{DB_PREFIX}stylesheet` WHERE cid={CID} and name = "Guestbook"',


        );

        $connection = $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection();

        $values = array('CID' => $id);

        foreach ($sqls as $stmt) {
            $stmt .= ';';
            $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($stmt, $values);
            $res = $connection->sql($stmt);
            if ($res->isError()) {
                $errors[] = 'SQL failed: <b>'.$stmt.'</b>';
            } else {
                $error = $connection->getError();
                if ($error !== null) {
                    $errors[] = 'SQL failed: [' . $error->getMessage() . '] <b>'.$stmt.'</b>';
                }
            }
        }
    }

    return $errors;
}

function baDeleteRecurse($path)
{
    $errors = array();

    if (!file_exists($path)) {
        if(defined('MIGRATE_DEBUG'))
            $errors[] = 'Not found to delete: <b>' . $path . '</b>';
    } else {

        if (is_dir($path)) {
            $files = glob($path . '*');
            if ($files === false) {
                $errors[] = 'Could not read from directory: <b>' . $path . '</b>';
            }
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $errors = array_merge($errors, baDeleteRecurse($file.'/'));
                } else {
                    if (!unlink($file)) {
                        $errors[] = 'Could not delete: <b>' . $file . '</b>';
                    }
                }
            }

            if (!rmdir($path)) {
                $errors[] = 'Could not delete directory: <b>' . $path . '</b>';
            }
        } else {
            if (!unlink($path)) {
                $errors[] = 'Could not delete file: <b>' . $path . '</b>';
            }
        }
    }

    return $errors;
}

function upgradeCommunities()
{
    $errors = array();
    $ids = baGetAllCids();

    foreach ($ids as $id) {
        $upgrades = array(
            MIGRATE_ROOT.'sites/'.MIGRATE_CID.'/',
            MIGRATE_ROOT.'public/'.MIGRATE_CID.'/',
        );

        foreach ($upgrades as $path) {
            $from = $path;
            $to   = str_replace(MIGRATE_CID, 'cid' . $id, $path);
            if (!file_exists($to)) {
                $errors[] = 'Could not upgrade Community folder: ' . $to;
                continue;
            }
            $errors = array_merge($errors, baCopyRecurse($from, $to));
        }
    }
    return $errors;
}

function baChmod($newFile, $perms = null)
{
    if ($perms === null) {
        $perms = MIGRATE_DIR_PERM;
        if (is_file($newFile)) {
            $perms = MIGRATE_FILE_PERM;
        }
    }

    clearstatcache();

    $curPerms = fileperms($newFile);

    if (is_file($newFile))
        $curPerms = intval(substr(decoct($curPerms), 3));
    else
        $curPerms = intval(substr(decoct($curPerms), 2));

    if ($curPerms !== false && $curPerms < decoct($perms)) {
        if (!chmod($newFile, $perms)) {
            if ($curPerms === false)
                $curPerms = 'unknown';
            else
                $curPerms = substr(decoct($curPerms), 2);

            return '&nbsp;- Could not change permissions on: ' . $newFile . ' ('.$perms.')';
        }
    }

    return true;
}

function baCopyRecurse($from, $to)
{
    $errors = array();

    $entries = glob($from.'*');
    if ($entries === false) {
        $errors[] = 'Could not read from directory: <b>' . $from . '</b>';
    }

    foreach ($entries as $file) {
        $filename = basename($file);
        $newFile = $to.$filename;
        if (is_dir($file)) {
            if (!file_exists($newFile)) {
                if (!mkdir($newFile.'/', MIGRATE_DIR_PERM)) {
                    $errors[] = 'Could not create directory: <b>' . $newFile . '</b>';
                }
            }

            $chRes = baChmod($newFile.'/', MIGRATE_DIR_PERM);
            if ($chRes !== true) {
                $errors[] = $chRes;
            }

            $errors = array_merge($errors, baCopyRecurse($file.'/', $newFile.'/'));
        } else {
            /*
            if (file_exists($newFile)) {
                $errors[] = "Already existing, skip copy from ".$file." to ".$newFile;
                continue;
            }
            * */

            if (!copy($file, $newFile)) {
                $errors[] = 'Could not copy file <b>' . $file . '</b> to <b>' . $newFile . '</b>';
            }


            $chRes = baChmod($newFile, MIGRATE_FILE_PERM);
            if ($chRes !== true) {
                $errors[] = $chRes;
            }
        }
    }
    return $errors;
}

/**
 * Deletes all files that we do not need any longer.
 */
function deleteFiles()
{
    $toDelete = array(
        'index.php',
        'login.php',
        'admin.php',
        '.htaccess',
        'robots.txt',
        //'public/.htaccess',
        'public/robots.txt',
        'public/index.php'
    );

    $errors = array();

    $dir = MIGRATE_ROOT;
    foreach ($toDelete as $name) {
        $rm = $dir . $name;

        if (!file_exists($rm)) {
            if(defined('MIGRATE_DEBUG'))
                $errors[] = 'Not found to delete: <b>' . $rm . '</b>';
            continue;
        }

        if (!unlink($rm)) {
            $errors[] = 'Could not delete: <b>' . $rm . '</b>';
        }
    }

    return $errors;
}

function fixPermissions()
{
    $errors = array();

    $set = new Bigace_Installation_FileSet();

    $dirs = $set->getDirectories();
    foreach ($dirs as $dir) {
        $chRes = baChmod($dir.'/', MIGRATE_DIR_PERM);
        if ($chRes !== true) {
            $errors[] = $chRes;
        }
    }

    $communities = baGetAllCids();
    foreach($communities as $id)
    {
        $dirs = $set->getCommunityDirectories($id);
        foreach($dirs as $dir)
        {
            if (!file_exists($dir)) {
                mkdir($dir, MIGRATE_DIR_PERM);
            }

            $chRes = baChmod($dir, MIGRATE_DIR_PERM);
            if ($chRes !== true)  {
                $errors[] = $chRes;
            }
        }
    }
    return $errors;
}


function baCleanupDatabase()
{
    $sqls = array(
        'DROP TABLE `{DB_PREFIX}events`',
        'DROP TABLE `{DB_PREFIX}autojobs`',
        'DROP TABLE `{DB_PREFIX}future_user_right`',
        'DROP TABLE `{DB_PREFIX}generic_entries`',
        'DROP TABLE `{DB_PREFIX}generic_mappings`',
        'DROP TABLE `{DB_PREFIX}generic_sections`',
        'DROP TABLE `{DB_PREFIX}item_future`',
        'DROP TABLE `{DB_PREFIX}item_history`',
        'DROP TABLE `{DB_PREFIX}statistics_history`',

        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "search" AND `name` = "minimum.word.length"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "search"  AND `name` = "template"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "workflow"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "admin"  AND `name` = "default.style"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "admin"  AND `name` = "dimension.large.mode"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "admin"  AND `name` = "display.latest.news"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "authentication"  AND `name` = "captcha.minimum.length"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "authentication"  AND `name` = "captcha.maximum.length"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "authentication"  AND `name` = "anonymous.group.registration"',
        'delete from {DB_PREFIX}configuration where    package  = "templates" and name = "auth.register"',
        'delete from {DB_PREFIX}configuration where    package  = "templates" and name = "auth.login"',
        'delete from {DB_PREFIX}configuration where    package  = "templates" and name = "auth.activate"',
        'delete from {DB_PREFIX}configuration where    package  = "templates" and name = "auth.password"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "templates"  AND `name` = "application.header"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "templates"  AND `name` = "application.footer"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "templates"  AND `name` = "maintenance"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "news" AND name = "rss.latest.template"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system" AND name = "write.statistic"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system"  AND `name` = "use.smarty"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system"  AND `name` = "captcha"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system"  AND `name` = "footer.type.extended"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "seo"  AND `name` = "image.use.extension"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "seo"  AND `name` = "file.use.extension"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "admin.menu"  AND `name` = "focus.on.select"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "comments"  AND `name` = "email.from"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "comments"  AND `name` = "email.send.posting"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "comments"  AND `name` = "email.send.moderator"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system"  AND `name` = "logger.loglevel"',
        'DELETE FROM `{DB_PREFIX}configuration` WHERE `package` = "system"  AND `name` = "show.smarty.hint"',

        'DELETE FROM `{DB_PREFIX}design` WHERE `name` = "BIGACE-REDIRECT"',
        'DELETE FROM `{DB_PREFIX}design` WHERE `name` = "News-Home"',
        'DELETE FROM `{DB_PREFIX}design` WHERE `name` = "News-Entry"',
        'DELETE FROM `{DB_PREFIX}design` WHERE `name` = "BLIX"',
        // will be installed when smarty is installed
        //'update {DB_PREFIX}design set name="default" where name = "BLIX"',

        'delete from {DB_PREFIX}group_frights where fright = "comments.edit"',
        'delete from {DB_PREFIX}frights where name = "comments.edit"',

        'delete from {DB_PREFIX}group_frights where fright = "autojobs.admin"',
        'delete from {DB_PREFIX}frights where name = "autojobs.admin"',

        'delete from {DB_PREFIX}group_frights where fright = "comments.delete"',
        'delete from {DB_PREFIX}frights where name = "comments.delete"',

        'delete from {DB_PREFIX}group_frights where fright = "news.create"',
        'delete from {DB_PREFIX}frights where name = "news.create"',

        'delete from {DB_PREFIX}group_frights where fright = "news.delete"',
        'delete from {DB_PREFIX}frights where name = "news.delete"',

        'delete from {DB_PREFIX}group_frights where fright = "news.categories"',
        'delete from {DB_PREFIX}frights where name = "news.categories"',

        'delete from {DB_PREFIX}group_frights where fright = "news.remote.login"',
        'delete from {DB_PREFIX}frights where name = "news.remote.login"',

        'delete from {DB_PREFIX}group_frights where fright = "edit_menus"',
        'delete from {DB_PREFIX}frights where name = "edit_menus"',

        'delete from {DB_PREFIX}group_frights where fright = "fotoalbum_admin"',
        'delete from {DB_PREFIX}frights where name = "fotoalbum_admin"',

        'delete from {DB_PREFIX}group_frights where fright = "community.deinstallation"',
        'delete from {DB_PREFIX}frights where name = "community.deinstallation"',

        'delete from {DB_PREFIX}group_frights where fright = "community.installation"',
        'delete from {DB_PREFIX}frights where name = "community.installation"',

        'delete from {DB_PREFIX}group_frights where fright = "edit_items"',
        'delete from {DB_PREFIX}frights where name = "edit_items"',

        'delete from {DB_PREFIX}group_frights where fright = "view_statistics"',
        'delete from {DB_PREFIX}frights where name = "view_statistics"',

        'delete from {DB_PREFIX}group_frights where fright = "group_frights"',
        'delete from {DB_PREFIX}frights where name = "group_frights"',

        'delete from {DB_PREFIX}group_frights where fright = "edit_frights"',
        'delete from {DB_PREFIX}frights where name = "edit_frights"',

        'delete from {DB_PREFIX}group_frights where fright = "view_statistics"',
        'delete from {DB_PREFIX}frights where name = "view_statistics"',

        'delete from {DB_PREFIX}group_frights where fright = "system_admin"',
        'delete from {DB_PREFIX}frights where name = "system_admin"',

        'delete from {DB_PREFIX}group_frights where fright = "smarty.designs.edit"',
        'delete from {DB_PREFIX}frights where name = "smarty.designs.edit"',

        'delete from {DB_PREFIX}group_frights where fright = "smarty.templates.edit"',
        'delete from {DB_PREFIX}frights where name = "smarty.templates.edit"',

        'delete from {DB_PREFIX}group_frights where fright = "smarty.stylesheets.edit"',
        'delete from {DB_PREFIX}frights where name = "smarty.stylesheets.edit"',

        'TRUNCATE TABLE `{DB_PREFIX}plugins`',
    );

    $connection = $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection();
    $errors = array();

    foreach ($sqls as $stmt)
    {
        $stmt .= ';';
        $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($stmt);
        $res = $connection->sql($stmt);
        if ($res->isError()) {
            $errors[] = 'SQL Cleanup failed with <b>'.$stmt.'</b>';
        } else  {
            $error = $connection->getError();
            if ($error !== null) {
                $errors[] = 'SQL CLEANUP ERROR: ' . $error->getMessage() . ' in <b>'.$stmt.'</b>';
            }
        }
    }
    return $errors;
}

function migrateDatabase()
{
    $sqls = array(
        'ALTER TABLE `{DB_PREFIX}item_1` ADD `type` VARCHAR( 50 ) NULL AFTER `unique_name` ',
        'ALTER TABLE `{DB_PREFIX}item_4` ADD `type` VARCHAR( 50 ) NULL AFTER `unique_name` ',
        'ALTER TABLE `{DB_PREFIX}item_5` ADD `type` VARCHAR( 50 ) NULL AFTER `unique_name` ',

        'ALTER TABLE `{DB_PREFIX}item_1` ADD `valid_from` INT( 11 ) NOT NULL default "0" AFTER `type`',
        'ALTER TABLE `{DB_PREFIX}item_1` ADD `valid_to` INT( 11 ) NOT NULL default "0" AFTER `valid_from`',

        'ALTER TABLE `{DB_PREFIX}item_4` ADD `valid_from` INT( 11 ) NOT NULL default "0" AFTER `type`',
        'ALTER TABLE `{DB_PREFIX}item_4` ADD `valid_to` INT( 11 ) NOT NULL default "0" AFTER `valid_from`',

        'ALTER TABLE `{DB_PREFIX}item_5` ADD `valid_from` INT( 11 ) NOT NULL default "0" AFTER `type`',
        'ALTER TABLE `{DB_PREFIX}item_5` ADD `valid_to` INT( 11 ) NOT NULL default "0" AFTER `valid_from`',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP COLUMN `workflow`',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP COLUMN `workflow`',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP COLUMN `workflow`',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP COLUMN `viewed`',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP COLUMN `viewed`',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP COLUMN `viewed`',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP INDEX cms_search',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP INDEX cms_search',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP INDEX cms_search',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_1` `num_1` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_2` `num_2` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_3` `num_3` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_4` `num_4` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `num_5` `num_5` INT( 11 ) NULL DEFAULT NULL',

        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `num_1` `num_1` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `num_2` `num_2` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `num_3` `num_3` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `num_4` `num_4` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `num_5` `num_5` INT( 11 ) NULL DEFAULT NULL',

        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `num_1` `num_1` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `num_2` `num_2` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `num_3` `num_3` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `num_4` `num_4` INT( 11 ) NULL DEFAULT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `num_5` `num_5` INT( 11 ) NULL DEFAULT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP INDEX `cms_cid`',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP INDEX `cms_cid`',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP INDEX `cms_cid`',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP INDEX `cms_language`',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP INDEX `cms_language`',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP INDEX `cms_language`',

        'ALTER TABLE `{DB_PREFIX}item_1` DROP INDEX `cms_parentid`',
        'ALTER TABLE `{DB_PREFIX}item_4` DROP INDEX `cms_parentid`',
        'ALTER TABLE `{DB_PREFIX}item_5` DROP INDEX `cms_parentid`',

        'ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX `item_1_cid` (`cid`)',
        'ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX `item_4_cid` (`cid`)',
        'ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX `item_5_cid` (`cid`)',

        'ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX `item_1_all_parent` (`cid`, `parentid`)',
        'ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX `item_4_all_parent` (`cid`, `parentid`)',
        'ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX `item_5_all_parent` (`cid`, `parentid`)',

        'ALTER TABLE `{DB_PREFIX}item_1` ADD INDEX `item_1_all_parent_language` (`cid`,`language`,`parentid`)',
        'ALTER TABLE `{DB_PREFIX}item_4` ADD INDEX `item_4_all_parent_language` (`cid`,`language`,`parentid`)',
        'ALTER TABLE `{DB_PREFIX}item_5` ADD INDEX `item_5_all_parent_language` (`cid`,`language`,`parentid`)',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `cid` `cid` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `id` `id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `id` `id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `id` `id` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `mimetype` `mimetype` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `mimetype` `mimetype` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `mimetype` `mimetype` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `createdate` `createdate` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `createdate` `createdate` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `createdate` `createdate` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `createby` `createby` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `createby` `createby` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `createby` `createby` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `modifieddate` `modifieddate` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `modifieddate` `modifieddate` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `modifieddate` `modifieddate` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `modifiedby` `modifiedby` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `modifiedby` `modifiedby` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `modifiedby` `modifiedby` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_1` CHANGE `unique_name` `unique_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL default ""',
        'ALTER TABLE `{DB_PREFIX}item_4` CHANGE `unique_name` `unique_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL default ""',
        'ALTER TABLE `{DB_PREFIX}item_5` CHANGE `unique_name` `unique_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL default ""',

        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "<Portlets", "<Widgets")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "</Portlets", "</Widgets")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "LastEditedItemsPortlet", "LastEditedMenus")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "LoginMaskPortlet", "LoginForm")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "NavigationPortlet", "Navigation")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "QuickSearchPortlet", "QuickSearch")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "SkypePortlet", "Skype")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "TwitterPortlet", "Twitter")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_value = REPLACE(project_value, "ToolPortlet", "Tools")  where project_key like "portlet.config.column.%"',
        'update {DB_PREFIX}item_project_text set project_key = REPLACE(project_key, "portlet.config.column.", "widget.column.")  where project_key like "portlet.config.column.%"',

        'UPDATE {DB_PREFIX}stylesheet SET `filename` = "style.css" WHERE `name` = "blix_stylesheet_main"' ,
        'UPDATE `{DB_PREFIX}stylesheet` SET `filename` = "bigace_extension.css" WHERE`name` = "blix_extension"',
        'UPDATE `{DB_PREFIX}stylesheet` SET `filename` = "layout.css" WHERE `name` = "blix_layout"',
        'UPDATE `{DB_PREFIX}stylesheet` SET `filename` = "spring_flavour.css" WHERE `name` = "blix_spring_flavour"',
        'UPDATE `{DB_PREFIX}stylesheet` SET `filename` = "editor.css" WHERE `name` = "blix_editor"',

        'update `{DB_PREFIX}item_1` set type = "redirect" and text_4 = "" where text_4 = "BIGACE-REDIRECT"',
        'update `{DB_PREFIX}item_1` set num_3 = 0 where num_3 = 1',
        'UPDATE `{DB_PREFIX}item_1` SET `num_5` = null',
        'update `{DB_PREFIX}item_1` set text_4="" where text_4="BLIX"',
        'update `{DB_PREFIX}item_1` set text_3="" where text_3="displayContent"',
        'UPDATE `{DB_PREFIX}item_1` SET `valid_from` = `createdate`',
        'UPDATE `{DB_PREFIX}item_1` SET `valid_to` = ' . MIGRATE_VALIDTO,
        'UPDATE `{DB_PREFIX}item_4` SET `valid_from` = `createdate`',
        'UPDATE `{DB_PREFIX}item_4` SET `valid_to` = ' . MIGRATE_VALIDTO,
        'UPDATE `{DB_PREFIX}item_5` SET `valid_from` = `createdate`',
        'UPDATE `{DB_PREFIX}item_5` SET `valid_to` = ' . MIGRATE_VALIDTO,
        'update `{DB_PREFIX}item_1` set text_3="submenu-preview" where text_3="submenuPreview"',

        'UPDATE {DB_PREFIX}item_1 set text_4 = "" where id = "-1"',

        'update `{DB_PREFIX}configuration` set type = "integer" where type = "long"',
        'update {DB_PREFIX}configuration set type = "layout" where type = "design"',
        'update {DB_PREFIX}configuration set type = "layout" where type = "template"',
        'update {DB_PREFIX}configuration set type = "layout" where type = "tpl_inc"',
        'update {DB_PREFIX}configuration set package = "system" where name = "show.default.content"',
        'update {DB_PREFIX}configuration set package = "admin", name = "menutree.open.on.select" where name = "open.on.select" and package = "admin.menu"',
        'update {DB_PREFIX}configuration set name = "send.no.cache.header" where name = "send.pragma.no.cache" and package = "system"',
        'update {DB_PREFIX}configuration set type = "integer", value="3600" where name = "check.csrf" and package = "admin"',
        'update {DB_PREFIX}configuration set value = "/" where name = "menu.default.extension" and package = "seo"',
        'UPDATE `{DB_PREFIX}configuration` SET name = "layout" WHERE package = "news" AND name = "template.news"',
        'update {DB_PREFIX}configuration set name = "akismet.api.key" where name = "akismet.wordpress.api.key" and package = "comments"',

        'UPDATE `{DB_PREFIX}item_1` SET text_3 = "contact-mail" where text_3 = "contactMail"',
        'UPDATE `{DB_PREFIX}item_1` SET text_3 = "sitemap" where text_3 = "sitemap2"',
        'UPDATE `{DB_PREFIX}item_project_text` SET project_key = "sitemap_startID" WHERE project_key = "sitemap2_startID"',
        'UPDATE `{DB_PREFIX}item_project_text` SET project_key = "sitemap_menuDepth" WHERE project_key = "sitemap2_menuDepth"',
        'UPDATE `{DB_PREFIX}item_project_text` SET project_key = "sitemap_useCss" WHERE project_key = "sitemap2_useCss"',

        'update `{DB_PREFIX}group_frights` set fright = "fotogallery" where fright = "fotogallery_admin"',
        'update `{DB_PREFIX}frights` set name = "fotogallery" where name = "fotogallery_admin"',

        'update `{DB_PREFIX}group_frights` set fright = "comments" where fright = "comments.activate"',
        'update `{DB_PREFIX}frights` set name = "comments" where name = "comments.activate"',

        'update `{DB_PREFIX}group_frights` set fright = "guestbook" where fright = "guestbook_admin"',
        'update `{DB_PREFIX}frights` set name = "guestbook" where name = "guestbook_admin"',

        'update `{DB_PREFIX}group_frights` set fright = "sitemap" where fright = "sitemap2_admin"',
        'update `{DB_PREFIX}frights` set name = "sitemap" where name = "sitemap2_admin"',

        'update `{DB_PREFIX}group_frights` set fright = "contact-mail" where fright = "webmail_admin"',
        'update `{DB_PREFIX}frights` set name = "contact-mail" where name = "webmail_admin"',

        'update `{DB_PREFIX}group_frights` set fright = "widget" where fright = "edit.portlet.settings"',
        'update `{DB_PREFIX}frights` set name = "widget" where name = "edit.portlet.settings"',

        'update `{DB_PREFIX}group_frights` set fright = "pages" where fright = "admin_menus"',
        'update `{DB_PREFIX}frights` set name = "pages" where name = "admin_menus"',

        'update `{DB_PREFIX}group_frights` set fright = "category" where fright = "admin_categorys"',
        'update `{DB_PREFIX}frights` set name = "category" where name = "admin_categorys"',

        'update `{DB_PREFIX}group_frights` set fright = "usergroup" where fright = "edit.usergroup"',
        'update `{DB_PREFIX}frights` set name = "usergroup" where name = "edit.usergroup"',

        'update `{DB_PREFIX}group_frights` set fright = "maintenance" where fright = "community.maintenance"',
        'update `{DB_PREFIX}frights` set name = "maintenance" where name = "community.maintenance"',

        'update `{DB_PREFIX}group_frights` set fright = "backup" where fright = "export_database"',
        'update `{DB_PREFIX}frights` set name = "backup" where name = "export_database"',

        'update `{DB_PREFIX}group_frights` set fright = "configuration" where fright = "admin_configurations"',
        'update `{DB_PREFIX}frights` set name = "configuration" where name = "admin_configurations"',

        'update `{DB_PREFIX}group_frights` set fright = "news" where fright = "news.create"',
        'update `{DB_PREFIX}frights` set name = "news" where name = "news.create"',

        'update `{DB_PREFIX}group_frights` set fright = "module" where fright = "module_all_rights"',
        'update `{DB_PREFIX}frights` set name = "module" where name = "module_all_rights"',

        'update `{DB_PREFIX}group_frights` set fright = "logging" where fright = "logging.messages"',
        'update `{DB_PREFIX}frights` set name = "logging" where name = "logging.messages"',

        'update `{DB_PREFIX}group_frights` set fright = "language" where fright = "languages_all_rights"',
        'update `{DB_PREFIX}frights` set name = "language" where name = "languages_all_rights"',

        'update `{DB_PREFIX}group_frights` set fright = "community" where fright = "community.admin"',
        'update `{DB_PREFIX}frights` set name = "community" where name = "community.admin"',

        'update `{DB_PREFIX}group_frights` set fright = "extension" where fright = "updates_manager"',
        'update `{DB_PREFIX}frights` set name = "extension" where name = "updates_manager"',

        'update `{DB_PREFIX}group_frights` set fright = "permission" where fright = "usergroup.permissions"',
        'update `{DB_PREFIX}frights` set name = "permission" where name = "usergroup.permissions"',

        'update `{DB_PREFIX}group_frights` set fright = "widget" where fright = "edit.portlet.settings"',
        'update `{DB_PREFIX}frights` set name = "widget" where name = "edit.portlet.settings"',

        'update `{DB_PREFIX}group_frights` set fright = "layout" where fright = "smarty.designs.edit"',
        'update `{DB_PREFIX}frights` set name = "layout" where name = "smarty.designs.edit"',

        'update `{DB_PREFIX}group_frights` set fright = "user" where fright = "admin_users"',
        'update `{DB_PREFIX}frights` set name = "user" where name = "admin_users"',

        'update `{DB_PREFIX}group_frights` set fright = "editor" where fright = "use_editor"',
        'update `{DB_PREFIX}frights` set name = "editor" where name = "use_editor"',

        'update `{DB_PREFIX}group_frights` set fright = "submenu.preview" where fright = "submenu_preview_admin"',
        'update `{DB_PREFIX}frights` set name = "submenu.preview" where name = "submenu_preview_admin"',

        'update `{DB_PREFIX}group_frights` set fright = "editor.sourcecode" where fright = "editor.html.sourcecode"',
        'update `{DB_PREFIX}frights` set name = "editor.sourcecode" where name = "editor.html.sourcecode"',

        'update `{DB_PREFIX}group_frights` set fright = "media.file" where fright = "admin_items"',
        'update `{DB_PREFIX}frights` set name = "media.file" and description = "Allows access to file administration (create, edit and delete are based on item permissions)." where name = "admin_items"',

        'update `{DB_PREFIX}group_frights` set fright = "user.own.profile" where fright = "edit_own_profile"',
        'update `{DB_PREFIX}frights` set name = "user.own.profile" where name = "edit_own_profile"',

        'ALTER TABLE `{DB_PREFIX}template` DROP `content`',

        'ALTER TABLE `{DB_PREFIX}user_group_mapping` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user_group_mapping` CHANGE `userid` `userid` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}user_attributes` CHANGE `attribute_value` `attribute_value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci',
        'ALTER TABLE `{DB_PREFIX}user_attributes` CHANGE `attribute_name` `attribute_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user_attributes` CHANGE `userid` `userid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user_attributes` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user_attributes` DROP PRIMARY KEY',
        'ALTER TABLE `{DB_PREFIX}user_attributes` ADD PRIMARY KEY ( `cid` , `userid` , `attribute_name` )',

        'ALTER TABLE `{DB_PREFIX}unique_name` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}unique_name` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}unique_name` DROP INDEX `cms_filename',
        'ALTER TABLE `{DB_PREFIX}unique_name` ADD UNIQUE `unique_name_filename` (`cid` ,`name`)',

        'ALTER TABLE `{DB_PREFIX}session` CHANGE `id` `id` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ',
        'ALTER TABLE `{DB_PREFIX}session` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}session` CHANGE `userid` `userid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}session` CHANGE `ip` `ip` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ',
        'ALTER TABLE `{DB_PREFIX}session` ADD `modified` INT( 11 ) NOT NULL AFTER `data`',
        'ALTER TABLE `{DB_PREFIX}session` DROP INDEX `cms_timestamp`',
        'ALTER TABLE `{DB_PREFIX}session` ADD INDEX `session_timestamp` ( `timestamp` )',

        'ALTER TABLE `{DB_PREFIX}user` CHANGE `cid` `cid` INT( 11 ) NOT NULL ',
        'ALTER TABLE `{DB_PREFIX}user` CHANGE `username` `username` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user` CHANGE `password` `password` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}user` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',
        'ALTER TABLE `{DB_PREFIX}user` DROP INDEX `cms_id`',
        'ALTER TABLE `{DB_PREFIX}user` ADD UNIQUE `user_id` (`cid` , `username`)',

        'ALTER TABLE `{DB_PREFIX}group_frights` DROP `value`',
        'ALTER TABLE `{DB_PREFIX}group_frights` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}group_frights` CHANGE `group_id` `group_id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}group_frights` CHANGE `fright` `fright` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}group_right` CHANGE `itemtype` `itemtype` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}group_right` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}group_right` CHANGE `itemid` `itemid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}group_right` CHANGE `group_id` `group_id` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}groups` CHANGE `group_id` `group_id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}groups` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}groups` CHANGE `group_name` `group_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',

        'ALTER TABLE `{DB_PREFIX}category` DROP `position`',
        'ALTER TABLE `{DB_PREFIX}category` CHANGE `id` `id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}category` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}category` CHANGE `parentid` `parentid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}category` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',
        'ALTER TABLE `{DB_PREFIX}category` DROP INDEX `cms_tree_req`',
        'ALTER TABLE `{DB_PREFIX}category` ADD INDEX `category_tree_req` ( `cid` , `parentid` )',

        'ALTER TABLE `{DB_PREFIX}id_gen` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}id_gen` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}id_gen` CHANGE `value` `value` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_category` CHANGE `itemtype` `itemtype` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_category` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_category` CHANGE `itemid` `itemid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_category` CHANGE `categoryid` `categoryid` INT( 11 ) NOT NULL',

        'ALTER TABLE `{DB_PREFIX}autojobs` CHANGE `name` `name` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}autojobs` CHANGE `classname` `classname` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}autojobs` DROP INDEX `cms_startup`',
        'ALTER TABLE `{DB_PREFIX}autojobs` ADD INDEX `autojobs_startup` ( `cid`,`state`,`next` )',

        'ALTER TABLE `{DB_PREFIX}logging` ADD INDEX `logging_id` (`cid` , `userid` , `timestamp`)',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `file` `file` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ""',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `level` `level` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `namespace` `namespace` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ""',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `timestamp` `timestamp` INT( 11 ) NOT NULL ',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `userid` `userid` INT( 11 ) NOT NULL ',
        'ALTER TABLE `{DB_PREFIX}logging` CHANGE `cid` `cid` INT( 11 ) NOT NULL ',

        'ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `itemtype` `itemtype` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `id` `id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_text` CHANGE `project_key` `project_key` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `itemtype` `itemtype` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `id` `id` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}item_project_num` CHANGE `project_key` `project_key` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}frights` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}frights` DROP `defaultvalue`',
        'ALTER TABLE `{DB_PREFIX}frights` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',

        'ALTER TABLE `{DB_PREFIX}content` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}content` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}content` CHANGE `cnt_type` `cnt_type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci default "html"',
        'ALTER TABLE `{DB_PREFIX}content` CHANGE `state` `state` VARCHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci default "R"',
        'ALTER TABLE `{DB_PREFIX}content` DROP INDEX `cms_search`',
        'ALTER TABLE `{DB_PREFIX}content` ADD INDEX `content_item_all` (`cid`,`id`,`language`)',

        'ALTER TABLE `{DB_PREFIX}configuration` DROP INDEX `cms_cid_package`',
        'ALTER TABLE `{DB_PREFIX}configuration` ADD INDEX `configuration_cid_package` (`cid`,`package`)',
        'ALTER TABLE `{DB_PREFIX}configuration` CHANGE `cid` `cid` INT( 11 ) NOT NULL',
        'ALTER TABLE `{DB_PREFIX}configuration` CHANGE `package` `package` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}configuration` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
        'ALTER TABLE `{DB_PREFIX}configuration` CHANGE `value` `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default ""',
        'ALTER TABLE `{DB_PREFIX}configuration` CHANGE `type` `type` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default "string"',
    );

    $connection = $GLOBALS['_BIGACE']['SQL_HELPER']->getConnection();
    $errors = array();

    foreach ($sqls as $stmt)
    {
        $stmt .= ';';
        $stmt = $GLOBALS['_BIGACE']['SQL_HELPER']->prepareStatement($stmt);
        $res = $connection->sql($stmt);
        if ($res->isError()) {
            $errors[] = 'SQL ERROR in <b>'.$stmt.'</b>';
        } else  {
            $error = $connection->getError();
            if ($error !== null) {
                $errors[] = 'SQL ERROR: ' . $error->getMessage() . ' in <b>'.$stmt.'</b>';
            }
        }
    }
    return $errors;
}

function connectZendDb($options)
{
    try {
        $adapterOptions = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => false
        );

        $dbAdapter = Zend_Db::factory(
            $options['database']['type'], array(
                'host'     => $options['database']['host'],
                'username' => $options['database']['user'],
                'password' => $options['database']['pass'],
                'dbname'   => $options['database']['name'],
                'charset'  => $options['database']['charset'],
                'prefix'   => $options['database']['prefix'],
                'options'  => $adapterOptions
            )
        );

        // make sure database connection is established
        $dbAdapter->getConnection();

        // setup legacy database layer
        return $dbAdapter;

    } catch (Zend_Db_Adapter_Exception $e) {
        echo '<p class="error">'.'Could not connect to database using Adapter "'. $adapterName .'": ' . $e->getMessage().'</p>';
    } catch (Zend_Exception $e) {
        echo '<p class="error">'.'Could not connect to database: '. $e->getMessage().'</p>';
    }
    return null;
}

/**
 * Displays a simple template header.
 */
function tpl_header()
{
    echo '<html><head>
    <style type="text/css">
    * { margin:0;padding:0;}
    body {font-family: sans-serif; font-size:13px; padding: 10px; }
    .hint { color:green; font-size:16px; font-weight: bold;}
    .error {color:red;}
    textarea {height:400px; width:100%; margin: 10px 0;}
    h1,h2,h3 {margin-top: 10px;}
    ul {margin-left: 20px;}
    .next {margin-top: 20px; font-size: 120%; border-top: 1px solid #eee;padding-top:10px; }
    pre { padding: 10px; border: 1px solid #ccc; }
    li { padding-top: 10px; }
    .step { font-size: 150%; font-weight: bold; text-decration:underline;}
    </style>
    </head><body>';
}

/**
 * Displays a simple template footer.
 */
function tpl_footer()
{
    echo '</body></html>';
}

/**
 * Displays an array of error messages.
 */
function tpl_error($errors)
{
    foreach($errors as $msg)
        echo '<p class="error">'.$msg.'</p>';
    return count($errors);
}

// =======================================================================================
// START THE MIGRATION PROCESS
// =======================================================================================

tpl_header();

if(file_exists(MIGRATE_ROOT.'system/libs/init_session.inc.php'))
{
    if (!isset($_GET['confirm']))
    {
        echo '
            <h1>Bigace Migration helper</h1>
            <p>This script will help you to convert your existing Bigace 2 website to a Bigace 3 powered website.</p>
            <p>Before you start make sure, that the script has all required file permissions.</p>
            <p>It is very important that this script can write all folders and files within your system, otherwise it will fail and create a disaster...
            YOU CAN EXECUTE THIS SCRIPT ONLY <u>ONCE</u> - IT WILL GENERATE ERRORS ON SUBSEQUENT CALLS!</p>

            <h2 class="hint">!!!! STOP - CREATE A BACKUP OF FILESYSTEM AND DATABASE FIRST !!!!</h2>
            ';


        $allowNext = true;

        $checkFiles = array(
            MIGRATE_ROOT,
            MIGRATE_ROOT.'public/',
            MIGRATE_ROOT.'public/.htaccess',
            MIGRATE_ROOT.'public/index.php',
            MIGRATE_ROOT.'public/robots.txt',
            MIGRATE_ROOT.'public/{CID}/',
            MIGRATE_ROOT.'public/system/',
            MIGRATE_ROOT.'system/',
            MIGRATE_ROOT.'misc/',
            MIGRATE_ROOT.'plugins/',
            MIGRATE_ROOT.'addon/',
            MIGRATE_ROOT.'admin.php',
            MIGRATE_ROOT.'index.php',
            MIGRATE_ROOT.'login.php',
            MIGRATE_ROOT.'robots.txt',
        );

        echo '<ul>';
        foreach ($checkFiles as $filename)
        {
            if (file_exists($filename) && !is_writable($filename))
            {
                $allowNext = false;
                echo '<li class="error">' . str_replace(MIGRATE_ROOT, '', $filename) . ' is not writable</li>';
            }
        }
        echo '</ul>';

        if ($allowNext) {
            echo '<p class="next"><a href="'.MIGRATE_SELF.'?confirm=1">Click here to start migration</a></p>';
        } else {
            echo '<p class="next"><a href="'.MIGRATE_SELF.'">Click here to reload this page</a></p>';
        }
    }
    else
    {
        // -------------------- initial preparation --------------------

        require_once(MIGRATE_ROOT.'system/libs/init_session.inc.php');

        initMigration();

        $count = 0;

        // FILESYSTEM WORK
        echo '<p class="step">Creating folder...</p>';
        $errors = createFolder();
        $count += tpl_error($errors);

        echo '<p class="step">Migrating config to new format...</p>';
        $errors = migrateConfig();
        $count += tpl_error($errors);

        echo '<p class="step">Moving files and folders...</p>';
        $errors = moveFilesAndFolder();
        $count += tpl_error($errors);

        echo '<p class="step">Deleting deprecated files...</p>';
        $errors = deleteFiles();
        $count += tpl_error($errors);

        // DATABASE and COMMUNITY WORK

        echo '<p class="step">Importing content...</p>';
        $errors = baImportContent();
        $count += tpl_error($errors);

        echo '<p class="step">Fixing unique Item URLs...</p>';
        $errors = fixUniqueName();
        $count += tpl_error($errors);

        echo '<p class="step">Updating communities...</p>';
        $errors = changeCommunities();
        $count += tpl_error($errors);

        echo '<p class="step">Exporting Smarty templates...</p>';
        $errors = baExportSmarty();
        $count += tpl_error($errors);

        echo '<p class="step">Migrating database...</p>';
        $errors = migrateDatabase();
        $count += tpl_error($errors);

        echo '<p class="step">Cleanup database...</p>';
        $errors = baCleanupDatabase();
        $count += tpl_error($errors);

        if ($count == 0) {
            echo '<h1>Yeah, step 1 finished without errors. Perfect! </h1>';
        } else {
            echo '<h1>'.$count.' error(s) occured during migration</h1>';
        }

        echo '<p class="next"><a href="'.MIGRATE_SELF.'">Click here for step 2</a>.</p>';
    }
}
else
{
    initMigration();

    if(file_exists(MIGRATE_ROOT.'__backup/') && !file_exists(MIGRATE_ROOT.'storage/logging/'))
    {
        if (file_exists(MIGRATE_ROOT.'bigace_3.0.zip') && function_exists('exec')) {
            $result = array();
            exec('unzip -n ' . MIGRATE_ROOT.'bigace_3.0.zip 2>1', $result);
            echo '<h1>Tried to extract bigace_3.0.zip for you:</h1>';
            echo '<textarea>'.implode(PHP_EOL, $result).'</textarea>';
            echo '<p class="hint">If anything failed, you need to unzip it manually!</p>';
            echo '<p class="next"><a href="'.MIGRATE_SELF.'">Click here for step 3</a>.</p>';
        } else {
            echo '<h1>Hmmm, I DO NOT KNOW WHAT TO DO ...</h1>
            <h2 class="hint"> ... did you extract the Bigace 3 ZIP into this folder?</h2>
            <p><br/>
            You can either upload the bigace_3.0.zip here and reload the page OR you extract the bigace_3.0.zip into this folder!</p>
            <p class="next"><a href="'.MIGRATE_SELF.'">Click here to retry</a></p>
            ';
        }

    }
    else
    {
        $ids = baGetAllCids();

        if(count($ids) > 0 && file_exists(MIGRATE_ROOT.'sites/cid{CID}/') && file_exists(MIGRATE_ROOT.'public/cid{CID}/'))
        {
            defined('APPLICATION_ROOT')
                || define('APPLICATION_ROOT', realpath(MIGRATE_ROOT . 'application'));

            defined('APPLICATION_PATH')
                || define('APPLICATION_PATH', APPLICATION_ROOT);

            defined('APPLICATION_ENV')
                || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

            require_once(MIGRATE_ROOT.'library/Bigace/constants.inc.php');
            require_once(MIGRATE_ROOT.'library/Bigace/Installation/FileSet.php');

            $errors = upgradeCommunities();
            tpl_error($errors);

            $errors = fixPermissions();
            tpl_error($errors);

            $config = include(MIGRATE_ROOT.'application/bigace/configs/bigace.php');
            $prefix = $config['database']['prefix'];
            set_include_path(implode(PATH_SEPARATOR,
                array(
                    realpath(MIGRATE_ROOT . 'library/'),
                    get_include_path(),
                )
            ));
            require_once('Zend/Loader.php');
            require_once('Zend/Db.php');



            echo '<h2>Done!</h2>
                  <p>Everything possible was automatically migrated for you. </p>
                  <p class="hint">Please read the next stuff carefully and follow the instructions!</p>
                  <h3>Helpful stuff:</h3>
                  <ul>
';
                $cids = baGetAllCids();
                $connection = connectZendDb($config);
                foreach($cids as $cid)
                {

                    echo '<li>
                    <hr><h3>Community '.$cid.'</h3>
                        Switch all pages to use the default design. Consider this <u>ONLY</u> if you want to switch to PHP templates.
                         Community '.$cid.':
                            <b>ATTENTION:</b>
                            You need to perform the SQL <u>for each Community</u> that wants to use PHP templates.
                        <br>
                        <pre>UPDATE `'.$prefix.'item_1` SET text_4 = "" WHERE cid = '.$cid.';</pre>
                      </li>';


                    $stmt = 'SELECT value from `'.$prefix.'configuration` WHERE package = "news" AND name = "root.id" and cid = '.$cid.';';
                    $newsId = '{NEWS_ROOT_ID}';
                    if ($connection !== null)
                    {
                        try {
                            $temp = $connection->fetchCol($stmt);
                            /*
                            $newsRes = $connection->query($stmt);
                            $temp = $newsRes->fetchAll();
                            $newsRes->closeCursor();
                            */
                            if (count($temp) > 0)
                            {
                                $newsId = $temp[0];
                    echo '<li>
                        Update News extension - Community '.$cid.':
                            <b>ATTENTION:</b>
                            You need to perform the SQL <u>for each Community</u> that used the News extension!
                        <br>
                            ';

                        echo '
<pre>
UPDATE `'.$prefix.'item_1` SET text_4 = "", type = "newshome" where id = '.$newsId.' and cid = '.$cid.';
UPDATE `'.$prefix.'item_1` SET text_4 = "", type = "news" where parentid = '.$newsId.' and cid = '.$cid.';
UPDATE `'.$prefix.'item_1` SET `date_2` = `createdate` where parentid = '.$newsId.' and (`date_2` = 0 OR `date_2` = NULL) and cid = '.$cid.';
UPDATE `'.$prefix.'item_1` SET `valid_to` = '.MIGRATE_VALIDTO.' where parentid = '.$newsId.' and cid = '.$cid.';
</pre>
</li>
';
                            }
                        } catch (Exception $ex) {
                            echo '<p class="error">'.$ex->getMessage().'</p>';
                        }
                    }

                }

                        echo '
                      <li>
                        To use your existing Smarty templates, please login to your new system and upload the Smarty extension.
                        Install it and then activate the Smarty Plugin. Now you should be able to use your old templates.
                        But they might be broken ... it was not possible to keep all Smarty Tags backward compatible :(
                        Sorry!!!! <br/>Please <a href="http://forum.bigace.de/beta-tester/changelog-v3/msg5834/#msg5834" target="_blank">read this guide</a> to find out more.
                      </li>
                  </ul>
                  ';

            echo '<p class="next">';
            echo 'Your new website will use the v3 default design. In order to use the old templates,
                    you have to install the Smarty extension!
                  <br/><br/>';
            echo '<a href="index.php">Click here to see your new website</a>.';
            echo '</p>';

        } else {
            echo '<h1>Hmmm, I DO NOT KNOW WHAT TO DO ...</h1>';
            echo '<h1> ... did you extract the Bigace 3 ZIP into this folder?</h1>';
            echo '<p class="next"><a href="'.MIGRATE_SELF.'">Click here or reload after extracting bigace_3.0.zip for step 3</a>.</p>';
        }
    }
}
tpl_footer();
