{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<div id="searchResults">

{if isset($RESULT_MENU)}
	<h2><img src="{$STYLE_DIR}item_1.png"> {translate key="item_1"}</h2>
	{if count($RESULT_MENU) > 0}
		<table class="tablesorter" cellspacing="0">
			<col width="80" />
			<col />
			<col width="40" />
			<col width="150" />
			<thead>
				<tr>
					<th align="center">{translate key="language"}</th>
					<th>{translate key="name"}</th>
					<th>{translate key="id"}</th>
					<th>{translate key="action"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$RESULT_MENU item="result"}
				<tr>
					<td align="center"><img src="{$STYLE_DIR}languages/{$result.language}.gif"></td>
					<td><a href="{$result.url}" class="edit">{$result.name}</a> <a href="#" onclick="$('#res1_{$result.id}_{$result.language}').toggle();return false;">[+]</a>
						<div id="res1_{$result.id}_{$result.language}" style="display:none;">
						<br/><a href="{$result.preview}">{$result.preview}</a>
						<br/>{$result.description}
						<br /><i>{$result.content}</i></div>
					</td>
					<td>{$result.id}</td>
					<td><a href="{$result.preview}" class="preview"><span>{translate key="preview"}</span></a></td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<b>{translate key="msg_no_result"}</b>
	{/if}
{/if}

{if isset($RESULT_IMAGE)}
	<h2><img src="{$STYLE_DIR}item_4.png"> {translate key="item_4"}</h2>
	{if count($RESULT_IMAGE) > 0}
		<table class="tablesorter" cellspacing="0">
			<col width="80" />
			<col />
			<col width="40" />
			<col width="150" />
			<thead>
				<tr>
					<th align="center">{translate key="language"}</th>
					<th>{translate key="name"}</th>
					<th>{translate key="id"}</th>
					<th>{translate key="action"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$RESULT_IMAGE item="result"}
				<tr>
					<td align="center"><img src="{$STYLE_DIR}languages/{$result.language}.gif"></td>
					<td><a href="{$result.url}" class="edit">{$result.name}</a></td>
					<td>{$result.id}</td>
					<td><a href="{$result.preview}" class="preview"><span>{translate key="preview"}</span></a></td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<b>{translate key="msg_no_result"}</b>
	{/if}
{/if}

{if isset($RESULT_FILE)}
	<h2><img src="{$STYLE_DIR}item_5.png"> {translate key="item_5"}</h2>
	{if count($RESULT_FILE) > 0}
		<table class="tablesorter" cellspacing="0">
			<col width="80" />
			<col />
			<col width="40" />
			<col width="150" />
			<thead>
				<tr>
					<th align="center">{translate key="language"}</th>
					<th>{translate key="name"}</th>
					<th>{translate key="id"}</th>
					<th>{translate key="action"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$RESULT_FILE item="result"}
				<tr>
					<td align="center"><img src="{$STYLE_DIR}languages/{$result.language}.gif"></td>
					<td><a href="{$result.url}" class="edit">{$result.name}</a></td>
					<td>{$result.id}</td>
					<td><a href="{$result.preview}" class="preview"><span>{translate key="preview"}</span></a></td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<b>{translate key="msg_no_result"}</b>
	{/if}
{/if}

{if isset($RESULT_USER)}
	<h2><img src="{$STYLE_DIR}user.png"> {translate key="user"}</h2>
	{if count($RESULT_USER) > 0}
		<table class="tablesorter" cellspacing="0">
			<col width="80" />
			<col />
			<thead>
				<tr>
					<th align="center">{translate key="language"}</th>
					<th>{translate key="name"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$RESULT_USER item="result"}
				<tr>
					<td align="center"><img src="{$STYLE_DIR}languages/{$result.language}.gif"></td>
					{if isset($result.url)}
					<td><a href="{$result.url}" class="edit">{$result.name}</a></td>
					{else}
					<td>{$result.name}</td>
					{/if}
				</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<b>{translate key="msg_no_result"}</b>
	{/if}
{/if}

{literal}
<script type="text/javascript">
$(document).ready( function() { 
		$(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 3: {sorter: false} } }); 
	} 
);
</script>
{/literal}
