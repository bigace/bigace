<div class="footer">
|&nbsp;Copyright <?php echo date('Y'); ?>&nbsp;|&nbsp;<a href="<?php echo createMenuLink(DESIGN_LINKID_IMPRESSUM); ?>">Impressum</a>&nbsp;|&nbsp;<a href="<?php echo createMenuLink(DESIGN_LINKID_SITEMAP); ?>">Sitemap</a>&nbsp;|&nbsp;<?php

    $APPS->setShowPicture(false);
    $APPS->setShowText(true);
    echo $APPS->getLink($APPS->STATUS);
    
?>&nbsp;|</div>