{* $Id$ *}
<link rel="stylesheet" type="text/css" href="{$MARKITUP_DIR}skins/bigace/style.css" />
<link rel="stylesheet" type="text/css" href="{$MARKITUP_DIR}sets/smarty/style.css" />

<script src="{$MARKITUP_DIR}jquery.markitup.js" type="text/javascript"></script>
<script src="{$MARKITUP_DIR}sets/smarty/set.js" type="text/javascript"></script>

<script type="text/javascript">
{literal}
$(document).ready(function()	{
	$('#modEdit').markItUp(mySettings);
});

function cancelBack() {
    location.href='{$CANCEL_URL}';
    return false;
}
{/literal}
</script>

<form action="{link_admin id=$MENU->getID() csrf=true}" method="post">
<input type="hidden" name="modulID" value="{$module->getID()}">
<input type="hidden" name="modeModul" value="save">

<table border="0" width="{$LARGE_FORM}" cellspacing="5">
<tr>
    <td>
        {translate key="name"}: <b>{$module->getName()}</b>
    </td>
</tr>
<tr>
    <td><a name="editor"></a>
		<textarea name="content" id="modEdit" style="width:{$LARGE_FORM};height:300px">{$MODULE_CONTENT}</textarea>
    </td>
</tr>
<tr>
    <td>
        <button type="submit" class="save">{translate key="save"}</button>
        <button class="cancel" onclick="return cancelBack();">{translate key="cancel"}</button>
    </td>
</tr>
</table>

</form>
