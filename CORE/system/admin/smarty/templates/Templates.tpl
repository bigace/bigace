{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<div id="tabpanes" class="ui-tabs">

	<ul>
		<li><a href="#tabpage1"><span>{translate key="title_existing_template"}</span></a></li>
		<li><a href="#tabpage2"><span>{translate key="title_system_template"}</span></a></li>
		<li><a href="#tabpage3"><span>{translate key="create"}</span></a></li>
	</ul>

   <div id="tabpage1">
        <table class="tablesorter" id="tpls" cellspacing="0">
        <colgroup>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col width="130"/>
        </colgroup>
        <thead>
	        <tr>
		        <th>{translate key="name}</th>
		        <th>{translate key="description}</th>
		        <th align="center">{translate key="is_in_work"}</th>
		        <th align="center">{translate key="is_template"}</th>
		        <th align="center">{translate key="is_include"}</th>
		        <th align="center">{translate key="action"}</th>
	        </tr>
        </thead>
        <tbody>
            {foreach from=$templates item="tpl"}
            <tr>
                <td noWrap="noWrap">
                    <span style="float:left" onmouseover="tooltip('{$tpl.filename}')" onmouseout="nd();">{$tpl.name}</span>
                    <form action="" method="post" style="float:right">
                        <input type="hidden" name="mode" value="edit" />
                        {$CSRF_TOKEN}
                        <input type="hidden" name="template" value="{$tpl.name|urlencode}" />
                        <button class="edit" type="submit">{translate key="edit"}</button>
                    </form>
                </td>
                <td>{$tpl.description}</td>
                <td align="center">
                    <img src="{$STYLE_DIR}{if $tpl.inWork}inactive.png{else}active.png{/if}" />
                </td>
            {if $tpl.include}
                <td align="center"></td>
                <td align="center"><img src="{$STYLE_DIR}sign_yes.png"/></td>
            {else}
                <td align="center"><img src="{$STYLE_DIR}sign_yes.png"/></td>
                <td align="center"></td>
            {/if}
                <td align="center">
            {if $tpl.system}
                <img src="{$STYLE_DIR}sign_no.png" />
            {elseif $tpl.usage == 0}
                <form action="" onsubmit="return confirm('{translate key="ask_delete"}')" method="post">
                    <input type="hidden" name="template" value="{$tpl.name|urlencode}" />
                    <input type="hidden" name="mode" value="delete" />
                    {$CSRF_TOKEN}
                    <button class="delete" type="submit">{translate key="delete"}</button>
                </form>
            {else}
                <img src="{$STYLE_DIR}sign_no.png" />
            {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
        </table>
   </div>
   
   <div id="tabpage2" class="ui-tabs-hide">
        <table class="tablesorter" id="tpls" cellspacing="0">
        <colgroup>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col width="130"/>
        </colgroup>
        <thead>
	        <tr>
		        <th>{translate key="name}</th>
		        <th>{translate key="description}</th>
		        <th align="center">{translate key="is_in_work"}</th>
		        <th align="center">{translate key="is_template"}</th>
		        <th align="center">{translate key="is_include"}</th>
		        <th align="center">{translate key="action"}</th>
	        </tr>
        </thead>
        <tbody>
            {foreach from=$system item="tpl"}
            <tr>
                <td noWrap="noWrap">
                    <span style="float:left" onmouseover="tooltip('{$tpl.filename}')" onmouseout="nd();">{$tpl.name}</span>
                    <form action="" method="post" style="float:right">
                        <input type="hidden" name="mode" value="edit" />
                        <input type="hidden" name="template" value="{$tpl.name|urlencode}" />
                        {$CSRF_TOKEN}
                        <button class="edit" type="submit">{translate key="edit"}</button>
                    </form>
                </td>
                <td>{$tpl.description}</td>
                <td align="center">
                    <img src="{$STYLE_DIR}{if $tpl.inWork}inactive.png{else}active.png{/if}" />
                </td>
            {if $tpl.include}
                <td align="center"></td>
                <td align="center"><img src="{$STYLE_DIR}sign_yes.png"/></td>
            {else}
                <td align="center"><img src="{$STYLE_DIR}sign_yes.png"/></td>
                <td align="center"></td>
            {/if}
                <td align="center">
            {if $tpl.system}
                <img src="{$STYLE_DIR}sign_no.png" />
            {elseif $tpl.usage == 0}
                <form action="" onsubmit="return confirm('{translate key="ask_delete"}')" method="post">
                    <input type="hidden" name="template" value="{$tpl.name|urlencode}" />
                    <input type="hidden" name="mode" value="delete" />
                    {$CSRF_TOKEN}
                    <button class="delete" type="submit">{translate key="delete"}</button>
                </form>
            {else}
                <img src="{$STYLE_DIR}sign_no.png" />
            {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
        </table>
   </div>
   
   <div id="tabpage3" class="ui-tabs-hide">
        <b>{translate key="title_create_template"}</b><br/>
        <form action="" method="post">
        <input type="hidden" name="mode" value="new" />
        {$CSRF_TOKEN}
        <table class="tablesorter" cellspacing="0">
        <colgroup>
            <col width="300"/>
            <col />
            <col width="120"/>
            <col width="120"/>
            <col width="130"/>
        </colgroup>
        <thead>
	        <tr>
		        <th>{translate key="name}</th>
		        <th>{translate key="description}</th>
		        <th align="center">{translate key="is_template"}</th>
		        <th align="center">{translate key="is_include"}</th>
		        <th align="center">{translate key="action"}</th>
	        </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" name="templatename" class="longText" /></td>
                <td><input type="text" name="description" class="longText" /></td>
                <td align="center"><input type="radio" name="isinclude" value="0" /></td>
                <td align="center"><input type="radio" name="isinclude" value="1" checked="checked" /></td>
                <td align="center"><button type="submit" class="save">{translate key="create"}</button></td>
            </tr>
	        </tbody>
        </table>
        </form>

        <br />

        <b>{translate key="title_copy_template"}</b><br/>
        <form action="" method="post">
        <input type="hidden" name="mode" value="copy" />
        {$CSRF_TOKEN}
        <table class="tablesorter" cellspacing="0">
        <col width="300"/>
        <col />
        <col width="240"/>
        <col width="130"/>
        <thead>
	        <tr>
		        <th>{translate key="name"}</th>
		        <th>{translate key="description"}</th>
		        <th align="center">{translate key="copy_of"}</th>
		        <th align="center">{translate key="action"}</th>
	        </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center"><input type="text" name="templatename" class="longText" /></td>
                <td align="center"><input type="text" name="description" class="longText" /></td>
                <td align="center">{$tplcopy}</td>
                <td align="center"><button type="submit" class="save">{translate key="create"}</button></td>
            </tr>
	        </tbody>
        </table>
        </form>
   </div>
   
</div>


<script type="text/javascript">{literal}
$(document).ready( function() { 
        $("#tpls").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false}, 3: {sorter: false}, 4: {sorter: false}, 5: {sorter: false} } }); 
        $("#tabpanes").tabs();        
    } 
);{/literal}
</script>
