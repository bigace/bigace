<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$charset}">
    <script type="text/javascript" src="{directory name="public"}system/javascript/ajax_xml.js"></script>
    <script type="text/javascript" src="{directory name="public"}system/javascript/bigace_ajax.js"></script>
    <script type="text/javascript" src="{directory name="addon_web"}jquery/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="{directory name="addon_web"}jquery/jquery-ui-1.7.2.custom.min.js"></script>
    <script type="text/javascript" src="{directory name="addon_web"}jquery/jgrowl/jquery.jgrowl.js"></script>
    <link rel="stylesheet" href="{directory name="addon_web"}jquery/themes/bigace/bigace.css" type="text/css">
    <link rel="stylesheet" href="{directory name="addon_web"}jquery/jgrowl/jquery.jgrowl.css" type="text/css">
    <link rel="stylesheet" href="{$stylesheet}" type="text/css">
    <link rel="stylesheet" href="{$styleDir}editor/editor.css" type="text/css">
    <TITLE>{translate key="title"}</TITLE>
    <script type="text/javascript">
    <!--

    var menuChooserUrl      = "{$menuChooserUrl}";
    var modeSave            = "{$mode_save}";
    var modeClose           = "{$mode_close}";
    var menuStartId         = "{$MENU->getID()}";
    var menuStartLanguage   = "{$MENU->getLanguageID()}";
    var menuStartJsName     = "{$jsname}";
    var translate_saving    = '{translate key="saving"}';
    var translate_save      = '{translate key="save"}';
    var styleDir            = '{$styleDir}';
    var currentTranslate    = '{$SHOW_TRANSLATOR}';
    var saveDirtyChanges    = '{$saveDirtyChanges}';
    var performEval         = true;
    
	{foreach from=$languages item=lang}
    var langTrans_{$lang->getID()} = '{$lang->getName()}';
    {/foreach}

    function getAjaxItemUrl(itemid, languageid) {literal}{{/literal}
        return "{$ajaxItemUrl}";
    {literal}}{/literal}

    function getAjaxContentUrl(itemid, languageid) {literal}{{/literal}
        return "{$ajaxContentUrl}";
    {literal}}{/literal}

    function getEditorUrl(mode) {literal}{{/literal}
        return '{$editorUrl}';
    {literal}}{/literal}

    function getEditorFrameworkUrl(menuid, menulanguage) {literal}{{/literal}
        return '{$editFrameworkUrl}';
    {literal}}{/literal}
    
    {literal}

	$(document).ready( function() { 
			// removed, the initial reset must be handled by the editor
			//resetIsDirtyEditor();
			{/literal}
			{if !$inWorkflowNoSave}
			showEditorErrorText('{translate key="readonly_wf"}');
			{/if}
			{literal}
			self.focus();
		} 
	);

	window.onbeforeunload = askForSaveBeforeUnload;

	function askForSaveBeforeUnload() {
		if(isDirtyEditor()) {
			return saveDirtyChanges;
		}
	}    

    var editorHelper = {
        menuID          : menuStartId,
        languageID      : menuStartLanguage,
        name            : menuStartJsName,
        setName         : function (name) { this.name = name; },
        getName         : function()    { return this.name; },
        setMenuID       : function (id) { this.menuID = id; },
        getMenuID       : function()    { return this.menuID; },
        setLanguageID   : function (id) { this.languageID = id; },
        getLanguageID   : function()    { return this.languageID; }
    };

    function requestItemInfo(itemid, languageid, asynchronous) {
        return loadItem(getAjaxItemUrl(itemid, languageid), asynchronous);
    }

    function showBigaceMenuChooser() {
        var sizeX = 300;
        var sizeY = 400;
        fenster = open (menuChooserUrl,"SelectParent","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=yes,height="+sizeX+",width="+sizeY+",screenX=0,screenY=0");bBreite=screen.width;bHoehe=screen.height;fenster.moveTo((bBreite-sizeY)/2,(bHoehe-sizeX)/2);
    }

    function setMenu(id, language, name) {
        loadBigaceMenu(id, language);
    }

    function loadMenuAskForSave(menuid, menulanguage) {
    	{/literal}
		{*
		{if $SHOW_TRANSLATOR == ''}
        	askForSaveBeforeChanges( 'loadBigaceMenu("'+menuid+'", "'+menulanguage+'")' );
        {else}
        	askForSaveBeforeChanges( 'location.href = "'+getEditorFrameworkUrl(menuid, menulanguage)+'"' );
        {/if}
		*}
    	askForSaveBeforeChanges( 'location.href = "'+getEditorFrameworkUrl(menuid, menulanguage)+'"' );
    	{literal}
    }
    
    function translate(menuid, fromLanguage, toLanguage) {
    	askForSaveBeforeChanges( 'showTranslator("'+menuid+'", "'+fromLanguage+'", "'+toLanguage+'")' );
    }

    function showTranslator(menuid, fromLanguage, toLanguage) {
		var translateUrl = {/literal}'{$translateUrl}'{literal};
	    location.href = translateUrl;
    }

    function loadBigaceMenu(menuid, menulanguage) {
        try {
	        var oXML = new BIGACEAjaxXmlRequest();
	        oXML.LoadUrl( getAjaxContentUrl(menuid, menulanguage) );

	        if(oXML != null && oXML.DOMDocument != null) {
                if(oXML.SelectSingleNode('Item/Content') != null) {
                    document.getElementById("hiddenMenuID").value = menuid;
                    document.getElementById("hiddenLanguageID").value = menulanguage;

                    showCurrentItem(menuid, menulanguage);
                    
                    setEditorContent( readXmlValue(oXML.SelectSingleNode('Item/Content')) );
					resetIsDirtyEditor();
					return true;
                } else if(oXML.SelectSingleNode('Item/Error') != null) {
                    alert(readXmlValue(oXML.SelectSingleNode('Item/Error')));
                } else {
                    alert('Failed loading Page. XML Problem!');
                }
	        }
        } catch (exc) {
            alert('Could not load Page! Error occured: ' + exc);	
        }
        return false;
    }

    // keep information for later usage and display important stuff
    function showCurrentItem(menuid, languageid) {
        try {
            var item = requestItemInfo(menuid, languageid, false);
            editorHelper.setName( item.getName() );
            editorHelper.setMenuID( item.getID() );
            editorHelper.setLanguageID( item.getLanguage() );
    
            var tempHtml = '<a href="{/literal}{link command="menu" id="'+item.getID()+'" language="'+item.getLanguage()+'"}{literal}" target="_blank"><img style="margin-right:5px" alt="'+editorHelper.getLanguageID()+'" src="'+styleDir+'languages/'+editorHelper.getLanguageID()+'.gif">' + editorHelper.getName() + '</a>';
            document.getElementById("menuDisplayName").innerHTML = tempHtml; 

            var optGroupTranslate = '';
            for (i=0; i < item.getLanguages().length; i++)
            {
	            for (a=0; a < item.getLanguages().length; a++)
    	        {
	                var tempLang1 = item.getLanguages()[i];
	                var tempLang2 = item.getLanguages()[a];
	                if(tempLang1 != tempLang2)
						optGroupTranslate += '<option value="translate(\''+menuid+'\', \''+tempLang1+'\', \''+tempLang2+'\')">'+eval('langTrans_'+tempLang1)+' -&gt; '+eval('langTrans_'+tempLang2)+'</option>';
				}
            }
            //document.getElementById("optGroupTranslate").innerHTML = optGroupTranslate;

            var optGroupEdit = '';
            for (i=0; i < item.getLanguages().length; i++)
            {
                var tempLang = item.getLanguages()[i];
	            if(tempLang != item.getLanguage())
					optGroupEdit += '<option value="loadMenuAskForSave(\''+menuid+'\', \''+tempLang+'\')">'+eval('langTrans_'+tempLang)+'</option>';
            }
            document.getElementById("optGroupEdit").innerHTML = optGroupEdit;

        
        } catch (exc) {
            alert('Error displaying all Item information: ' + exc);	
        }
    }
    
    // parameter doClose indicates if we close the editor after saving
    function saveBigaceMenu(doClose) {
        showEditorStatusText( translate_saving, 700 );

        if(doClose == null) {
            doClose = false;
        }

        /*
        // Posting with AjAX does not work well, so we use the old fashioned style
        // by using an blind frame...
            alert( getEditorContent() );
            alert( encodeURI(getEditorContent()) );
            alert( encodeURI(getEditorContent()).replace(/&/g,"%26") );

	        var oXML = new BIGACEAjaxXmlRequest();
            
            var paramsToPost = 'mode='+modeSave;
            paramsToPost += '&data[id]='+editorHelper.getMenuID();
            paramsToPost += '&data[langid]='+editorHelper.getLanguageID();
            paramsToPost += '&{$contentParam}='+encodeURI(getEditorContent().replace(/&/g,"%26"));
	        oXML.PostUrl( getEditorUrl( modeSave ), paramsToPost);
        */

        try {
            document.forms["saveForm"].action = getEditorUrl( modeSave );
            document.getElementById("hiddenLanguageID").value = editorHelper.getLanguageID();
            document.getElementById("hiddenMenuID").value = editorHelper.getMenuID();
            if(doClose) {
                document.getElementById("hiddenSendClose").value = 'true';
            } else {
                document.getElementById("hiddenSendClose").value = 'false';
            }
            document.forms["saveForm"].submit();
			resetIsDirtyEditor(); // prevent the windowbeforeunload event to catch it again
        } catch (exc) {
            alert('Error saving Content: ' + exc);	
        }
    }

    function askForSaveBeforeChanges(callback) 
    {
        if(isDirtyEditor()) {
			performEval = true;
			$("#dialog").show();
			$("#dialog").dialog({ 
				title: {/literal}"{translate key="save_before_title"}"{literal},
				resizable : false,
				buttons: { 
					{/literal}"{translate key="save_before_yes"}"{literal}: function() { 
						//performEval = false;
						saveContent();
						$(this).dialog("close"); 
					}, 
					{/literal}"{translate key="save_before_no"}"{literal}: function() { 
						resetIsDirtyEditor(); // prevent the windowbeforeunload event to catch it again
						$(this).dialog("close"); 
					},
					{/literal}"{translate key="save_before_cancel"}"{literal}: function() { 
						performEval = false;
						$(this).dialog("close"); 
					} 
				} ,
				modal: true, 
				overlay: { 
					opacity: 0.7, 
					background: "black" 
				},
				close: function() { 
					if(performEval)
						eval(callback); 
				}
			});
        }
		else {
			eval(callback);
		}
    }

    // save content and exit afterwards 
    function saveAndExit() {
        saveBigaceMenu(true);
    }

    function saveContent() {
        saveBigaceMenu(false);
    }
    
    // reload the opener window and close the editor
    function doClose() {
        opener.location.reload();
        window.close();
    }

    function showEditorStatusText(msg, fadeoutTime) {
		$.jGrowl.defaults.closerTemplate = {/literal}'<div>{translate key="jgrowl_close_all"}</div>'{literal};
				
		if(fadeoutTime != null) {
			$("#jgrowlDiv").jGrowl(msg, { life: fadeoutTime });
			return;
		}

		$("#jgrowlDiv").jGrowl(msg, { sticky: true, speed:  'slow' });
    }
    
    function showEditorErrorText(msg, fadeoutTime) {
		showEditorStatusText('<span id="msg-err">' + msg+'</span>', fadeoutTime);
    }

    function showEditorHelp() {
    	myX = screen.availWidth/2 - 350;
        myY = screen.availHeight/2 - 250;
        {/literal}
        window.open( "http://wiki.bigace.de/doku.php?id=bigace:manual:{$editorType}", "EditorHelp", "width=700,height=500,screenX="+myX+",screenY="+myY);
        {literal}
    }

    function showHelp() {
    	myX = screen.availWidth/2 - 350;
        myY = screen.availHeight/2 - 250;
        window.open( "http://wiki.bigace.de/doku.php?id=bigace:manual:editcontent", "ContentHelp", "width=700,height=500,screenX="+myX+",screenY="+myY);
    }
    {/literal}
    // -->
    </script>
