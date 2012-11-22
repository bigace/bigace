{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst *}
<!-- $Id$ -->
{news_item id=$NEWS_ID assign="newsItem"}

<form action="" method="POST">
<input type="hidden" name="mode" value="remove">
<input type="hidden" name="newsID" value="{$NEWS_ID}">
<input type="hidden" name="newsLangID" value="{$NEWS_LANGUAGE}">

<table class="tablesorter" cellspacing="0">
<col width="150px" />
<col />
<thead>
	<tr>
	    <th colspan="5">{translate key="news_delete_question"}</th>
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