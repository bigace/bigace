<?php
    
    define('ACTUAL_VERSION', '2.0');
    define('DATABASE_XML', getDir('system/sql/') . '/structure.xml');
    define('METHOD_INDEX', 1);
    define('METHOD_DB_CORE', 2);

    $method = isset($_POST['method']) ? 0+$_POST['method'] : METHOD_INDEX;

    $steps = array();

    function stderr($msg, $title = '') {
        echo '<br><span style="color:red;"><b>'.$title.' =&gt; ' . $msg.' !!!</b></span>';
    }

    function stdinfo($msg, $title = '') {
        echo ($title == '' ? '' : '<b>'.$title.'</b>: ') . $msg;
    }

    function getDir($dir) {
        return realpath(dirname(__FILE__).'/'.$dir);
    }

?>
<html>
<head>
    <title>BIGACE UPGRADE</title>
    <style type="text/css">
    body, td, p, th { font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size:12px; }
    body { margin:0px; background-color:#eeeeee; }
    .outerTable { margin-top:10px; background-color:#eeeeee; border:0px; width:100%;}
	.header
	{
		background-image: url(http://www.bigace.de/public/bigace/bg.jpg);
		background-repeat: repeat-x;
		background-color: #7bb000;
		padding: 5px 4% 7px 20px;
        margin-bottom:0px;
		font-size: 18px;
		border-bottom: 1px solid black;
	}
    .name {font-weight:bold;font-size:22px; }
    .upgradecolumn { padding-left:20px;padding-right:20px;border:0px;padding-bottom:15px;  }
    .updateBox { background-color:#ffffff;border:1px solid #999999;padding:5px; }
    h1 { margin:0px; }
    h2 { margin:0px; }
    </style>
</head>
<body>
		<div class="header">
            <table border="0">
                <tr>
                    <td valign="center"><img src="http://www.bigace.de/public/bigace/bigace_logo.gif" border="0" style="margin-right:10px;"></td>
                    <td valign="center"><span class="name">BIGACE <?php echo ACTUAL_VERSION; ?></span><br>...easily manages your Content!</td>
                </tr>
            </table>
		</div>
        <table class="outerTable" cellspacing="0" cellpadding="0" align="center">
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h1>UPGRADE TOOLKIT</h1>
            This tool will upgrade your BIGACE Version!
            <br/><br/>
            But before you start the upgrade, remember to <b>BACKUP, BACKUP, BACKUP</b>!!!!!
            </div>
        </td></tr>
<?php
if(file_exists(getDir('system/config/') . '/config.system.php'))
{
    $_BIGACE = array();
    require_once( getDir('system/config/') . '/config.system.php');
    require_once( getDir('system/libs/adodb/') . '/adodb.inc.php' );
    require_once( getDir('system/libs/adodb/') . '/adodb-xmlschema03.inc.php' );

    ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Step 1: Database Upgrade</h2>
            In the first step the Core Database will be updated. Then all community data will be upgraded, 
            to be compatible with <?php echo ACTUAL_VERSION; ?>.
            <?php 
    if($method == METHOD_DB_CORE) 
    {
            echo '<br><br>';

            if(file_exists(DATABASE_XML)) 
            {
                $db = ADONewConnection( $_BIGACE['db']['type'] );
                @$db->Connect( $_BIGACE['db']['host'], $_BIGACE['db']['user'], $_BIGACE['db']['pass'], $_BIGACE['db']['table'] );
                if($db->IsConnected()) {
                    $schema = new adoSchema($db);
        			$schema->ExistingData(XMLS_MODE_UPDATE);
                    $sql = @$schema->ParseSchema(DATABASE_XML);
                    if($sql === FALSE) {
                        stderr('Could not parse Database Structure File !', 'XML Error');
                    }
                    else {
            			$newSqlArray = array();
    	    	        foreach($sql AS $statement) {
                          	// make sure to replace the DB Prefix 
                            $newSqlArray[] = str_replace("{CID_DB_PREFIX}",$_BIGACE['db']['prefix'],$statement);
        		        }
                        $result = $schema->ExecuteSchema($newSqlArray, true);
                        if($result == 0) {
                            stderr('Could not install DB: ' . $result, 'DB Error');
                        } else {
                            stdinfo('Upgraded you Database!', 'Success');
                        }
                    }
                    unset ($sql);            
                }
                else {
                    stderr('Database connection could not be established!', 'Connection failed!');
                }
                unset($db);
            }
            else {
                stderr('XML Structure File missing: ' . DATABASE_XML, 'File missing');
            }   
    } 
            ?>
            </div>
        </td></tr>
        <?php 
        if($method != METHOD_DB_CORE) {
            ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox">
            <h2>Start Upgrade</h2>
            <form name="" action="" method="POST">
                <input type="hidden" name="method" value="<?php echo METHOD_DB_CORE; ?>">
                Hit the button to start the Upgrade. Make sure, you have a working backup! <input type="submit" value="Start..."></p>
            </form>
            </div>
        </td></tr>
            <?php
        }
}
else
{
            ?>
        <tr><td valign="top" class="upgradecolumn">
            <div class="updateBox" style="color:red;">
            <h2>Wrong directory</h2>
            This file must be placed into the BIGACE Root Directory! 
            </div>
        </td></tr>
            <?php
}
        ?>
        </table>
</body>
</html>
