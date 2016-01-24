{* $Id$ *}

<script type="text/javascript">
<!--

    function checkFormular(mform)
    {literal}{{/literal}
    /*
    	// no check, cause there is no default way of handling wrong data
        if(mform.userName.value == '')
    	{literal}{{/literal}
            mform.userName.focus();
            showJSError('{translate key="error"}', '{translate key="create_user_missing_values"}');
            return false;
    	{literal}}{/literal}
        return comparePasswords(mform);
	*/
		return true;
   	{literal}}{/literal}

    function comparePasswords(mform) 
    {literal}{{/literal}
    	if(mform.passwordnew.value == '') 
    	{literal}{{/literal}
            showJSError('{translate key="error"}', '{translate key="create_user_missing_values"}');
    	    return false;
	   	{literal}}{/literal}
        
        if (mform.passwordnew.value != mform.passwordcheck.value) 
    	{literal}{{/literal}
            showJSError('{translate key="error"}', '{translate key="msg_pwd_no_match"}');
            mform.passwordcheck.value = "";
            mform.passwordcheck.focus();
    	    return false;
	   	{literal}}{/literal}
        return true;
   	{literal}}{/literal}

// -->
</script>

<form action="{$CREATE_URL}" method="post" onSubmit="return checkFormular(this);">
    <table cellspacing="1" cellpadding="4" width="600" class="tablesorter">
    <colgroup>
        <col width="200px" />
        <col />
    </colgroup>
    <thead>
	  <tr>
	    <th colspan="2" align="left"><img src="{$STYLE_DIR}user_add.png"> {translate key="create_user_title"}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td align="left">{translate key="create_user_name"}</td>
        <td align="left"><input type="text" name="userName" id="name" maxlength="200" size="35" value="{$USERNAME}"></td>
      </tr>
      <tr>
        <td align="left">{translate key="create_user_language"}</td>
        <td align="left">
        	<select name="language">
        	{foreach from=$LANGUAGES key="langName" item="langID"}
        		<option value="{$langID}">{$langName}</option>
        	{/foreach}
        	</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">{translate key="create_user_group"}</td>
        <td align="left">{$GROUPS}</td>
      </tr>
      <tr>
        <td align="left">{translate key="user_state"}</td>
        <td align="left">
			<img src="{$STYLE_DIR}user_active.png"> <input type="radio" name="state" value="1" checked id="stateYes"><label for="stateYes">{translate key="user_active"}</label><br>
			<img src="{$STYLE_DIR}user_inactive.png"> <input type="radio" name="state" value="0" id="stateNo"><label for="stateNo">{translate key="user_inactive"}</label>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">{translate key="email"}</td>
        <td align="left"><input type="text" name="email" maxlength="99" size="35" value="{$EMAIL|default:"@"}"></td>
      </tr>
      <tr>
        <td align="left" valign="top">{translate key="new_password"}</td>
        <td align="left"><input type="password" name="passwordnew" maxlength="30" size="35" value=""></td>
      </tr>
      <tr>
        <td align="left" valign="top">{translate key="rewrite_password"}</td>
        <td align="left"><input type="password" name="passwordcheck" maxlength="30" size="35" value=""></td>
      </tr>
      <tr>
        <td colspan="2">
    <button type="submit">{translate key="create_user_button"}</button>
        </td>
      </tr>
      </tbody>
    </table>
</form>
