<div id="pagetools"><p><?php
        echo '<a href="'.createMenuLink(_BIGACE_TOP_LEVEL).'" border="0" alt="'.getTranslation('home').'"><img src="' . $publicDir . 'bigacedesign2/home.gif" border="0" alt="'.getTranslation('home').'"></a>';
        echo '<a href="'.createMenuLink($GLOBALS['MENU']->getID() . '_tBIGACE-DESIGN2_kprint').'" title="'.getTranslation('print_page').'" target="printVersion" onclick="window.open(\''.createMenuLink($GLOBALS['MENU']->getID() . '_tRUSSIA-GERMANY_kprint').'\',\'printVersion\',\'scrollbars=yes,toolbar=no,menubar=yes,width=500,height=400,left=100,top=100\');"><img src="' . $publicDir . 'bigacedesign2/print.gif" border="0" alt="'.getTranslation('print_page').'"></a>';
        echo '<a href="'.createMenuLink(DESIGN_LINKID_CONTACT).'" title="'.getTranslation('contact_email').'"><img src="' . $publicDir . 'bigacedesign2/email.gif" border="0" alt="'.getTranslation('contact_email').'"></a>';
        echo $APPS->getAllLink();
        
?></p></div>