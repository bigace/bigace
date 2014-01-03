<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 * 
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation, 
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 */

import('classes.util.html.CopyrightFooter');
define('_BIGACE_ERR_PUB_DIR', str_replace('{BASE_DIR}','',BIGACE_URL_HTTP . 'public/'));

/**
 * This script displays a HTML representation of the 403 Error Code.
 */


if (!headers_sent()) {
    header("HTTP/1.0 500 Internal Server Error");
}

?><!DOCTYPE html>
<html>
<head>
<title>BIGACE Web CMS - Fatal System error</title>
<meta name="robots" content="noindex, nofollow">
<style type="text/css">
    body { background-color:#DDD; margin:5px; color: #000000; }
    body, td { font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size:13px; margin : 0; }
    a { color: #000080; }
    table { box-shadow:0 0 50px #AAAAAA; border: 1px solid #CCC; margin-top: 50px; padding: 50px 50px 20px 50px; }
</style>
</head>
<body>

<?php
echo '<table width="630" height="300" border="0" BGCOLOR="#FFFFFF" align="center">';

echo '<tr>';
echo '<td width="22">&nbsp;</td>';
echo '<td COLSPAN="2" valign="TOP" align="left">';
    echo '<div class="error"><B>Error: 500</B><br />'.$exception->getMessage().'</div>';
echo '</td>';
echo '<td width="22">&nbsp;</td>';
echo '</tr>';

echo '<tr>';
echo '<td width="22">&nbsp;</td>';
echo '<td colspan="2" valign="TOP" align="left">';
    echo 'Please check your community configuration!<br />&nbsp;<br /><b>System could not startup.</b>';
echo '</td>';
echo '<td width="22">&nbsp;</td>';
echo '</tr>';

echo '<tr>';
echo '<td width="22">&nbsp;</td>';
echo '<td colspan="2" valign="bottom" align="center">';
   echo 'Powered by <a href="http://www.bigace.de/" target="_blank">BIGACE ' . _BIGACE_ID. '</a>';
echo '</td>';
echo '<td width="22">&nbsp;</td>';
echo '</tr>';

echo '</TABLE>';
echo '</BODY>';
echo '</HTML>';
exit;