</head>
<body>

<div id="jgrowlDiv"></div>
<div class="bigace-dialog" id="dialog">{translate key="save_before_change"}</div>

<div class="button-panel">
	<div class="solid-buttons">
		<button class="save" onClick="saveBigaceMenu(false); return false;" accesskey="s">{translate key='save'}</button>
        <form action="{link item=$MENU}" method="post" target="_blank"><button class="preview" type="submit">{translate key="preview"}</button></form>
		{* if count($contents) > 1}
		<span class="pieces">
			{foreach from=$contents item="contentPiece"}
			<button href="#{$contentPiece.param}Ref">{$contentPiece.title}</button>
			{/foreach}
		</span>
		{/if *}
	</div>

	<div class="action-td bl">
    	<b>{translate key='select_action'}</b> 
    	<select onChange="eval(this.value);this.selectedIndex=0;">
        {if count($languages) > 0}
    		<option value=""></option>
    		<optgroup label="{translate key='group_editing'}" id="optGroupEdit">
              {foreach from=$languages item=lang}
            	{if $lang->getID() != $MENU->getLanguageID() || $SHOW_TRANSLATOR != ''}
        			<option value="loadMenuAskForSave('{$MENU->getID()}', '{$lang->getID()}')">{$lang->getName()}</option>
        		{/if}
              {/foreach}
    		</optgroup>
    		<optgroup label="{translate key='group_translating'}" id="optGroupTranslate">
              {foreach from=$languages item=lang}
	          	{foreach from=$languages item=lang2}
	            	{if $lang->getID() != $lang2->getID()}
        				<option value="translate('{$MENU->getID()}', '{$lang->getID()}','{$lang2->getID()}')">{$lang->getName()} -&gt; {$lang2->getName()}</option>
        			{/if}
              	{/foreach}
              {/foreach}
    		</optgroup>
        {/if}
    		<optgroup label="{translate key='group_help'}" id="optGroupHelp">
				<option value="showHelp()">{translate key='help_editing'}</option>
				<option value="showEditorHelp()">{translate key='help_editor'}</option>
    		</optgroup>
    	</select>
    </div>
	<div style="clear:both"></div>
