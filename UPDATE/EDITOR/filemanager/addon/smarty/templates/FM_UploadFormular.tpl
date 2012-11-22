{* $Id$ *}
<script type="text/javascript">
<!--
	var counter = 1;
	
	var msgError = "{translate key="error"}";
	var msgChooseFile = "{translate key="upload_choose_file"}";
	var msgChooseName = "{translate key="upload_choose_name"}";
	var msgNamingFilename = "{translate key="naming_filename"}";
	var msgNamingCounter = "{translate key="naming_count"}";
	
{literal}

    function checkFormular()
    {
		/*
		// TODO removed cause of server side validation and no valid framework
        if (document.getElementById("userfile").value == '') {
            showJSError(msgError, msgChooseFile);
            return false;
        }
        if (document.getElementById("name").value == '') {
            showJSError(msgError, msgChooseName);
            return false;
        }
		*/
        return true;
    }

	function addUploadBox() {
		boexli = document.getElementById("uploadFileBox");
		var neueBR = document.createElement("br");
		var neueBoexli = document.createElement("input");
		var neueBoexliType = document.createAttribute("type");
		neueBoexliType.nodeValue = "file";
		neueBoexli.setAttributeNode(neueBoexliType);
		var neueBoexliName = document.createAttribute("name");
		neueBoexliName.nodeValue = "userfile[]";
		neueBoexli.setAttributeNode(neueBoexliName);
		boexli.insertBefore(neueBR,document.getElementById('endSpacer'));
		boexli.insertBefore(neueBoexli,document.getElementById('endSpacer'));
		counter++;
		if(counter == 2) {
			boexli2 = document.getElementById("namingMethod");
			boexli2.innerHTML = '<br><input type="radio" name="namingType" value="namingFile" checked="checked" id="namingFile"> <label for="namingFile">' + msgNamingFilename + '</label>';
			boexli2.innerHTML += '<br><input type="radio" name="namingType" value="namingCount" id="namingCount"> <label for="namingCount">' + msgNamingCounter + '</label>';
		}
	}
{/literal}
// -->
</script>

<form name="uploadForm" id="uploadForm" action="{$ACTION_LINK}" method="post" ENCTYPE="multipart/form-data" onSubmit="return checkFormular();">
  <table cellspacing="0" class="tablesorter">
    <thead>
    	<tr>
    		<th colspan="2">{translate key="upload_title"}</th>
    	</tr>
    </thead>
    <tbody>  
      <tr class="row1">
        <td align="left">{translate key="choose_file"}</td>
        <td align="left" id="uploadFileBox">
			<input type="file" name="userfile[]" />
			<span id="endSpacer"></span>
			<br /><a href="#" onClick="addUploadBox();return false;">[+] {translate key="choose_more_files"}</a>
		</td>
      </tr>
      <tr class="row2">
        <td align="left">{translate key="name"}</td>
        <td align="left">
			<input type="text" name="data[name]" id="name" maxlength="200" size="35" value="{$DATA_NAME}">
			<div id="namingMethod"></div>
		</td>
      </tr>
      <tr class="row1">
			<td align="left" valign="top">{translate key="unique_name"}</td>
			<td align="left">
				<input type="text" name="data[unique_name]" id="unique_name" maxlength="200" size="35" value="{$UNIQUE_NAME}">
				<div>{translate key="replacer"}: {literal}{NAME}, {FILENAME}, {COUNTER}{/literal}</div>
			</td>
      </tr>
      <tr class="row2">
        <td align="left" valign="top">{translate key="description"}</td>
        <td align="left"><textarea name="data[description]" id="description" rows="5" cols="40" wrap="">{$DATA_DESCRIPTION}</textarea></td>
      </tr>
      <tr class="row1">
        <td align="left">{translate key="language"}</td>
        <td align="left">
            <select name="data[langid]" id="langid">
			    {foreach item="langID" key="langName" from=$LANGUAGES}
                <option value="{$langID}"{if $LANGUAGE_SELECTED == $langID} selected{/if}>{$langName}</option>
                {/foreach}
            </select>
        </td>
      </tr>
      <tr class="row2">
        <td align="left" valign="top">{translate key="category"}</td>
        <td align="left">{$CATEGORY_SELECTOR}</td>
      </tr>
      <tr class="row1">
        <td class="buttons" colspan="2">
            <button type="submit">{translate key="process_upload"}</button>
        </td>
      </tr>
    </tbody>
  </table>
</form>
