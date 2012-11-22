<?php

    echo '<title>'.$_SERVER['HTTP_HOST'].' - ' . getTranslation('print_page') . ' "' . $MENU->getName() . '"</title>' . "\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.$LANGUAGE->getCharset().'">' . "\n";
    echo '<meta name="description" content="'.$MENU->getDescription().'">' . "\n";
    echo '<meta name="robots" content="index,follow">' . "\n";
    echo '<meta name="generator" content="BIGACE v'._BIGACE_ID.'">' . "\n";
    echo '<meta name="language" content="'.$LANGUAGE->getLocale().'">' . "\n";
    echo '<link rel="stylesheet" href="' . $publicDir . 'bigacedesign2/design.css" type="text/css">' . "\n";

?>