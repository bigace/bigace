<?php
 
$DEFINITION['BIGACE-DESIGN2'] = array (

    'NAME'          => 'BIGACE-DESIGN2',
    'TITLE'         => 'BIGACE DESIGN 2',
    'STANDARD'      => 'bigacedesign2/design.php',
    'DESCRIPTION'   => 'BIGACE Design 2 - Print Page - Last edited Items',
    'KEYS'          => array (
                        'print'     => 'bigacedesign2/print.php'
    ),
    'portlet_columns' => array( 'left', 'right' ),
    'portlet'       => array( 
                        'ToolPortlet', 
                        'NavigationPortlet', 
                        'LastEditedItemsPortlet',
                        'LoginMaskPortlet',
                        'QuickSearchPortlet'
                       ),
    'PUBLIC'        => true,
    'CSS'           => _BIGACE_DIR_PUBLIC_WEB . 'cid' . _CID_ . '/bigacedesign2/design.css'

);

?>