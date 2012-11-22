<?php

require_once(dirname(__FILE__).'/../../system/libs/init_session.inc.php');
import('classes.parser.Smileys');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Listing of all available Smileys</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo _BIGACE_DIR_PUBLIC_WEB;?>system/images/favicon.ico" />
	<meta name="description" content="Smileys listing" />
	<meta name="catchwords" content="BIGACE, Smileys, Plugin" />
	<meta name="robots" content="noindex,nofollow" />
    <meta name="generator" content="BIGACE <?php echo _BIGACE_ID; ?>" />
</head>
<body>
<table border="0" width="100%">
	<colgroup>
		<col width="45%"/>
		<col width="10%" />
		<col width="45%"/>
	</colgroup>
	<thead>
		<tr>
			<th>Default Smileys</th>
			<th></th>
			<th>Extended Smileys</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td valign="top">
				<table border="1" width="100%">
					<colgroup>
						<col />
						<col />
					</colgroup>
					<thead>
						<tr>
							<th>Sign</th>
							<th>Smiley</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$all = Smileys::getSmileys();
						foreach($all AS $code => $url) {
							echo "<tr>\n";
							echo '<td align="center">'.$code.'</td>';
							echo '<td align="center"><img src="'._BIGACE_DIR_ADDON_WEB.'/smileys/'.$url.'" border="0" alt="' . htmlspecialchars($code) . '"></td>';
							echo "</tr>\n";
						}
					?>
					</tbody>
				</table>
			</td>
			<td>&nbsp;</td>
			<td valign="top">
				<table border="1" width="100%">
					<colgroup>
						<col />
						<col />
					</colgroup>
					<thead>
						<tr>
							<th>Sign</th>
							<th>Smiley</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$all = Smileys::getTextSmileys();
						foreach($all AS $code => $url) {
							echo "<tr>\n";
							echo '<td align="center">'.$code.'</td>';
							echo '<td align="center"><img src="'._BIGACE_DIR_ADDON_WEB.'/smileys/'.$url.'" border="0" alt="' . htmlspecialchars($code) . '"></td>';
							echo "</tr>\n";
						}
					?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

</body>
</html>