</div>

<div class="outerEditor">
	{if $SHOW_TRANSLATOR != ''}
	<div class="translator">
		{foreach from=$translateContents item="transCnt"}
		<h1 class="contentTitle">&raquo; {$transCnt.title} [{$SHOW_TRANSLATOR}]</h1>
		<iframe class="translatorIframe" name="prevIFrame" src="{$transCnt.translate}" frameborder="no" scrolling="yes"></iframe>
		{/foreach}
	</div>
	{/if}

	{if $readOnly}
	<div class="readonly">
		<iframe name="readonly" src="{$readonlyUrl}" width="100%" height="99%" frameborder="no" scrolling="yes" style="border:1px solid #000000"></iframe>
	</div>
	{else}
	<div class="ctnEditor{if $SHOW_TRANSLATOR != ''}Translate{/if}">
		<form action="{$saveUrl}" target="fenster" method="post" name="saveForm" language="javascript" onSubmit="this.focus()" style="border:0px solid #000000;height:100%">
		<input type="hidden" name="mode" value="{$mode_save}">
		<input type="hidden" id="hiddenMenuID" name="data[id]" value="{$MENU->getID()}">
		<input type="hidden" id="hiddenLanguageID" name="data[langid]" value="{$MENU->getLanguageID()}">
		<input type="hidden" id="hiddenSendClose" name="sendClose" value="false">
	{/if}
