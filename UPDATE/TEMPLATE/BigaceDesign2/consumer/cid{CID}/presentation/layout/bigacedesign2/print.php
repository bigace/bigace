<?php
/**
* BIGACE DESIGN 2
*
* Copyright (C) Kevin Papst. 
*
* For further information go to http://www.bigace.de/ 
*
* @version $Id$
* @author Kevin Papst 
*/

$INCLUDE_SUB_DIR = 'bigacedesign2/';
define('DESIGN_INC_DIR', _BIGACE_DIR_CID . 'include/bigacedesign2/');

require_once(DESIGN_INC_DIR.'environment.php');
    
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <?php include(DESIGN_INC_DIR.'htmlhead_print.php'); ?>
    
    <script language="javascript">
    <!--
    function doclose() {
        self.close();
    } 
    //-->
    </script>
    
    <style type="text/css">
    .printTools 
    {
        position:absolute;
        top:10px;
        left:370px;
        font-size:75%;
        color:#000000;
    }
    .printHeader
    {
        margin:0px 10px 10px 10px;
        border-color:#000000;
        border-style:solid;
        border-width:0px 0px 1px 0px;
    }
    #printLogo
    {
        margin:10px 0px 10px 0px;
    }
    .printVersion 
    {
        margin:10px 10px 0px 10px;
        padding:10px 0px 10px 10px;
        border-color:#000000;
        border-style:solid;
        border-width:1px 0px 0px 0px;
        font-size:75%;
    }
    .spacer
    {
        padding:0px 5px 0px 0px;
    }
    </style>
</head>
<body>
    <div class="printHeader">
        <img src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/images/logosmall.gif" id="printLogo">
        <div class="printTools">
            <script language="javascript">
                document.write('<a href="javascript:void(0)" onclick="window.print();" class="spacer"><img src="<?php echo $publicDir; ?>bigacedesign2/print.gif" class="spacer"><?php echo getTranslation('print'); ?></a>');
                document.write('<div style="padding-bottom:2px;"></div>');
                document.write('<a href="javascript:void(0)" onclick="doclose();" class="spacer"><img src="<?php echo $publicDir; ?>bigacedesign2/close.gif" class="spacer"><?php echo getTranslation('close'); ?></a>');
            </script>
        </div>
    </div>
<?php

    echo '<div id="content">';

    include(_BIGACE_DIR_CID . 'include/loadModul.php'); 

    echo '</div>';

    echo '<div class="printVersion">';
    echo getTranslation('print_version') . ':<br>' . createMenuLink($MENU->getID());
    echo '<br>';
    echo '<br>';
    echo '&copy; Copyright '.date("Y").' ' . $_SERVER['HTTP_HOST'];
    echo '</div>';
?>
</body>
</html>