{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}
	
<div id="newsHeader" style="width:100%;margin-bottom:10px;">
	<table border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td>
			    <form action="{$LISTING_URL}" method="post"><button type="submit">
				    {translate key="news_header_listing"}
			    </button></form>
            </td>
			{if $PERM_CREATE}
			<td>
			    <form action="{$CREATE_URL}" method="post"><button type="submit">
				    {translate key="news_header_create"}
			    </button></form>
			</td>
			{/if}
			{*if $PERM_CATEGORIES}
			<td{if $MODE == 'categories'} class="row1"{/if}>
				<a href="{$CATEGORIES_URL}"><img src="{directory name="plugins"}news/news_categories.png" border="0" style="padding-right:6px;">{translate key="news_header_categories"}</a>
			</td>
			{/if*}
			{if $PERM_CONFIG}
			<td>
			    <form action="{$CONFIG_URL}" method="get"><button type="submit">
				    {translate key="news_header_config"}
			    </button></form>
			</td>
			{/if}
		</tr>
	</table>
</div>