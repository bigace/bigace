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
 */


/**
 * This uninstaller script needs to go into the BIGACE main directory.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */

function recursive_remove_directory($directory, $empty=FALSE)
{
	if(substr($directory,-1) == '/') {
		$directory = substr($directory,0,-1);
	}

	if(!file_exists($directory) || !is_dir($directory)) {
		return FALSE;
	}
	elseif(!is_readable($directory)) {
		return FALSE;
	}
	else {
		$handle = opendir($directory);
		while (FALSE !== ($item = readdir($handle))) 
		{
			if($item != '.' && $item != '..') {
				$path = $directory.'/'.$item;
				if(is_dir($path)) {
					recursive_remove_directory($path);
				}
				else{
					unlink($path);
				}
			}
		}
		closedir($handle);

		if($empty == FALSE) {
			if(!rmdir($directory)) {
				return FALSE;
			}
		}
		return TRUE;
	}
}


$allRemove = array(
    'addon',
    'consumer',
    'misc',
    'plugins',
    'public',
    'system',
    '.htaccess',
    'admin.php',
    'login.php',
    'robots.txt',
    'install.php',
    'index.php',
    'README'
);

define('CUR', dirname(__FILE__).'/');

$errors = array();
$deleted = false;
if(isset($_POST['action']) && $_POST['action'] == 'delete')
{
    foreach($allRemove AS $a) {
        $cur = CUR . $a;
        if(file_exists($cur)) {
            if(!@chmod($cur, 0777))
                $errors[] = "Could not set permission for: " . $cur;
            if(is_file($cur)) {
                if(!@unlink($cur))
                    $errors[] = "Could not remove file: " . $cur;
            } 
            else {
                if(!recursive_remove_directory($cur))
                    $errors[] = "Could not delete directory: " . $cur;
            }
        }
    }
    $deleted = true;
}

?><html>
<head>
    <title>BIGACE Uninstall</title>
</head>

<body>
<?php 
if($deleted)
{
    if(count($errors) == 0) {
        ?>
        <p>You successfully removed BIGACE from your server.</p>
        <?php
    }
    else {
        echo "<p>There were errors during BIGACE un-installation:<ul>";
        foreach($errors AS $e) {
            echo "<li>".$e."</li>\n";
        }
        echo "</ul><br/>Try reloading this page!</p>";
    }
}
else 
{
    ?>
    <p>This script tries to uninstall BIGACE. Please place it in the BIGACE folder.</p>

    <p>
    	<b>MAKE SURE YOUR BACKUPS WORK. THIS WILL COMPLETELY REMOVE BIGACE FROM YOUR SERVER. YOU CANNOT UNDO THIS ACTION!</b>
    	<br/>
    	Keep in mind, that you still have to delete your database!
    </p>

    <p>The following directories and files will be deleted:
        <ul>
        <?php 
            foreach($allRemove AS $a) {
                if(file_exists(CUR.$a))
                    echo "<li>".$a."</li>\n";
            }
        ?>        
        </ul>
    </p>    

    <form action="" method="post">
        <input type="hidden" name="action" value="delete" />
        <button type="submit">Uninstall now!</button>
    </form>
    <?php
}
?>
</body>
</html>