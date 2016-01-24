{* $Id$ *}
<script type="text/javascript">
<!--
{literal}
    function setParent(id, language, tname) 
    {
        document.getElementById('parentid').value = id;
        document.getElementById('parentname').value = tname;
    }

    function setParentImport(id, language, tname) 
    {
        document.getElementById('parentid2').value = id;
        document.getElementById('parentname2').value = tname;
    }
	
    function checkFormular()
    {
    	{/literal}
		{*
		// TODO removed cause of server side validation and no valid framework
        if (document.getElementById("userfile").value == '') {
            showJSError('{translate key="error"}', '{translate key="upload_choose_file"}');
            return false;
        }
        if (document.getElementById("name").value == '') {
            showJSError('{translate key="error"}', '{translate key="upload_choose_name"}');
            return false;
        }
		*}
		{literal}
        return true;
    }

	function addUploadBox() {
		boexli = document.getElementById("uploadFileBox");
		var neueBR = document.createElement("br");
		var neueBoexli = document.createElement("input");
		var neueBoexliType = document.createAttribute("type");
		neueBoexliType.nodeValue = "file";
		neueBoexli.setAttributeNode(neueBoexliType);
		var neueBoexliSize = document.createAttribute("size");
		neueBoexliSize.nodeValue = "50";
		neueBoexli.setAttributeNode(neueBoexliSize);
		var neueBoexliName = document.createAttribute("name");
		neueBoexliName.nodeValue = "userfile[]";
		neueBoexli.setAttributeNode(neueBoexliName);
		var neueBoexliMultiple = document.createAttribute("multiple");
		neueBoexliMultiple.nodeValue = "multiple";
		neueBoexli.setAttributeNode(neueBoexliMultiple);
		var neueBoexliOnChange = document.createAttribute("onChange");
		neueBoexliOnChange.nodeValue = "addNamingInfo()";
		neueBoexli.setAttributeNode(neueBoexliOnChange);
		boexli.insertBefore(neueBR,document.getElementById('endSpacer'));
		boexli.insertBefore(neueBoexli,document.getElementById('endSpacer'));
                addNamingInfo();
		}

    function addNamingInfo()
    {
	var boexli2 = document.getElementById("namingMethod");
        var boexli3 = document.getElementsByName("userfile[]");
        if (boexli2.hasChildNodes()) {
            return;
        }
        if (boexli3[0].files && boexli3[0].files.length > 1 || boexli3.length > 1) {
{/literal}
			boexli2.innerHTML = '<input type="radio" name="namingType" value="namingFile" checked="checked" id="namingFile"> <label for="namingFile">{translate key="naming_filename}</label>'
                             + '<br><input type="radio" name="namingType" value="namingCount" id="namingCount"> <label for="namingCount">{translate key="naming_count}</label>';
{literal}
        }
    }
{/literal}
// -->
</script>

{$PARENT_CHOOSER1}
{$PARENT_CHOOSER2}

