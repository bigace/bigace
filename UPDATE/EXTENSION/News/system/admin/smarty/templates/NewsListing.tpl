{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}

{news assign="currentNews" hidden=true}
{if count($currentNews) == 0}
 <div><b>{translate key="error_nonews"}</b></div>
{else}
 <table class="tablesorter" cellspacing="0">
 <colgroup>
     <col />
     <col width="150" />
     <col width="100" />
     <col width="220" />
</colgroup>     
 <thead>
	<tr>
	    <th>{translate key="news_title"} &amp; {translate key="news_teaser"}</th>
		<th>{translate key="news_date"}</th>
		<th>{translate key="news_author"}</th>
    	<th align="center">{translate key="news_actions"}</th>
    </tr>
 </thead>
 <tbody>
  {foreach from=$currentNews item="newsPage"}
    <tr>
	    <td align="left">
			{if $PERM_EDIT}
			<form action="" id="newsEditForm-{$newsPage->getID()}" method="POST">
				<input type="hidden" name="mode" value="edit">
				<input type="hidden" name="newsID" value="{$newsPage->getID()}">
				<input type="hidden" name="newsLangID" value="{$newsPage->getLanguageID()}">
				<a href="{link item=$newsPage}" onclick="$('#newsEditForm-{$newsPage->getID()}').submit(); return false;" class="edit{if $newsPage->isHidden()}hidden{/if}" title="{translate key="edit"}">{$newsPage->getTitle()}</a>
			</form>
			{else}
				 {$newsPage->getTitle()}
			{/if}
			<br/>
			<span class="light">{$newsPage->getTeaser()}</span>
		</td>
	    <td>{$newsPage->getDate()|date_format:$DATE_FORMAT}</td>
	    <td>{user id=$newsPage->getCreateByID()}</td>
	    <td align="center">
			<form target="_blank" action="{link item=$newsPage}" method="get">
			<button class="preview" type="submit">{translate key="preview"}</button>
			</form>
	    	{if $PERM_DELETE}
			<form action="" method="POST" style="display:inline;">
				<input type="hidden" name="mode" value="delete">
				<input type="hidden" name="newsID" value="{$newsPage->getID()}">
				<input type="hidden" name="newsLangID" value="{$newsPage->getLanguageID()}">
				<button type="submit" class="delete">{translate key="news_action_delete"}</button>
			</form>
	    	{/if}
	    </td>
    </tr>
  {/foreach}
 </tbody>
 </table>
{/if}

<script type="text/javascript">
{literal}
$(document).ready( function() { 
        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 4: {sorter: false} }}); 
    } 
);
{/literal}
</script>