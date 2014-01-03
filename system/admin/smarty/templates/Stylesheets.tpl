{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<b>{translate key="title_existing_stylesheet"}</b><br/>
<table id="stylesTable" class="tablesorter" cellspacing="0">
<colgroup>
    <col width="300"/>
    <col />
    <col width="130"/>
</colgroup>
<thead>
	<tr>
		<th>{translate key="name"}</th>
		<th>{translate key="description"}</th>
		<th align="center">{translate key="action"}</th>
	</tr>
</thead>
<tbody>
    {foreach from=$stylesheets item="style"}
    <tr>
        <td valign="top" noWrap="noWrap">
            <span style="float:left">{$style.name}</span>
            <form action="{link_admin id=$MENU->getID()}" method="post" style="float:right">
                <input type="hidden" name="mode" value="edit" />
                {$CSRF_TOKEN}
                <input type="hidden" name="stylesheet" value="{$style.filename}" />
                <button class="edit" type="submit">{translate key="edit"}</button>
            </form>
        </td>
        <td>{$style.description}</td>
        <td align="center">
        {if $style.usage == 0}
            <form action="{link_admin id=$MENU->getID()}" onsubmit="return confirm('{translate key="ask_delete"}')" method="post">
                {$CSRF_TOKEN}
                <input type="hidden" name="stylesheet" value="{$style.filename}" />
                <input type="hidden" name="mode" value="delete" />
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

<script type="text/javascript">{literal}
$(document).ready( function() { 
		$("#stylesTable").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false} } }); 
	} 
);{/literal}
</script>

<br/> 

<b>{translate key="title_create_stylesheet"}</b><br/>
<form action="{link_admin id=$MENU->getID()}" method="post">
<input type="hidden" name="mode" value="new" />
{$CSRF_TOKEN}
<table class="tablesorter" cellspacing="0">
<colgroup>
    <col width="300"/>
    <col />
    <col width="240"/>
    <col width="130"/>
</colgroup>
<thead>
	<tr>
		<th>{translate key="name"}</th>
		<th>{translate key="description"}</th>
		<th align="center">{translate key="editorcss"}</th>
		<th align="center">{translate key="create"}</th>
	</tr>
</thead>
<tbody>
    <tr>
        <td align="center"><input type="text" name="stylesheet" class="longText" /></td>
        <td align="center"><input type="text" name="description" class="longText" /></td>
        <td align="center">{$editorcss}</td>
        <td align="center"><button type="submit" class="save">{translate key="save"}</button></td>
    </tr>
	</tbody>
</table>
</form>
