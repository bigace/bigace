{* 	@license http://opensource.org/licenses/gpl-license.php GNU Public License
	@author Kevin Papst 
	@copyright Copyright (C) Kevin Papst 
	@version $Id$ *}

<style type="text/css">@import url({directory name="plugins"}news/jscalendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="{directory name="plugins"}news/jscalendar/calendar.js"></script>
<script type="text/javascript" src="{directory name="plugins"}news/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="{directory name="plugins"}news/jscalendar/lang/calendar-{$LANGUAGE}.js"></script>
<script type="text/javascript" src="{directory name="plugins"}news/jscalendar/calendar-setup.js"></script>
<script type="text/javascript">
<!--
	var imgChooserUrl = '{$IMAGE_CHOOSER_URL}';
	
	{literal}
    function OpenServerBrowser( url, width, height )
    {
    	var iLeft = (screen.width  - width) / 2 ;
    	var iTop  = (screen.height - height) / 2 ;
    
    	var sOptions = "scrollbars=yes,toolbar=no,status=no,resizable=yes,dependent=yes" ;
    	sOptions += ",width=" + width ;
    	sOptions += ",height=" + height ;
    	sOptions += ",left=" + iLeft ;
    	sOptions += ",top=" + iTop ;
    
    	var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
    	return oWindow;
    }    		

	function chooseImage() {
		OpenServerBrowser(imgChooserUrl,760,450);
	}

	function clearImage() {
		$("#imageURL").val("");
		$("#imageID").val("");
		$("#imageName").val("");
	}

	function chooseDate() {
		alert("Datum ausssuchen");
	}
	
	function SetUrl(imgUrl) {
		document.getElementById('imageID').value = imgUrl;
	}
	
	function SetImageInfos(imgID, imgName) {
		document.getElementById('imageID').value = imgID;
		document.getElementById('imageName').value = imgName;
	}
	{/literal}

//-->
</script>

<form action="" method="POST">
{foreach from=$EDIT_CONFIG.hiddenValues item="value" key="key"}
<input type="hidden" name="{$key}" value="{$value}">
{/foreach}

<table class="tablesorter" cellspacing="0">
<col width="150px" />
<col />
<thead>
	<tr>
	    <th colspan="2">{translate key="news_mode_`$EDIT_CONFIG.mode`"}</th>
    </tr>
</thead>
<tbody>
	<tr class="row1">
	    <td>{translate key="news_title"}</td>
	    <td><input type="text" name="title" style="width:350px;" value="{$EDIT_CONFIG.title|text_input}"></td>
    </tr>
	<tr>
		<td>{translate key="news_teaser"}</td>
	    <td><textarea name="teaser" style="width:350px;" rows="5">{$EDIT_CONFIG.teaser|text_input}</textarea></td>    
	</tr>
	<tr class="row1">
		<td>{translate key="news_categories"}</td>
	    <td>
	    	<table border="0" cellspacing="0" cellpadding="0">
	    		<tr>
	    			<td>{$CATEGORY_CHOOSER}</td>
	    			<td valign="bottom">
				    	{if $PERM_CATEGORIES}
				    		{translate key="news_new_categories"}
				    		<br>
				    		<input type="text" name="newCategories" style="width:250px;">
				    		{translate key="news_categories_split"}
				    	{/if}
	    			</td>
	    		</tr>
	    	</table>
	    </td>    
	</tr>
	<tr>
	    <td>{translate key="news_date"}</td>
	    <td valign="center">
			<input type="hidden" id="news_Date" name="newsDate" value="{$EDIT_CONFIG.newsDate}">
			<span id="show_Date">{$EDIT_CONFIG.newsDate}</span>
			<img src="{directory name="plugins"}news/jscalendar/img.gif" id="dateTrigger"
				 style="cursor: pointer; border: 1px solid red;"
				 title="{translate key="news_choose_date"}"
				 onmouseover="this.style.background='red';"
				 align="absmiddle"
				 onmouseout="this.style.background=''" />
		</td>
    </tr>
	<tr class="row1">
	    <td>{translate key="news_image"}</td>
	    <td>
	    	<input type="hidden" id="imageURL" name="imgURL" value="{$EDIT_CONFIG.imageURL}"> 
	    	<input type="hidden" id="imageID" name="imgID" value="{$EDIT_CONFIG.imageID}"> 
			<input type="text" id="imageName" name="imgName" size="30" value="{$EDIT_CONFIG.imageName}" disabled="disabled">
			<button onClick="chooseImage(); return false;">{translate key="news_choose_image"}</button>
			<button class="reset" onClick="clearImage(); return false;">{translate key="news_reset_image"}</button>
		</td>
    </tr>
	<tr>
	    <td colspan="2">
	    	{if $CONTENT_EDITOR != ''}
	    		{$CONTENT_EDITOR}
	    	{else}
	    		<textarea name="content" style="height:300px;width:100%">{$EDIT_CONFIG.content|htmlspecialchars}</textarea>
	    	{/if}
	    </td>
    </tr>
	<tr class="row1">
	   <td>{translate key="news_published"}</td>
	   <td>
	   		<input id="pubNewsCheck" type="checkbox" value="1" name="publish"{if $EDIT_CONFIG.published} checked="checked"{/if}>
	   		<label for="pubNewsCheck">{translate key="news_published_info"}</label> 
	   </td>
    </tr>
	<tr>
	    <td colspan="2" align="center"><button type="submit" class="save">{translate key="news_save"}</button></td>
    </tr>
</tbody>
</table>

</form>

<script type="text/javascript">
  Calendar.setup(
  {literal}{{/literal}
	    inputField	: "news_Date",
        ifFormat	: "{$EDIT_CONFIG.dateFormat}",
        displayArea : "show_Date",
        daFormat	: "{$EDIT_CONFIG.dateFormat}",
		showsTime	: true,
        timeFormat	: "{$EDIT_CONFIG.timeFormat}",
		button      : "dateTrigger"
  {literal}}{/literal}
  );
</script>