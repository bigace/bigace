{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{admin_box_support_header}

    <form name="MenuValues" action="{$FORM_ACTION}" method="POST">
    <input type="hidden" name="mode" value="{$FORM_MODE}">
    <input type="hidden" name="data[id]" value="{$item->getID()}">
    <input type="hidden" name="data[langid]" value="{$item->getLanguageID()}">
    <input type="hidden" name="data[parentid]" value="{$item->getParentID()}">
    {if !$supportUniqueName}
    <input type="hidden" name="data[unique_name]" value="{$item->getUniqueName()|text_input}">
    {/if}


    <div class="stuffbox">
        <h3>{translate key="edit"} {translate key="edit_id"}: {$item->getID()} {translate key="edit_in"}
            <img alt="{$tempLanguage->getName()}" src="{$STYLE_DIR}languages/{$tempLanguage->getLocale()}.gif"> {$tempLanguage->getName()}</h3>
        <div class="inside">
            <table border="0" cellspacing="0">
            <col width="170"/>
            <col />
            <tr>
                <td><label for="title">{translate key="name"}</label> </td>
                <td><input type="text" id="title" name="data[name]" size="35" maxlength="250" tabindex="1" value="{$item->getName()|text_input}"></td>
            </tr>
        {if $supportUniqueName}
            <tr>
                <td><label for="unique">{translate key="unique_name"}</label></td>
                <td><input type="text" id="unique" name="data[unique_name]" size="35" maxlength="250" value="{$item->getUniqueName()|text_input}"></td>
            </tr>
        {/if}
            <tr>
                <td><label for="catchwords">{translate key="catchwords"}</label></td>
                <td><input type="text" id="catchwords" name="data[catchwords]" size="35" maxlength="200" value="{$item->getCatchwords()|text_input}"></td>
                <td><label for="recursive">{translate key="recursive"}</label></td>
				<td><input type="checkbox" id="recursive_catchwords" name="data[recursive_catchwords]" value="1"> 
            </tr>
            <tr>
                <td><label for="description">{translate key="description"}</label></td>
                <td><textarea id="description" name="data[description]" rows="5" cols="40">{$item->getDescription()|text_input}</textarea></td>
                <td><label for="recursive">{translate key="recursive"}</label></td>
				<td><input type="checkbox" id="recursive_description" name="data[recursive_description]" value="1"> 
            </tr>
            </table>
            <button type="submit">{translate key="save"}</button>
        </div>
    </div>
    
                
    {translate key="properties" assign="propHead"}
    {admin_box_header title=$propHead}
        <table border="0" cellspacing="0">
        <col width="170"/>
        <col />
            <tr>
                <td>{translate key="layout"}</td>
                <td>{$LAYOUT_SELECT}</td>
                <td><label for="recursive">{translate key="recursive"}</label></td>
				<td><input type="checkbox" id="recursive_layout" name="data[recursive_layout]" value="1"> 
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
                <td>{$hiddenOrShown}<br/><i>{translate key="hidden_description"}</i></td>
            </tr>
        </table>
    {admin_box_footer}


    {if isset($META_VALUES) && is_array($META_VALUES) && count($META_VALUES) > 0}
        {foreach from=$META_VALUES key="prjTitle" item="prjItem"}
            {admin_box_header title=$prjTitle}
                {$prjItem}
            {admin_box_footer}
        {/foreach}
    {/if}

    </form>

    {translate key="page_metadata" assign="metaHeader"}
    {admin_box_header title=$metaHeader}
        {* <p>{translate key="url"}: <a href="{link item=$item}">{link item=$item}</a></p> *}
        <p>{translate key="created"}: {$item->getCreateDate()|date_format:"%Y-%m-%d %H:%M:%S"} {translate key="by"} {user id=$item->getCreateByID()}</p>
        <p>{translate key="last_edited"}: {$item->getLastDate()|date_format:"%Y-%m-%d %H:%M:%S"} {translate key="by"} {user id=$item->getLastByID()}</p>
        <p><a onClick="popup('{$METADATA_URL}','Metadata',500,400);return false;" href="{$METADATA_URL}" target="_blank">{translate key="more_metadata"}</a></p>
    {admin_box_footer}

{admin_box_support_footer}