<div id="tabpanes" class="ui-tabs">
	
	<ul>
		<li><a href="#tabpage1"><span>{translate key="tab_upload"}</span></a></li>
		<li><a href="#tabpage2"><span>{translate key="tab_import"}</span></a></li>
	</ul>
	
	<div id="tabpage1">
	    <form name="uploadForm" id="uploadForm" action="{$ACTION_LINK}" method="post" ENCTYPE="multipart/form-data" onSubmit="return checkFormular();">
	    <input type="hidden" name="mode" value="{$MODE_UPLOAD}">
	    <table class="tablesorter" cellspacing="0">
	    <col width="250" />
	    <col />
	      <thead>
		     <tr>
		       <th colspan="2" align="left"><img src="{$STYLE_DIR}item_{$ITEMTYPE}_new.png"> {translate key="title_upload"}</th>
		    </tr>
	      </thead>
	      <tbody>
		    <tr>
			    <td align="left" valign="top">
	                {translate key="choose_file"}<br/>
	                (max. {$MAX_FILE_SIZE} / File)
	            </td>
		        <td align="left" id="uploadFileBox">
				    <input type="file" size="50" name="userfile[]" multiple="multiple" onChange="addNamingInfo()"/>
				    <span id="endSpacer"></span>
				    <a href="#" onClick="addUploadBox();return false;"><br />[+] {translate key="choose_more_files"}</a>
			    </td>
	        </tr>
	      </tbody>
	    </table>
	
	    <div class="buttons">
	        <button class="ok" type="submit">{translate key="process_upload"}</button>
		</div>
	
	    <table class="tablesorter" cellspacing="0">
	    <col width="250" />
	    <col />
	      <thead>
		     <tr>
		       <th colspan="2" align="left">Details</th>
		    </tr>
	      </thead>
	      <tbody>
		    <tr>
			    <td align="left" valign="top">{translate key="save_below"}</td>
		        <td align="left">
	                <input type="hidden" name="data[parentid]" value="{$PARENT_ID}" id="parentid">
	                <input type="text" id="parentname" name="parentname" value="{$PARENT_NAME}" disabled="disabled">
	                &nbsp;<input type="button" value="{translate key='choose'}" onclick="parentSelector()">
			    </td>
	        </tr>
		    <tr>
			    <td align="left" valign="top">{translate key="name"}</td>
			    <td align="left">
				    <input type="text" name="data[name]" id="name" maxlength="200" size="35" value="{$DATA_NAME}">
				    <div id="namingMethod"></div>
			    </td>
		    </tr>
		    <tr>
			    <td align="left" valign="top">{translate key="unique_name"}</td>
			    <td align="left">
				    <input type="text" name="data[unique_name]" id="unique_name" maxlength="200" size="35" value="{$UNIQUE_NAME}">
				    <div>{translate key="replacer"}: {literal}{NAME}, {FILENAME}, {COUNTER}{/literal}</div>
			    </td>
		    </tr>
		    <tr>
		          <td align="left" valign="top">{translate key="description"}</td>
		          <td align="left"><textarea name="data[description]" id="description" rows="5" cols="40" wrap="">{$DATA_DESCRIPTION}</textarea></td>
		    </tr>
		    <tr>
			    <td align="left">{translate key="language"}</td>
			    <td align="left">{$LANGUAGES}</td>
		    </tr>
		    <tr>
		      <td align="left" valign="top">{translate key="category"}</td>
		      <td align="left">{$CATEGORY_SELECTOR}</td>
		    </tr>
	      </tbody>
	    </table>
	
	    <div class="buttons">
	        <button class="ok" type="submit">{translate key="process_upload"}</button>
		</div>
	
	    </form>
	</div>
	
	<div id="tabpage2" class="ui-tabs-hide">
	    <form name="uploadForm" id="uploadForm" action="{$ACTION_LINK}" method="post" ENCTYPE="multipart/form-data" onSubmit="return checkFormular();">
	    <input type="hidden" name="mode" value="{$MODE_IMPORT}">
	    <table class="tablesorter" cellspacing="0">
	    <col width="250" />
	    <col />
	      <thead>
		     <tr>
		       <th colspan="2" align="left"><img src="{$STYLE_DIR}item_{$ITEMTYPE}_new.png"> {translate key="title_import"}</th>
		    </tr>
	      </thead>
	      <tbody>
		    <tr>
			    <td align="left" valign="top">{translate key="import_urls"}</td>
		        <td align="left" id="uploadFileBox">
				    <textarea name="importURLs" style="width:430px" rows="5">{$IMPORT_URLS}</textarea>
				    <br />
				    {translate key="import_info"}
			    </td>
		    </tr>
	      </tbody>
	    </table>
	
	    <div class="buttons">
	        <button class="ok" type="submit">{translate key="process_import"}</button>
		</div>
	    
	    <table class="tablesorter" cellspacing="0">
	    <col width="250" />
	    <col />
	      <thead>
		     <tr>
		       <th colspan="2" align="left">Details</th>
		    </tr>
	      </thead>
	      <tbody>
		    <tr>
			    <td align="left" valign="top">{translate key="save_below"}</td>
		        <td align="left">
	                <input type="hidden" name="data[parentid]" value="{$PARENT_ID}" id="parentid2">
	                <input type="text" id="parentname2" name="parentname" value="{$PARENT_NAME}" disabled="disabled">
	                &nbsp;<input type="button" value="{translate key='choose'}" onclick="parentSelector2()">
			    </td>
	        </tr>
		    <tr>
			    <td align="left" valign="top">{translate key="name"}</td>
			    <td align="left">
				    <input type="text" name="data[name]" id="name" maxlength="200" size="35" value="{$DATA_NAME}">
				    <div id="namingMethod">
	                    <input type="radio" name="namingType" value="namingFile" checked="checked" id="namingFile"> <label for="namingFile">{translate key="naming_filename}</label>
	                    <br><input type="radio" name="namingType" value="namingCount" id="namingCount"> <label for="namingCount">{translate key="naming_count}</label>
	                </div>
			    </td>
		    </tr>
		    <tr>
			    <td align="left" valign="top">{translate key="unique_name"}</td>
			    <td align="left">
				    <input type="text" name="data[unique_name]" id="unique_name" maxlength="200" size="35" value="{$UNIQUE_NAME}">
				    <div>{translate key="replacer"}: {literal}{NAME}, {FILENAME}, {COUNTER}{/literal}</div>
			    </td>
		    </tr>
		    <tr>
		          <td align="left" valign="top">{translate key="description"}</td>
		          <td align="left"><textarea name="data[description]" id="description" rows="5" cols="40" wrap="">{$DATA_DESCRIPTION}</textarea></td>
		    </tr>
		    <tr>
			    <td align="left">{translate key="language"}</td>
			    <td align="left">{$LANGUAGES}</td>
		    </tr>
		    <tr>
		      <td align="left" valign="top">{translate key="category"}</td>
		      <td align="left">{$CATEGORY_SELECTOR}</td>
		    </tr>
	      </tbody>
	    </table>
	
	    <div class="buttons">
	        <button class="ok" type="submit">{translate key="process_import"}</button>
		</div>
	
	    </form>
	</div>

</div>

<script type="text/javascript">
{literal}
$(document).ready( function() { 
        $(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 0: {sorter: false} } }); 
    } 
);

$(document).ready(function(){
    $("#tabpanes").tabs();
});
{/literal}
</script>
