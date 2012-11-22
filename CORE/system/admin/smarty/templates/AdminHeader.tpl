<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{* $Id$ *}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$ADMIN_LANGUAGE}" lang="{$ADMIN_LANGUAGE}">
    <head>
        <title>BIGACE {translate key="admin"}</title>
        <meta name="description" content="{translate key="admin"}">
        <meta name="generator" content="BIGACE {bigace_version}">
        <meta name="robots" content="noindex,nofollow,noarchive">
        <link rel="stylesheet" type="text/css" href="{$STYLE_DIR}style/header.css" />
        <script language="JavaScript" src="{directory name="public"}system/javascript/administration.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset={$CHARSET}">
        <script language="JavaScript">
            <!--
            {literal}
            function changeLanguage(languagelink) {
                top.location.href = languagelink;
            }
			function callSearch($searchterm) {
				return true;
			}
            {/literal}
            // -->
        </script>
    </head>
    <body>
		<div class="header"><img src="{directory name="public"}system/images/bigace_logo.jpg"></div>

    	<span id="topLinks">
        	{if isset($PROFILE_ADMIN)}
            	{translate key="hello"} <a href="{$PROFILE_ADMIN}" target="content">{$FIRSTNAME} {$LASTNAME} ({$USERNAME})</a>!
        	{else}
            	{translate key="hello"} {$FIRSTNAME} {$LASTNAME} ({$USERNAME})!
        	{/if} 
        	|
        	<a href="{$INDEX_LINK}" target="content">{translate key="menu_index"}</a> |
        	<a href="{$WEB_LINK}" target="_blank">{translate key="menu_website_index"}</a> |
            <a target="_parent" href="{$LOGOUT}">{translate key="logout"}</a></span>
    	</span>

        <span class="sitename">{sitename}</span>
		        
		<div id="info">
        	<span id="message">
        		bigace CMS&copy; (<span title="bigace Version: {bigace_version build="true" full="true"}">Version: {bigace_version}</span>)
        	</span>
        	
        	<span id="menu">
                <form name="search" method="post" target="content" action="{$SEARCH_URL}" onsubmit="return callSearch(this.elements['query'].value);">
                <span class="buttondelim"><input type="text" id="query" name="query" value="" /><input type="submit" value="{translate key="search"}" class="stdButton"/></span>
                </form>
                <span class="buttondelim"><a href="{$MANUAL}" target="_blank" class="menulinks">{translate key="manual"}</a></span>
                <span class="buttondelim"><a href="{$FORUM}" target="_blank" class="menulinks">{translate key="forum"}</a></span>
                <span class="buttondelim" id="languageSelectorSpan">
                    <select id="languageSelector" onChange="changeLanguage(this.options[this.options.selectedIndex].value);" class="std">
                    	{foreach from=$LANGUAGES item=lang}
                    	<option value="{$lang.link}" {$lang.selected}>{$lang.name}</option>
                        {/foreach}
                    </select>
            	</span>
                <span class="buttondelim"><a href="{$ABOUT}" target="content" title="{translate key="menu_about"}"><img src="{$STYLE_DIR}about.png" alt="{translate key="menu_about"}" /></a></span>
            </span>
        
        	<span class="clearUp" />
        </div>
    </body>
</html>
