<?php
/**
 * Standard include for displaying one of the following Contents:
 *
 * - historical
 * - future
 * - current
 *
 * Copyright (C) Kevin Papst.
 *
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.modul
 */

    $seenContent = false;

    if (isset($_GET['historyID']))
    {
        // Display the History Version
        import('classes.item.ItemHistoryService');
        $ihs = new ItemHistoryService(_BIGACE_ITEM_MENU);
        $temp = $ihs->getHistoryVersion($GLOBALS['MENU']->getID(), $GLOBALS['MENU']->getLanguageID(), $_GET['historyID']);
        $url = $temp->getFullURL();
        if (file_exists($url) && is_file($url)) {
            $fp = fopen($url, "rb");
            $content = fread ($fp, filesize($url));
            fclose($fp);
        	echo $content;
            $seenContent = true;
        }
    }
    else if(isset($_GET['previewItemID']) && isset($_GET['previewLanguageID']))
    {
        // Display a Preview of the Future Version
        import('classes.item.ItemFutureService');
        $ifs = new ItemFutureService(_BIGACE_ITEM_MENU);
        if ($ifs->hasFutureVersion($_GET['previewItemID'], $_GET['previewLanguageID']))
        {
            $future = $ifs->getFutureVersion($_GET['previewItemID'], $_GET['previewLanguageID']);
            echo $future->getContent();
            $seenContent = true;
        }
    }

    if(!$seenContent)
    {
        $cont = $GLOBALS['MENU']->getContent();
        if(strlen($cont) == 0)
        {
            if(ConfigurationReader::getConfigurationValue('content', 'show.default.content', true))
            {
                import('classes.language.ItemLanguageEnumeration');
                $ile = new ItemLanguageEnumeration($GLOBALS['MENU']->getItemtype(), $GLOBALS['MENU']->getID());
                for($i=0; $i < $ile->count(); $i++)
                {
                    $tempLanguage = $ile->next();
                    if ($tempLanguage->getID() != $GLOBALS['MENU']->getLanguageID()) {
                        $newMenu = new Menu($GLOBALS['MENU']->getID(), ITEM_LOAD_FULL, $tempLanguage->getID());
                        $cont = $newMenu->getContent();
                        unset($tempLanguage);
                        unset($newMenu);
                        break;
                    }
                }
            }
        }
        echo $cont;
        unset($cont);
    }

    unset($seenContent);


?>