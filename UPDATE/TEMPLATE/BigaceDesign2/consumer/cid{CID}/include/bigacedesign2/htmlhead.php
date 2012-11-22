<?php 

	define('EMPTY_MENU', '&nbsp;&nbsp;');

	function printMenuJavascript($id, $languageid) 
	{
		$menu_info = $GLOBALS['MENU_SERVICE']->getLightTreeForLanguage($id, $languageid);
		
		for ($i=0; $i < $menu_info->count(); $i++) 
		{
			$temp_menu = $menu_info->next();
			$temp_name = prepareMenuValue($temp_menu->getName());
			echo "  ['".EMPTY_MENU."', '".$temp_name."', '".createMenuLink($temp_menu->getId())."', '_self', '".$temp_name."'";
			unset($temp_name);
			
			if ($temp_menu->hasChildren())
			{
				echo ",\n";
				printMenuJavascript( $temp_menu->getID(), $temp_menu->getLanguageID() );
			}
			echo "  ],\n";
		}
		unset($menu_info);
	}
	
	function prepareMenuValue($str) {
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace("'", '&#039;', $str);
        return $str;
	}

    echo "\n";
    echo '<title>' . $_SERVER['HTTP_HOST'] . ' :: ' . $MENU->getName() . ' ::</title>' . "\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.$LANGUAGE->getCharset().'">' . "\n";
    echo '<meta name="description" content="'.$MENU->getDescription().'">' . "\n";
    echo '<meta name="robots" content="index,follow">' . "\n";
    echo '<meta name="generator" content="BIGACE v'._BIGACE_ID.'">' . "\n";
    echo '<meta name="language" content="'.$LANGUAGE->getLocale().'">' . "\n";
    echo '<link rel="stylesheet" href="' . $publicDir . 'bigacedesign2/design.css" type="text/css">' . "\n";

    ?>

    <style type="text/css">
	.menu1List { padding-top:5px; list-style-image:url(<?php echo $publicDir; ?>bigacedesign2/1arrow.gif); }
	.menu2List { list-style-image:url(<?php echo $publicDir; ?>bigacedesign2/2arrows.gif); }
	.menu3List { list-style-image:url(<?php echo $publicDir; ?>bigacedesign2/3arrows.gif); }    
    </style>

    <?php

    echo '<SCRIPT LANGUAGE="JavaScript" SRC="'._BIGACE_DIR_PUBLIC_WEB.'JSCookMenu/JSCookMenu.js"></SCRIPT>' . "\n";
	echo '<LINK REL="stylesheet" HREF="'._BIGACE_DIR_PUBLIC_WEB.'JSCookMenu/ThemeBigace/theme.css" TYPE="text/css">' . "\n";
	echo '<SCRIPT LANGUAGE="JavaScript" SRC="'._BIGACE_DIR_PUBLIC_WEB.'JSCookMenu/ThemeBigace/theme.js"></SCRIPT>' . "\n";
	
	
	?>
	
<SCRIPT LANGUAGE="JavaScript"><!--
var myMenu =
[
<?php
	
	printMenuJavascript( _BIGACE_TOP_LEVEL, $GLOBALS['_BIGACE']['SESSION']->getLanguageID() );
	
?>
];
--></SCRIPT>

<?php
	
    echo $APPS->getAllJavascript();

?>