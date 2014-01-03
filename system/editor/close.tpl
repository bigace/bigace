{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst 
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
    <title>Editor</title>
    <meta http-equiv="Content-Type" content="text/html; charset={$charset}">
    <style type="text/css">
    {literal}
    body { margin:0px; }
    body, td { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px}
    a { color: #d54a00; text-decoration: none }
    a:hover { text-decoration: underline }
    .BackBody { background-color: #DEDBD6 }
    .Bar { background-color: #B5AEAD }
    </style>
    <script language="javascript">
    <!--
    function CloseWithSave()
    {
        opener.saveAndExit();
        window.close();
    }

    function CloseWithoutSave()
    {
        opener.doClose();
        window.close();
    }
    
    window.focus();
    {/literal}
    //-->
    </script>
</head>
<body bgcolor="#ffffff" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<form name="saveForm">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr bgcolor="#b5aead">
							<td><img src="{directory name="public"}system/images/spacer.gif" width="10" height="19"></td>
							<td align="right" width="100%">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="100%" valign="top" class="BackBody">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" align="middle">
								<table width="80%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td align="center">
    										<br>
                                            {translate key="save_before_quit"}
    										<br>
    										<br>
    										<table border="0" width="20%" align="center">
        										<tr>
            										<td><input type="button" value="{translate key="close_yes"}" onclick="CloseWithSave();"></td>
                                                    <td><input type="button" value="{translate key="close_no"}" onclick="CloseWithoutSave();"></td>
        										</tr>
    										</table>
    										<br>
    										
										</td>
									</tr>
								</table>
								<br>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height="100%">
				<td class="BackBody" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
						<tr>
							<td height="100%" align="right" valign="bottom">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><img src="{directory name="public"}system/images/spacer.gif" width="10" height="10"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="Bar" align="right">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="right" valign="bottom">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><img src="{directory name="public"}system/images/spacer.gif" width="10" height="19"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
</body>
</html>
