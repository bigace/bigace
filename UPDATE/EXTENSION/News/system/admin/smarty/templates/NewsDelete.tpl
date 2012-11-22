{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}

{news_item id=$NEWS_ID assign="newsItem"}

<form action="" method="POST">
<input type="hidden" name="mode" value="remove">
<input type="hidden" name="newsID" value="{$NEWS_ID}">
<input type="hidden" name="newsLangID" value="{$NEWS_LANGUAGE}">

<h3>{translate key="news_delete_question"}</h3>
<table class="tablesorter" cellspacing="0">
 <colgroup>
     <col width="150" />
     <col />
     <col width="150" />
     <col width="100" />
     <col width="220" />
</colgroup>     
<thead>
	<tr>
	    <th>{translate key="news_title"}</th>
		<th>{translate key="news_teaser"}</th>
		<th>{translate key="news_date"}</th>
		<th>{translate key="news_author"}</th>
    	<th align="center">{translate key="news_actions"}</th>
    </tr>
</thead>
<tbody>
	<tr>
	    <td>{$newsItem->getTitle()}</td>
		<td>{$newsItem->getTeaser()}</td>
	    <td>{$newsItem->getDate()|date_format:$DATE_FORMAT}</td>
	    <td>{user id=$newsItem->getCreateByID()}</td>
    	<td align="center"><input type="submit" value="{translate key="news_action_delete"}"></td>
    </tr>
</tbody>
</table>

</form>