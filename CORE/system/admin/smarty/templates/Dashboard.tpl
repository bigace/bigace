{* $Id$ *}

<script type="text/javascript">
<!--{literal}
	function changeLayout() {
		document.forms.layoutSelect.submit();
	}

    function tooltip(msg) {
        overlib(msg, VAUTO, WIDTH, 250);
    }
{/literal}
{if isset($FEEDS) > 0}
	$(document).ready(function() {literal}{{/literal}
	
	{if count($FEEDS) > 0}
	  {foreach name="feedloop" from=$FEEDS item="feedEntry"}
	     {if isset($feedEntry.ajax) && strlen($feedEntry.ajax) > 0}
	         $.get("{$feedEntry.ajax}", function(data){literal}{{/literal}
	          $('#rss{$smarty.foreach.feedloop.index}').html(data);
	{literal}}{/literal});
	     {/if}
	  {/foreach}
	{/if}
	
	{literal}
	  });
	{/literal}
{/if}

// -->
</script>

    {if count($error) > 0}
	    {foreach from=$error key="a" item="msg"}
	    <h3 class="error">{$msg}</h3>
	    {/foreach}
    {/if}

    {if count($error) > 0}
	    {foreach from=$tips key="a" item="msg"}
	    <h3 class="info">{$msg}</h3>
	    {/foreach}
    {/if}

    <div class="menuEntries"> 
    <table border="0">
    {foreach from=$MENUS item="menu" name="menuLoop"}
        {if $smarty.foreach.menuLoop.index % 3 == 0}<tr>{/if}
        <td valign="top">
        <div class="indexEntry">
	        <a href="{$menu.url}"></a>
	        <h1><img src="{$STYLE_DIR}menu/{$menu.id}.png" border="0"> {$menu.name}</h1>
	        <br/>
	        {$menu.desc}
	        <br/>
	        <ul>
	        {foreach from=$menu.child item="child"}
		        <li><a href="{$child.url}" onmouseover="tooltip('{$child.desc}')" onMouseOut="nd();">{$child.name}</a></li>
	        {/foreach}
	        </ul>
        </div>
        </td>
        {if $smarty.foreach.menuLoop.index % 3 == 2}</tr>{/if}
    {/foreach}
    </table>
    </div>
    
    <hr class="clearer" />

    {if isset($FEEDS) && count($FEEDS) > 0}
        {admin_box_support_header}
          <div id="rssItems">
          <table width="100%">
            {foreach name="feedloop" from=$FEEDS item="feedEntry"}
            {if $smarty.foreach.feedloop.index % 2 == 0}<tr>{/if}
            <td width="50%">
                {admin_box_header title=$feedEntry.name toggle=false}
                    <div id="rss{$smarty.foreach.feedloop.index}">{$feedEntry.html}</div>
                {admin_box_footer}
            </td>
            {if $smarty.foreach.feedloop.index % 2 == 1}</tr>{/if}
            {/foreach}
          </table>
          </div>
        {admin_box_support_footer}
    {/if}


    {if count($STYLES) > 1}
	    <form name="layoutSelect" action="{$FORM_ACTION}" method="POST" target="_top">
        <b>{translate key="system_info"}</b>
	    <table align="center" cellpadding="3" class="infoBox">
        <tr>
            <td><b>{translate key="user"}:</b> {$FIRSTNAME} {$LASTNAME} ({$USERNAME})</td>
            <td><b>Domain:</b> {$HOST} (CID: {$CID})</td>
            <td><b>Admin Style:</b>
            	{$STYLE_SELECT}
	            <noscript>
	            <button type="submit">{translate key="change_style"}</button>
	            </noscript>
            </td>
        </tr>
        </table>
        </form>
    {/if}
