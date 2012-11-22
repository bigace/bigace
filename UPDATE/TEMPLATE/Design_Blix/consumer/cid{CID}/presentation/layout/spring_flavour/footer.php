<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * -------------------------------------------------
 * The BLIX Layout for BIGACE.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */
?>

<hr class="low" />

<div id="footer">

    <p><strong>&copy; Copyright <?php echo date("Y")." " . _BLIX_COPYRIGHT_BY; ?>. All rights reserved.</strong><br />
    Powered by <a href="http://sourceforge.net/projects/bigace/">BIGACE <?php echo _BIGACE_ID?></a>. 
    <?php 
    if(_BLIX_SHOW_STATUS)
    {
        loadClass('util', 'ApplicationLinks');
        if(_IS_ANONYMOUS) {
            echo '<a href="'.ApplicationLinks::getLoginFormURL($MENU->getID()).'">Login</a>';
        } else {
            echo '<a target="_blank" href="'.ApplicationLinks::getAdministrationURL($MENU->getID()).'">Admin</a> | ';
            echo '<a href="'.ApplicationLinks::getLogoutURL($MENU->getID()).'">Logout</a>';
        }
    }
    ?></p>
</div>

</div>

</body>

</html>