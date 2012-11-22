<?php
    // Create Way Home if not TOP_LEVEL is shown

    if (_BIGACE_TOP_LEVEL != $MENU->getID()) 
    {
        echo '<div class="wayhome"><a href="' . createMenuLink($TOP_LEVEL->getID()) . '">'.$TOP_LEVEL->getName().'</a>&nbsp;';

        $ahtml = '';
        for ($i = 0; $i < count($wayHomeInfo); $i++) {
            foreach ($wayHomeInfo[$i] AS $key => $val)
            {
                $ahtml = '&gt;&nbsp;<a href="' . createMenuLink($key) . '">' . $val . '</a>&nbsp;' . $ahtml;
            }
        }

        echo $ahtml;
        echo '</div>';
        unset ($ahtml);
    }
?>