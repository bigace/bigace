<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{* $Id$ *}
<html>
<head>
    <title>{translate key="browser_title"}</title>
    <meta http-equiv="Content-Type" content="text/html; charset={$LANGUAGE->getCharset()}">
    <meta name="generator" content="{bigace_version full="true"}">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="{$CSS}" type="text/css">
    <script type="text/javascript" src="{directory name="addon_web"}jquery/jquery.js"></script>
    <script type="text/javascript" src="{directory name="addon_web"}jquery/tablesorter/jquery.tablesorter.js"></script>
    <link rel="stylesheet" href="{directory name="addon_web"}jquery/themes/bigace/bigace.css" type="text/css">
    {*
        <script type="text/javascript" src="{directory name="addon_web"}jquery/jgrowl/jquery.jgrowl.js"></script>
        <link rel="stylesheet" href="{directory name="addon_web"}jquery/jgrowl/jquery.jgrowl.css" type="text/css">
    *}
    {literal}
    <style type="text/css">
    body {
        margin:10px;
        font-size:12px;
    }
    #topLinkBox {
        border-top:1px solid #000000;
        border-bottom:1px solid #000000;
        margin-bottom:5px;
        padding:10px;
    }
    #topLinkBox a {
        border:1px solid #cccccc;
        background-color:#eeeeee;
        color:#000000;
        padding:3px;
        text-decoration:none;
    }
    #topLinkBox a:hover {
        border:1px solid #cccccc;
        background-color:#666666;
        color:#ffffff;
        padding:3px;
        text-decoration:none;
    }
    #scalePreview {
        visibility:hidden;
    }
    #jgrowlDiv { color: #fff;  }
    #jgrowlDiv img { border-width:0px; margin-right:10px; }
    #jgrowlDiv a { text-decoration:none; color: #fff; font-size:20px; font-weigth:bold; }
    #jgrowlDiv a:hover { text-decoration:underline; color: #fff; }
    #jgrowlDiv a:hover img { text-decoration:none;}
    </style>
    <script type="text/javascript">
    function showItemPreview(msg, fadeoutTime) {
        $.jGrowl.defaults.closerTemplate = {/literal}'<div>Close all</div>'{literal};
                
        if(fadeoutTime != null) {
            $("#jgrowlDiv").jGrowl(msg, { life: fadeoutTime });
            return;
        }

        $("#jgrowlDiv").jGrowl(msg, { sticky: true, speed:  'slow' });
    }

    function buttonAccept() {
        parent.window.acceptItem();
    }
    
    
    function showEditorErrorText(msg, fadeoutTime) {
        showEditorStatusText('<span id="msg-err">' + msg+'</span>', fadeoutTime);
    }

    function chooseItem(itemtype, itemid, languageid, filename, url, mimetype) 
    {
        parent.window.setSelectedItem(itemtype, itemid, languageid, filename, url, mimetype);
        parent.window.acceptItem();
    }
    
    function changeLanguageBySelect(selectBox)
    {
        var locale = selectBox.options[selectBox.selectedIndex].value;
        var current = location.href;
        var newUrl = current.replace(/&language=..&/, "&language="+locale+"&");
        location.href = newUrl;
    }
                
    function setCurrentItem( extension, item, url )
    {
        itempreview = item.getName();
        buttonAccept();
        /*
        if(item.getItemtype() == 4) {
            itempreview = '<img src="'+url+'" height="99">' + itempreview;
            showItemPreview('<a href="'+url+'" onclick="buttonAccept();return false;">' + itempreview + '</a>',null);
        }
        else {
            if(extension != null)
                itempreview = '<img src="icons/32/' + extension + '.gif">' + itempreview;
                
            showItemPreview('<a href="'+url+'" onclick="buttonAccept();return false;">' + itempreview + '</a>',null);
        }
        */
    }
    </script>
    {/literal}
</head>
<body>
<div id="jgrowlDiv" class="top-right"></div>