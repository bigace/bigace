{*
    $Id$ 
*}

{$BACK_LINK}

<div id="userSettingsTab">

	<ul>
		<li><a href="#tabPageUserSetting"><span>{translate key="tab_settings"}</span></a></li>
		<li><a href="#tabPageUserData"><span>{translate key="tab_userdata"}</span></a></li>
    	{if $USER_PASSWORD_FORM != ""}
		<li><a href="#tabPageUserPassword"><span>{translate key="tab_password"}</span></a></li>
		{/if}
    	{if $USER_GROUP_FORM != ""}
		<li><a href="#tabPageUserGroups"><span>{translate key="tab_groups"}</span></a></li>
		{/if}
		{if $USER_DELETE_FORM != ""}
		<li><a href="#tabPageUserDelete"><span>{translate key="tab_delete"}</span></a></li>
		{/if}
	</ul>

	<div id="tabPageUserSetting">
        {$USER_SETTINGS_FORM}
	</div>

	<div id="tabPageUserData">
        {$USER_DATA_FORM}
	</div>

	{if $USER_PASSWORD_FORM != ""}
	<div id="tabPageUserPassword">
        {$USER_PASSWORD_FORM}
	</div>
	{/if}

	{if $USER_GROUP_FORM != ""}
	<div id="tabPageUserGroups">
        {$USER_GROUP_FORM}
	</div>
	{/if}

	{if $USER_DELETE_FORM != ""}
	<div id="tabPageUserDelete">
        {$USER_DELETE_FORM}
	</div>
	{/if}

</div>

{literal}
<script type="text/javascript">
<!--
  $(document).ready(function(){
        $("#userSettingsTab").tabs();
        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false}, 1: {sorter: false} } }); 
  });
// -->
</script>
{/literal}

