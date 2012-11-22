{* $Id$ *}
{if isset($WAYHOME)}
<div style="margin-bottom:5px">
    {translate key="wayhome"}: {$WAYHOME}
</div>
{else}
<h3>{translate key="category_menu"}</h3>
{/if}
<table class="tablesorter" cellspacing="0">
<col width="30" />
<col width="40%" />
<col />
  <thead>
    <tr>
        <th>&nbsp;</th>
        <th>{translate key="category_name"}</th>
        <th align="center">{translate key="cat_display"}</th>
    </tr>
  </thead>
  <tbody>
    {foreach item="entry" from=$entries}
    <tr class="{$entry.CSS}">
        <td>
            {if $entry.CATEGORY_PARENT_URL != ""}
            <a href="{$entry.CATEGORY_PARENT_URL}" title="{$entry.CATEGORY_NAME}">..</a>
            {/if}
            {if $entry.CATEGORY_CHILD_URL != ""}
            <a href="{$entry.CATEGORY_CHILD_URL}" title="{$entry.CATEGORY_NAME}"><img src="images/folder.png" border="0" alt="{$entry.CATEGORY_NAME}"></a>
            {/if}
        </td>
        <td>{$entry.CATEGORY_NAME}</td>
        <td align="center">{$entry.ACTION_LINKED}</td>
    </tr>
    {/foreach}  
  </tbody>
</table>