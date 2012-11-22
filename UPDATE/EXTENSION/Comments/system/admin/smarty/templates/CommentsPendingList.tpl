{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}

{if count($COMMENTS) == 0}
     <div><b>{translate key="error_no_comments"}</b></div>
{else}
     <table class="tablesorter" cellspacing="0">
     <col width="150px" />
     <col width="120px" />
     <col width="180px" />
     <col width="110px" />
     <col />
     <col />
    {if $PERM_ACTIVATE}
     <col />
    {/if}
    {if $PERM_DELETE}
     <col />
    {/if}
     <thead>
	    <tr>
	        <th>{translate key="comment_name"}</th>
	        <th align="center">{translate key="comment_homepage"}</th>
		    <th>{translate key="comment_email"}</th>
		    <th>{translate key="comment_date"}</th>
		    <th align="center">{translate key="comment_anonymous"}</th>
		    <th>{translate key="comment_text"}</th>
        	{if $PERM_ACTIVATE}
        	<th align="center">{translate key="comment_action_activate"}</th>
        	{/if}
        	{if $PERM_DELETE}
        	<th align="center">{translate key="comment_actions"}</th>
        	{/if}
        </tr>
     </thead>
     <tbody>
      {foreach from=$COMMENTS item="comment"}
        <tr class="{cycle name="css" values="row1,row2"}">
	        <td valign="top">
	        	{if $PERM_EDIT}
	        	<form action="" method="post">
				    <input type="hidden" name="mode" value="edit">
				    <input type="hidden" name="commentID" value="{$comment.id}">
				    <button class="edit links" type="submit" title="{translate key="comment_action_edit"}">{$comment.name}</button>
			    </form>
	        	{else}
                    {$comment.name}
	        	{/if}
            </td>
	        <td align="center" valign="top">{if $comment.homepage != ''}<a href="{$comment.homepage}" target="_blank"><img src="{directory name="plugins"}comments/house.png" border="0"></a>{/if}</td>
	        <td valign="top">{$comment.email}</td>
	        <td valign="top">{$comment.timestamp|date_format:"%m.%d.%y %H:%M"}</td>
	        <td align="center" valign="top">{if $comment.anonymous}<b>{$comment.ip}</b>{else}<img src="{$STYLE_DIR}user.png" border="0" alt="{$comment.ip}" title="{$comment.ip}">{/if}</td>
	        <td>{$comment.comment|nl2br}</td>
        	{if $PERM_ACTIVATE}
	        <td align="center" valign="top">
			    <form action="" method="post">
				    <input type="hidden" name="mode" value="activate">
				    <input type="hidden" name="commentID" value="{$comment.id}">
				    <input type="image" src="{directory name="plugins"}comments/comment_add.png" title="{translate key="comment_action_activate"}">
			    </form>
            </td>
        	{/if}
        	{if $PERM_DELETE}
	        <td align="center" valign="top">
			    <form action="" method="post">
				    <input type="hidden" name="mode" value="remove">
				    <input type="hidden" name="commentID" value="{$comment.id}">
				    <input type="image" src="{directory name="plugins"}comments/comment_delete.png" title="{translate key="comment_action_delete"}">
			    </form>
			    <form action="" method="post">
				    <input type="hidden" name="mode" value="spam">
				    <input type="hidden" name="commentID" value="{$comment.id}">
				    <input type="image" src="{directory name="plugins"}comments/spam_delete.png" title="{translate key="comment_spam_delete"}">
			    </form>
	        </td>
        	{/if}
        </tr>
      {/foreach}
     </tbody>
     </table>
{/if}
