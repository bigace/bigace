{* $Id$ *}

<table class="tablesorter" cellspacing="0">
<colgroup>
	<col />
	<col />
	<col />
	<col />
</colgroup>
<thead>
	<tr>
		<th>{translate key="module_name"}</th>
		<th>{translate key="module_title"}</th>
		<th>{translate key="module_description"}</th>
		<th>{translate key="module_state"}</th>
	</tr>
</thead>
<tbody>
    {foreach item="module" from=$MODULES}
	{assign var="id" value=$module->getID()}
    <tr>
        <td><a href="{link_admin id=$MENU->getID() params="modulID=$id&modeModul=edit"}" class="edit">{$module->getName()}</a></td>
        <td>{$module->getTitle()}</td>
        <td>{$module->getDescription()}</td>
        <td align="center">
			{if $module->isActivated()}
				<a href="{link_admin id=$MENU->getID() params="modulID=$id&modeModul=state" csrf=true}" onmouseover="tooltip('{translate key="modul_active"}')" onMouseOut="nd();"><img src="{$STYLE_DIR}active.png" border="0"></a>
			{else}
				<a href="{link_admin id=$MENU->getID() params="modulID=$id&modeModul=state" csrf=true}" onmouseover="tooltip('{translate key="modul_inactive"}')`" onMouseOut="nd();"><img src="{$STYLE_DIR}inactive.png" border="0"></a>
			{/if}
        </td>
    </tr>
    {/foreach}
	</tbody>
</table>

<br/> 

<p>
<b>{translate key="title_create_module"}</b>
<br/>
{translate key="description_create_module"}
</p>
<br />
<form action="{link_admin id=$MENU->getID() params="modeModul=create" csrf=true}" method="POST">
<label for="newModName">{translate key="name"}:</label>
<input type="text" maxlength="25" id="newModName" name="name">
<input type="image" src="{$STYLE_DIR}save.png">
</form>

<script type="text/javascript">
{literal}
$(document).ready( function() { 
		$(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 3: {sorter: false} } }); 
	} 
);
{/literal}
</script>
		
