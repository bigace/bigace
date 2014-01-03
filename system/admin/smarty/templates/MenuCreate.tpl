{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{load_translation name="editor" locale=$LOCALE}
{literal}
<script type="text/javascript">
<!--
    function setMenu(id, language, tname) 
    {
        document.getElementById('parentid').value = id;
        document.getElementById('parentname').value = tname;
    }
        
    function checkCreateForm()
    {
return true;
        if (document.getElementById('parentid').value == '') {
{/literal}
            alert('{translate key="javascript_choose_parent"}');
{literal}
            return false;
        }
        if (document.getElementById('menuName').value == '') {
{/literal}
            alert('{translate key="javascript_choose_title"}');
{literal}
            return false;
        }
        return true;    
    }

//-->        
</script>
{/literal}

{admin_box_support_header}

<form name="MenuValues" onSubmit="return checkCreateForm();" action="{$FORM_ACTION}" method="POST">
<input type="hidden" name="mode" value="{$FORM_MODE}">
<input type="hidden" name="data[nextAdmin]" value="{$NEXT_ADMIN}">

    {admin_box_header title=$title toggle=false}
    <table border="0" cellspacing="0">
    <col width="170"/>
    <col />
        <tr>
	        <td>{translate key="language"}</td>
	        <td>{$NEW_LANGUAGE}</td>
        </tr>
        <tr>
	        <td>{translate key="submenu_of"}</td>
	        <td>{$NEW_SUBMENU}</td>
        </tr>
        <tr>
	        <td>{translate key="name"}</td>
	        <td><input type="text" id="menuName" name="data[name]" size="35" maxlength="250" value="{$NEW_NAME|text_input}"></td>
        </tr>
    {if $supportUniqueName}
        <tr>
	        <td>{translate key="unique_name"}</td>
	        <td><input type="text" name="data[unique_name]" size="35" maxlength="250" value="{$NEW_UNIQUE_NAME|text_input}"></td>
        </tr>
    {/if}
        <tr>
	        <td>{translate key="catchwords"}</td>
	        <td><input type="text" name="data[catchwords]" size="35" maxlength="200" value="{$NEW_CATCHWORDS|text_input}"></td>
        </tr>
        <tr>
	        <td>{translate key="description"}</td>
	        <td><textarea name="data[description]" rows="5" cols="40">{$NEW_DESCRIPTION|text_input}</textarea></td>
        </tr>
    </table>
	<button type="submit">{translate key="create"}</button>
    {admin_box_footer}

    {translate key="properties" assign="propHead"}
    {admin_box_header title=$propHead}
    <table border="0" cellspacing="0">
    <col width="170"/>
    <col />
        <tr>
            <td>{translate key="layout"}</td>
            <td>{$LAYOUT_SELECT}</td>
        </tr> 
        <tr>
            <td>{translate key="modul"}</td>
            <td>{$MODUL_SELECT}</td>
        </tr>
        <tr>
            <td>{translate key="menu_workflow"}</td>
            <td>{$WORKFLOW_SELECT}</td>
        </tr>
        <tr>
            <td valign="top">{translate key="display_state"}</td>
	        <td>{$NEW_STATE}<br/><i>{translate key="hidden_description"}</i></td>
        </tr>
    </table>
    {admin_box_footer}

    {translate key="content_page" assign="title"}
    {admin_box_header title=$title closed=true}
    	{if $NEW_EDITOR != ''}
    		{$NEW_EDITOR}
    	{else}
			<textarea id="content" name="data[content]" style="height:300px;width:100%">{$NEW_EDITOR|htmlspecialchars}</textarea>
    	{/if}
    {admin_box_footer}


    {if isset($META_VALUES) && is_array($META_VALUES) && count($META_VALUES) > 0}
        {foreach from=$META_VALUES key="prjTitle" item="prjItem"}
            {admin_box_header title=$prjTitle closed=true}
                {$prjItem}
            {admin_box_footer}
        {/foreach}
    {/if}

</form>

{admin_box_support_footer}