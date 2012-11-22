<!-- $Id$ -->
<div style="clear:both;margin-bottom:5px">
    {translate key="wayhome"}: {$WAYHOME}
    {if isset($LANGUAGES)}
    <select onchange="changeLanguageBySelect(this)" style="float:right">
        {foreach from=$LANGUAGES item="language"}
            <option value="{$language->getLocale()}"{if $language->getLocale() == $ITEM_LANGUAGE} selected{/if}>{$language->getName()}</option>
        {/foreach}
    </select>
    <div style="clear:both"></div>
    {/if}
</div>
<table class="tablesorter" cellspacing="0">
<col width="30" />
<col />
<col />
<thead>
    <tr>
        <th>&nbsp;</th>
        <th align="left">{translate key="name"}</th>
    </tr>
</thead>
<tbody>
    {foreach item="entry" from=$ITEMS}
    <tr>
        <td width="20" align="left">{$entry.FOLDER}</td>
        <td align="left"><a href="{$entry.ITEM_URL}" onclick="chooseItem('{$entry.ITEM_TYPE}', '{$entry.ITEM_ID}', '{$entry.ITEM_LANGUAGE}', '{$entry.ITEM_MIMETYPE}', '{$entry.ITEM_URL}', '{$entry.ITEM_MIMETYPE}'); return false;">{$entry.ITEM_NAME}</a></td>
    </tr>
    {/foreach}
    </tbody>
</table>

{literal}
<script type="text/javascript">
$(document).ready( function() { 
        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 1: {sorter: false}, 2: {sorter: false} }}); 
    } 
);
</script>
{/literal}