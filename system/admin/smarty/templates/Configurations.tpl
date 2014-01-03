{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{*

FIXME: style me as select box, open config box on select and jump to
<b>{translate key="jump_to_config"}:</b> 
{foreach from=$CONFIGURATIONS item=package}
<a href="#{$package.name}">{$package.name}</a>&nbsp;
{/foreach} 

TODO: remove translation
{translate key="config_package}

*}

{admin_box_support_header}

<script language="javascript">
    <!--
    function {$CHOOSE_ID_JS}(userfunc, inputid) 
    {literal}{{/literal}
        {$MENU_CHOOSER_JS} = userfunc;
        fenster = open({$MENU_CHOOSER_LINK},"SelectParent","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
        bBreite=screen.width;
        bHoehe=screen.height;
        fenster.moveTo((bBreite-400)/2,(bHoehe-350)/2);
    {literal}}{/literal}

    {literal}
    function submitConfigForm(name) 
    {
        if(document.getElementById(name) != null) {
            document.getElementById(name).submit();
        }
    }
    
    function toggleAll() {
        $(".postbox h3").parent().toggleClass("closed");
        if($(".postbox h3").parent().hasClass("closed"))
            $("#tg").text("{/literal}{translate key="show_all"}{literal}");
        else
            $("#tg").text("{/literal}{translate key="hide_all"}{literal}");
    }
    {/literal}
    
    // -->
</script>

<form onsubmit="return false;" class="actionForm"><button id="tg" onclick="toggleAll();return false;">{translate key="show_all"}</button></form>

{foreach from=$CONFIGURATIONS item=package}
    {cycle name="rowCss" reset=true values="row1," print=false assign="css"}
    {admin_box_header title=$package.name closed=true}
        <form action="{$package.action}#{$package.name}" id="{$package.name}" method="POST">
        <input type="hidden" name="{$PARAM_PACKAGE}" value="{$package.name}">

        <table width="100%" border="0" cellspacing="2" cellpadding="2">
	        <col width="250"/>
	        <col />
	        <tbody>
		        {foreach from=$package.configs item=configEntry}
                    {cycle name="rowCss" values="row1," print=false assign="css"}
	                <tr>
	                    <td class="{$css}">{$configEntry.name}</td>
	                    <td class="{$css}">{$configEntry.formInput}</td>
	                </tr>
	                {/foreach}
	                <tr class="buttons">
	                    <td colspan="2">
				            <button type="submit">{translate key="save}</button>
			            </td>
	                </tr>
	        </tbody>
        </table>

        </form>
    {admin_box_footer}
{/foreach}

<br/> 


<b>{translate key="create_new_configuration"}</b><br/>
<form action="{$NEW_URL}" method="POST">
<table class="tablesorter" cellspacing="0">
<col width="200" />
<col width="200"/>
<col />
<col width="130"/>
<col width="60" />
<thead>
	<tr>
		<th>{translate key="config_package}</th>
		<th>{translate key="name}</th>
		<th>{translate key="value}</th>
		<th align="center">{translate key="type}</th>
		<th align="center">{translate key="action}</th>
	</tr>
</thead>
<tbody>
    <tr>
        <td><input type="text" name="entryPackage" class="longText" /></td>
        <td><input type="text" name="entryName" class="longText" /></td>
        <td><input type="text" name="entryValue" class="longText" /></td>
        <td align="center">
        	<select name="{$NEW_PARAM}">
        	{foreach from=$NEW_TYPES item=type}
        		<option value="{$type}">{$type}</option>
        	{/foreach}
        	</select>
        </td>
        <td align="center"><button type="submit">{translate key="create"}</button></td>
    </tr>
	</tbody>
</table>
</form>


{admin_box_support_footer}