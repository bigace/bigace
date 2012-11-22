        <div id="menuList">
        <?php
        
        // Receive all IDs on our Way Home
        $wayHomeInfo = array();

        $TOP_LEVEL = $MENU_SERVICE->getMenu(_BIGACE_TOP_LEVEL, $MENU->getLanguageID());
        $AMENU = $MENU;
        while (($AMENU->getParentID() != _BIGACE_TOP_PARENT)) {
            $wayHomeRight = $RIGHT_SERVICE->getMenuRight($GLOBALS['_BIGACE']['SESSION']->getUserID(), $AMENU->getID());
            if ($wayHomeRight->canRead()) {
                array_push($wayHomeInfo, array($AMENU->getID() => $AMENU->getName()));
            }
            $AMENU = $AMENU->getParent();
            unset ($wayHomeRight);
        }
        unset ($AMENU);
        
        $menu_info = $MENU_SERVICE->getLightTreeForLanguage(_BIGACE_TOP_LEVEL, $GLOBALS['_BIGACE']['SESSION']->getLanguageID());
        echo '<ul class="menu1List">';
        
        for ($i=0; $i < $menu_info->count(); $i++) 
        {
            $temp_menu = $menu_info->next();
            
            // If current Menu is selected Menu show Childs
            $showChilds = ($temp_menu->getID() == $MENU->getID());
            
            // otherwise see if we are in a subtree of current Menu
            if (!$showChilds) 
            {
                for ($a = 0; $a < count($wayHomeInfo); $a++) {
                    foreach ($wayHomeInfo[$a] AS $key => $val)
                    {
                        if ($key == $temp_menu->getID()) {
                            $showChilds = true;
                        }
                    }
                }
                
                unset ($a);
            }
            
            echo '<li><a href="' . createMenuLink($temp_menu->getId()) . '" title="' . $temp_menu->getDescription() . '" class="menuLevel1">'.$temp_menu->getName().'</a></li>';
            
            if ($showChilds) 
            {
                $menu_info2 = $MENU_SERVICE->getLightTreeForLanguage( $temp_menu->getId(), $GLOBALS['_BIGACE']['SESSION']->getLanguageID() );
                if ($menu_info2->count() > 0)
                {
                    // display following for html validation (and display corruption)
                    //echo '<li class="blindLi">';
                    echo '<ul class="menu2List">';
                    for ($a=0; $a < $menu_info2->count(); $a++) 
                    {
                        $temp_menu2 = $menu_info2->next();

                        // If current Menu is selected Menu show Childs
                        $showChilds2 = ($temp_menu2->getID() == $MENU->getID());
                        
                        // otherwise see if we are in a subtree of current Menu
                        if (!$showChilds2) 
                        {
                            for ($a2 = 0; $a2 < count($wayHomeInfo); $a2++) {
                                foreach ($wayHomeInfo[$a2] AS $key => $val)
                                {
                                    if ($key == $temp_menu2->getID()) {
                                        $showChilds2 = true;
                                    }
                                }
                            }
                            
                            unset ($a2);
                        }
                        
                        // Display 2level menu
                        echo '<li><a href="' . createMenuLink($temp_menu2->getId()) . '" title="' . $temp_menu2->getDescription() . '" class="menuLevel1">'.$temp_menu2->getName().'</a></li>';

                        // Show 3 level menu?
                        if ($showChilds2) 
                        {
                            $menu_info3 = $MENU_SERVICE->getLightTreeForLanguage( $temp_menu2->getId(), $GLOBALS['_BIGACE']['SESSION']->getLanguageID() );
                            if ($menu_info3->count() > 0)
                            {
                                // display following for html validation (and display corruption)
                                //echo '<li class="blindLi">';
                                echo '<ul class="menu3List">';
                                for ($a3=0; $a3 < $menu_info3->count(); $a3++) 
                                {
                                    $temp_menu3 = $menu_info3->next();
                                    echo '<li><a href="' . createMenuLink($temp_menu3->getId()) . '" title="' . $temp_menu3->getDescription() . '" class="menuLevel1">'.$temp_menu3->getName().'</a></li>';
                                    unset ($temp_menu3);
                                }
                                echo '</ul>';
                                unset ($a3);
                            }
                            
                            unset ($menu_info3);
                        }
                        unset ($showChilds2);
                        unset ($temp_menu2);
                    }
                    echo '</ul>';
                    unset ($a);
                }
                unset ($menu_info2);
            }
            unset ($showChilds);
        }
        unset ($i);
        echo '</ul>';
        ?>        
        </div>
