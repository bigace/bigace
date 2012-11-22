<!-- $Id$ -->
{if isset($LANGUAGES)}
<select onchange="changeLanguageBySelect(this)" style="float:right">
    {foreach from=$LANGUAGES item="language"}
        <option value="{$language->getLocale()}"{if $language->getLocale() == $ITEM_LANGUAGE} selected{/if}>{$language->getName()}</option>
    {/foreach}
</select>
<h3>{translate key="choose_image"}</h3>
<div style="clear:both;height:4px">&nbsp;</div>
{/if}
<table class="tablesorter" cellspacing="0">
<col width="30" />
<col />
<col />
<thead>
    <tr>
        <th>&nbsp;</th>
        <th align="left">{translate key="name"}</th>
        <th align="left">{translate key="image_filename"}</th>
    </tr>
</thead>
<tbody>
    {foreach item="entry" from=$ITEMS}
    <tr>
        <td align="left"><a href="{$entry.ITEM_URL}" onclick="chooseItem('{$entry.ITEM_TYPE}', '{$entry.ITEM_ID}', '{$entry.ITEM_LANGUAGE}', '{$entry.ITEM_FILENAME}', '{$entry.ITEM_URL}', '{$entry.ITEM_MIMETYPE}'); return false;"><img src="{thumbnail item=$entry.ITEM height=40 width=40}" border="0"></a></td>
        <td align="left"><a href="{$entry.ITEM_URL}" onclick="chooseItem('{$entry.ITEM_TYPE}', '{$entry.ITEM_ID}', '{$entry.ITEM_LANGUAGE}', '{$entry.ITEM_FILENAME}', '{$entry.ITEM_URL}', '{$entry.ITEM_MIMETYPE}'); return false;">{$entry.ITEM_NAME}</a></td>
        <td align="left">{$entry.ITEM_FILENAME}</td>
    </tr>
    {/foreach}
    </tbody>
</table>

{literal}
<script type="text/javascript">
$(document).ready( function() { 
        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false} }}); 
    } 
);
</script>
{/literal}