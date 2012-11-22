{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst *}
<!-- $Id$ -->

<form action="" method="POST">
<input type="hidden" name="mode" value="update">
<input type="hidden" name="commentID" value="{$COMMENT.id}">

<table class="tablesorter" cellspacing="0">
<col width="150px" />
<col />
<thead>
	<tr>
	    <th colspan="2">{translate key="comment_action_edit"}</th>
    </tr>
</thead>
<tbody>
	<tr class="row1">
	    <td>{translate key="comment_name"}</td>
	    <td><input type="text" name="name" style="width:350px;" value="{$COMMENT.name|text_input}"></td>
    </tr>
	<tr>
		<td>{translate key="comment_email"}</td>
	    <td><input type="text" name="email" style="width:350px;" value="{$COMMENT.email|text_input}"></td>
	</tr>
	<tr class="row1">
	    <td>{translate key="comment_homepage"}</td>
	    <td><input type="text" name="homepage" style="width:350px;" value="{$COMMENT.homepage|text_input}"></td>
    </tr>
	<tr>
	    <td>{translate key="comment_text"}</td>
	    <td>
	    		<textarea name="comment" style="height:300px;width:100%">{$COMMENT.comment|htmlspecialchars}</textarea>
	    </td>
    </tr>
	<tr class="row1">
	    <td colspan="2"><button type="submit">{translate key="comment_button_save"}</button></td>
    </tr>
</tbody>
</table>

</form>
