{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}

<div id="newsHeader" style="width:100%;margin-bottom:10px;">
	<table border="0" cellpadding="10" cellspacing="0">
		<tr>
			<td>
			    <form action="{$LISTING_URL}" method="post"><button type="submit">{translate key="comments_header_listing"}</button></form>
			</td>
			{if $PERM_CONFIG}
			<td>
			    <form action="{$CONFIG_URL}" method="post"><button type="submit">{translate key="comments_header_config"}</button></form>
			</td>
			{/if}
		</tr>
	</table>
</div>