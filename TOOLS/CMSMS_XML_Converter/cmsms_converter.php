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
 * @package bigace.tools
 */

if(!defined('CMSMS_FOR_BIGACE')) die('Not runnable');

/**
 * This file contains the logic, required to convert a CMSMS Template into a BIGACE Design.
 */
class CMSMS_TemplateConverter
{
	private $imp;
	private $stylesheets = array();
	private $references = array();
	private $meta;

	function CMSMS_TemplateConverter($importer,$meta) {
		$this->imp = $importer;
		$this->meta = $meta;
	}
	
	function write($file, $data)
	{
		
	}

	function createExport($dirname) 
	{
		$designName = $this->imp->name->getValue();

		$tplDir = 'consumer/cid{CID}/install/';
		$publicDir = 'public/cid{CID}/'.$designName.'/';
		$tplFile = $tplDir . $designName . '.xml';

		$this->write('update.ini', $this->updateIni($designName, 'consumer/cid{CID}/install/', $designName . '.xml'));
		$data = $this->templateHeader() .
				"    <table name=\"stylesheet\">"; 

		foreach($this->imp->stylesheet AS $t) {
			$this->stylesheets[$t->getValue('cssmediatype')] = $t->getValue('cssname');
			$this->write($publicDir.$t->getValue('cssname').'.css', base64_decode($t->getValue('cssdata')));
			$data .= $this->formatStylesheet($designName, $t->getValue('cssname'));
			//$t->getValue('cssmediatype')
		}
		$data .= "    </table>\n\n";
		
		// create references
		foreach($this->imp->reference AS $t) {
			$this->references[$t->getValue('refname')] = $t->getValue('reflocation');
			$nnnn = ($t->getValue('refencoded') ? base64_decode($t->getValue('refdata')): $t->getValue('refdata'));
			$this->write($publicDir . $nnnn, $t->getValue('refname'));
		}

		// create templates
		$data .= "    <table name=\"template\">";
		foreach($this->imp->template AS $t) {
			$data .= $this->formatTemplate($tplDir, 
										$designName,
										$t->getValue('tname'), 
										base64_decode($t->getValue('tdata')),
										$t->getValue('tencoding'));
		}
		$data .= "    </table>\n\n";
		$data .= $this->templateFooter();
		
		$this->write($tplFile, $data);
	}

	// tries to replace known cmsms tags with bigace tags
	function parseTemplate($content, $designName) {

		// fix paths - mostly images
		foreach($this->references as $name => $refLoc) {
			$content = str_replace($refLoc, '{directory}'.$designName.'/'.$name, $content);
		}

		// replace {title}
		$content = str_replace("{title}", '{$MENU->getName()}', $content);

		// replace {metadata}
		$content = str_replace("{metadata}", "
    <meta name=\"description\" content=\"{\$MENU->getDescription()}\" />
    <meta name=\"generator\" content=\"{bigace_version full=true}\">
    <meta name=\"keywords\" content=\"your,keywords,goes,here\" />
", $content);

		// replace {stylesheet} tag
		$styles = "\n";
		foreach($this->stylesheets as $type => $name) {
			$styles .= '<link rel="stylesheet" type="text/css" href="{stylesheet name="'.$name.'"}" media="'.$type.'" title="'.$designName.'" />' . "\n";
		}
		$content = str_replace("{stylesheet}", $styles, $content);

		return $content;
	}

	function formatStylesheet($designName, $name) {
		return "
        <row>
            <name key=\"true\">".$name."</name>
            <cid key=\"true\">{CID}</cid>
            <description>Stylesheet for ".$name.".</description>
            <filename>".$designName."/".$name.".css</filename>
            <editorcss>dummy_stylesheet</editorcss>
        </row>
		";
	}

	function formatTemplate($dir, $designName, $name, $content, $encoding) {
		return "
        <row>
            <name key=\"true\">".$name."</name>
            <cid key=\"true\">{CID}</cid>
            <description>Auto import: ".$name."</description>
            <inwork>0</inwork>
            <include>0</include>
            <timestamp function=\"true\">unix_timestamp()</timestamp>
            <userid>1</userid>
            <filename>".$name.".tpl</filename>
            <content><![CDATA[ 
			" . $this->parseTemplate($content, $designName) . "
            ]]></content>
        </row>
		";
	}

	function templateHeader() {
		return "<content version=\"1.0\">
";
	}

	function templateFooter() {
		return "\n</content>";
	}

	function updateIni($designName, $tplDir, $tplFile) {
		return "
;
; Update Configuration file
;
; @author: Kevin Papst
;

[info]
title = \"".$designName."\"
version = \"".$this->meta['VERSION']."\"
description = \"".$designName.". Ported by ".$this->meta['AUTHOR']." for BIGACE.\"
type = smarty

[ignore_files]
cvs	= CVS
xml = \"".$tplFile."\"

[permission]
consumer = ALL
version = \"2.4\"
comparator = \">=\"

[consumer]
xml = \"".$tplDir.$tplFile."\"
";
	}

}

?>