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

<div id="subcontent">
<?php
    foreach($_BLIX['PORTLETS'] AS $currentPortlet)
    {
        echo '<h2><em>'.$currentPortlet->getTitle().'</em></h2>';
        echo $currentPortlet->getHtml();
    }
?>	
</div>