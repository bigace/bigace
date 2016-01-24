{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<script language="javascript">
    <!--
    function {$CHOOSE_ID_JS}(userfunc, inputid) 
    {literal}{{/literal}
        {$MENU_CHOOSER_JS} = userfunc;
        fenster = open({$MENU_CHOOSER_LINK},"SelectParent","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
        bBreite=screen.width;
        bHoehe=screen.height;
        fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
    {literal}}{/literal}

    function setMenu(id, language, name) 
    {literal}{{/literal}
           $("#newParentID").val(id);
           $("#newParentName").val(name);
    {literal}}{/literal}
// -->
</script>

{load_translation name="upload"}

<form action="" method="post">

<table border="0" cellpadding="5" width="100%">
<tr><td width="500" valign="top">
    <table class="tablesorter" cellspacing="0" style="margin-top:0px">
    <colgroup>
        <col width="30" />
        <col />
    </colgroup>
    <thead>
	    <tr>
	        <th>&nbsp;</th>
		    <th>{translate key="name"}</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$ITEMS item="item"}
        <tr>
	        <td><input type="checkbox" name="data[ids][]" value="{$item->getID()}" checked="checked"></td>
	        <td>{$item->getName()}</td>
        </tr> 
    {/foreach}
    </tbody>
    </table>
</td>
<td valign="top">
    <fieldset>
        <input type="radio" name="mode" value="{$MODE_UPDATE_MULTIPLE}" id="updateMultiple" checked="checked">
        <label for="updateMultiple">Reload</label>
    </fieldset>
    <br />

    <fieldset>
        <div>
        <input type="radio" name="mode" value="{$MODE_SET_GROUP_PERM}" id="setGroupPerm">
        <label for="setGroupPerm">Set group permission</label>
        {$GROUP_SELECT} {$PERMISSION_SELECT}
        </div>
        <div>
        <input type="radio" name="mode" value="{$MODE_DELETE_MULTIPLE}" id="deleteMultiple">
        <label for="deleteMultiple">{translate key="delete"}</label>
        </div>
        <div>
        <input type="radio" name="mode" value="{$MODE_PARENT_MULTIPLE}" id="parentMultiple">
        <label for="parentMultiple">{translate key="save_below"}</label>
        <input style="border: 1px solid rgb(0, 0, 0); width: 100px; margin-right: 5px;" id="newParentName" name="parentName" value="" disabled="disabled" type="text">
        <input style="border: 1px solid rgb(0, 0, 0); width: 50px;" id="newParentID" name="parentID" value="" type="text">
        <button onclick="chooseMenuID('setMenu', 'newParentID'); return false;">Choose</button>

        </div>
    </fieldset>
    <br />

    <input type="submit" value="Update multiple">

</td></tr>
</table>


</form>
            
{literal}
<script type="text/javascript">
$(document).ready( function() { 
		$(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false} } }); 
	} 
);
</script>
{/literal}