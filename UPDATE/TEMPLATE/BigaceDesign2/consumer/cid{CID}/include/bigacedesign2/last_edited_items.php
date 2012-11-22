    <div id="lastEdited">
        <p>
        <?php 
            echo getTranslation('last_edited_items');
        ?>
        </p>
        <?php
        
            $temp = $MENU_SERVICE->getLastEditedItems( $GLOBALS['_BIGACE']['SESSION']->getLanguageID(), 0, 6 );
            
            for ($i=0; $i < $temp->count(); $i++)
            {
                $lastEdited = $temp->next();
                
                if ( $lastEdited->getID() != _BIGACE_TOP_LEVEL )
                {
                    echo '<div class="lastEditedItem">';
                    echo '<a href="' . createMenuLink( $lastEdited->getID() ) . '">';
                    echo $lastEdited->getName();
                    echo '</a>';
                    echo '<br>';
                    echo substr ( $lastEdited->getDescription(), 0, 50);
                    if (strlen($lastEdited->getDescription()) > 53) {
                        echo '...';
                    }
                    echo ' <a href="' . createMenuLink( $lastEdited->getID() ) . '">';
                    echo '<img src="'._BIGACE_DIR_PUBLIC_WEB.'cid'._CID_.'/bigacedesign2/3arrows.gif" alt="Hier lesen Sie mehr">';
                    echo '</a>';
                    echo '<br>';
                    echo '<i>(' . date("d.m.Y", $lastEdited->getLastDate()) . ')</i>';
                    echo '</div>';
                }
            }
        
        ?>
    </div>
