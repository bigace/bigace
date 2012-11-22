{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<div id="tabpanes">

    <ul>
        <li><a href="#tabpage1"><span>{translate key="tab_sections"}</span></a></li>
        {if $PERM_CREATE}
        <li><a href="#tabpage2"><span>{translate key="tab_create"}</span></a></li>
        {/if}
        {if isset($SECTION)}
        <li><a href="#tabpage3"><span>{translate key="tab_edit"}</span></a></li>
        {if $PERM_CREATE}
        <li><a href="#tabpage4"><span>{translate key="tab_add"}</span></a></li>
        {/if}
        {/if}
    </ul>

   <div id="tabpage1">
    {if count($SECTIONS) > 0}
        <table class="tablesorter sectionTable" cellspacing="0">
        <col />
        {if $FORM->has_section_tools()}
        <col width="100px"  />
        {/if}
        <col width="100px"  />
        {if $PERM_EDIT}
        <col width="100px" />
        {/if}
        <thead>
            <tr>
                <th>{translate key="name"}</th>
                <th align="center">{translate key="amount"}</th>
                {if $FORM->has_section_tools()}
                <th align="center">{translate key="action"}</th>
                {/if}
                {if $PERM_EDIT}
                <th>{translate key="delete"}</th>
                {/if}
            </tr>
        </thead>
        <tbody>
        {foreach from=$SECTIONS item="sec"}
            <tr>
                <td>
                    <form method="POST" action="{$ACTION_URL}">
                    <input type="hidden" name="mode" value="editSection">
                    <input type="hidden" name="section" value="{$sec.id}">
                    <button type="submit" class="edit links">{$sec.name}</button>
                    </form>
                </td>
                <td align="center">{$sec.amount}</td>
                {if $FORM->has_section_tools()}
                <td align="center">{$FORM->style_section_tools($sec)}</td>
                {/if}
                {if $PERM_EDIT}
                <td>
                    <form method="POST" action="{$ACTION_URL}" onSubmit="return confirm('{translate key="ask_delete_section"}')">
                    <input type="hidden" name="mode" value="deleteSection">
                    <input type="hidden" name="section" value="{$sec.id}">
                    <button type="submit" class="delete links">{translate key="delete"}</button>
                    </form>
                </td>
                {/if}
            </tr>
        {/foreach}
        </tbody>
        </table>
        <br />
    {else}
    {translate key="sections_empty"}
    {/if}
   </div>

    {* Create section form *}
    {if $PERM_CREATE}
    <div id="tabpage2">
      <form method="post" action="{$ACTION_URL}">
      <input type="hidden" name="mode" value="createSection">
        <p>{translate key="create_section_info"}</p>
        <br />
        {$INPUT_SECTION}
        <button type="submit">{translate key="create"}</button>
      </form>
    </div>
    {/if}

{if isset($SECTION)}
   
    <div id="tabpage3">
    <p>{translate key="edit_section_entries"} <b>{$SECTION.name}</b>.</p>
    {if isset($ENTRIES) && count($ENTRIES) > 0}
        <table class="tablesorter entryTable" cellspacing="0">
        <col />
        {if $PERM_EDIT}
        <col width="100px"  />
        {/if}
        <thead>
            <tr>
                <th>{translate key="generic_entry"}</th>
                {if $PERM_EDIT}
                <th>{translate key="delete"}</th>
                {/if}
            </tr>
        </thead>
        <tbody>
        {foreach item="entry" from=$ENTRIES}
        {cycle name="rowCss" values="even,odd" print=false assign="css"}        
            <tr class="{$css}">
                {if $PERM_EDIT}
                <td>
                    <form method="POST" action="{$ACTION_URL}">
                    <input type="hidden" name="mode" value="updateEntry">
                    <input type="hidden" name="section" value="{$SECTION.id}">
                    <input type="hidden" name="entry" value="{$entry.id}">
                    {$FORM->style_input_entry($entry.name, $entry.value, $SECTION.id)}
                    </form>
                </td>
                <td>
                    <form method="POST" action="{$ACTION_URL}">
                    <input type="hidden" name="mode" value="removeEntry">
                    <input type="hidden" name="section" value="{$SECTION.id}">
                    <input type="hidden" name="entry" value="{$entry.id}">
                    <button type="submit" class="delete links">{translate key="delete"}</button>
                    </form>
                </td>
                {else}
                <td>
                    <table border="0">
                    <tr><td style="background-color:transparent">{translate key="generic_name"}</td>
                    <td style="background-color:transparent">{$entry.name}</td></tr>
                    <tr><td style="background-color:transparent">{translate key="generic_value"}</td>
                    <td style="background-color:transparent">{$entry.value}</td></tr>
                    </table>
                </td>
                {/if}
            </tr>
        {/foreach}
        </tbody>
        </table>
        <br />
    {else}
    {translate key="no_entries"}
    {/if}
    </div>

    {if $PERM_CREATE}
    <div id="tabpage4">
      <form method="post" action="{$ACTION_URL}">
      <input type="hidden" name="mode" value="createEntry">
      <input type="hidden" name="section" value="{$SECTION.id}">
        <p>{translate key="create_entry_info"} <b>{$SECTION.name}</b></p>
        <br />
            {$INPUT_ENTRY}
            <button type="submit">{translate key="create"}</button>
      </form>
    </div>
    {/if}
{/if}

</div>

{literal}
<script type="text/javascript">
$(document).ready( function() { 
    $("#tabpanes").tabs();
    $(".sectionTable").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false} } }); 
{/literal}
    {if isset($SECTION)}
        {literal}
        $(".entryTable").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 1: {sorter: false} } }); 
        {/literal}
        {if $MODE == "editSection" && isset($SECTION)}
        $("#tabpanes").tabs("select", 2);
        {elseif $PERM_CREATE && $MODE == 'createEntry'}
        $("#tabpanes").tabs("select", 3);
        {elseif !$PERM_CREATE}
        $("#tabpanes").tabs("select", 1);
        {else}
        $("#tabpanes").tabs("select", 2);
        {/if}
    {/if}
{literal}
});
</script>
{/literal}
