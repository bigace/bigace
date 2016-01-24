<!-- $Id$ -->

<table id="permTable" class="tablesorter" cellspacing="0">
<col width="250" />
<col />
<thead>
	<tr>
		<th>{translate key="group"}</th>
		<th align="center">{translate key="rights}</th>
	</tr>
</thead>
<tbody>
    {foreach from=$ALL_PERMS item="curPerm"}
    <tr>
        <td>
            <img src="{$STYLE_DIR}user_group.png" border="0" valign="absmiddle" alt="{translate key="group"}">
            {$curPerm.GROUP_NAME}
        </td>
        <td align="center">

            {if $curPerm.IS_NEW}

                <form method="post" action="{$curPerm.CREATE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                    <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                    <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                    <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                    <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_READ}">
                    <button type="submit" class="{$curPerm.BUTTON_STYLE_READ}" title="{translate key="read"}">R</button>
                </form> 

                {if $USER_PERM->canWrite()}
                    <form method="post" action="{$curPerm.CREATE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                        <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                        <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                        <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                        <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_WRITE}">
                        <button type="submit" class="{$curPerm.BUTTON_STYLE_WRITE}" title="{translate key="write"}">W</button>
                    </form> 
                {else}
                    <button onClick="alert('{translate key="no_change_own_right"}');return false;" 
                        class="{$curPerm.BUTTON_STYLE_WRITE}" title="{translate key="write"}">W</button>
                {/if}

                {if $USER_PERM->canDelete()}
                    <form method="post" action="{$curPerm.CREATE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                        <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                        <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                        <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                        <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_DELETE}">
                        <button type="submit" class="{$curPerm.BUTTON_STYLE_DELETE}" title="{translate key="delete"}">D</button>
                    </form>
                {else}
                    <button onClick="alert('{translate key="no_change_own_right"}');return false;" 
                        class="{$curPerm.BUTTON_STYLE_DELETE}" title="{translate key="delete"}">D</button>
                {/if}

            {else}

                <form method="post" action="{$curPerm.DELETE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                    <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                    <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                    <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                    <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_READ}">
                    <button type="submit" class="{$curPerm.BUTTON_STYLE_READ}" title="{translate key="read"}">R</button>
                </form> 

                {if $USER_PERM->canWrite()}
                    <form method="post" action="{$curPerm.CHANGE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                        <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                        <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                        <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                        <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_WRITE}">
                        <button type="submit" class="{$curPerm.BUTTON_STYLE_WRITE}" title="{translate key="write"}">W</button>
                    </form> 
                {else}
                    <button onClick="alert('{translate key="no_change_own_right"}');return false;" 
                        class="{$curPerm.BUTTON_STYLE_WRITE}" title="{translate key="write"}">W</button>
                {/if}

                {if $USER_PERM->canDelete()}
                    <form method="post" action="{$curPerm.CHANGE_RIGHT_URL}" style="display:inline;padding:0px;margin:0px">
                        <input type="hidden" name="data[id]" value="{$curPerm.ITEM_ID}">
                        <input type="hidden" name="data[langid]" value="{$curPerm.LANGUAGE_ID}">
                        <input type="hidden" name="data[group]" value="{$curPerm.GROUP_ID}">
                        <input type="hidden" name="data[rights]" value="{$curPerm.RIGHT_VALUE_DELETE}">
                        <button type="submit" class="{$curPerm.BUTTON_STYLE_DELETE}" title="{translate key="delete"}">D</button>
                    </form>
                {else}
                    <button onClick="alert('{translate key="no_change_own_right"}');return false;" 
                        class="{$curPerm.BUTTON_STYLE_DELETE}" title="{translate key="delete"}">D</button>
                {/if}

            {/if}
        </td>
    </tr>
    {/foreach}

</tbody>
</table>

<script type="text/javascript">
{literal}
$(document).ready( function() { 
		$("#permTable").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 1: {sorter: false}, 2: {sorter: false} } }); 
	} 
);

function togglePerm(grpID, myId, newPerm) {
    if($('#'+myId+grpID).attr('class') == "permoff")
        $('#'+myId+grpID).attr('class', 'permon')
    else
        $('#'+myId+grpID).attr('class', 'permon')

    $("#newPermVal"+grpID).val(newPerm);
}
{/literal}
</script>

