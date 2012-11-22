<?php
/**
* @package bigace.classes
*/

/**
* RSS Parser
*
* Klasse zur Anzeige von News-Channels, die im RDF/RSS-Format vorliegen
*
* (c) 2002 - 2003 Stefan Fischer
*
* @author  Stefan Fischer <post@stefan-fischer.net>
* @version 0.2
* @package bigace.classes
* @access  public
*/
class RSSParser
{
	var $fileUrl;

	var $parser;

	var $case_folding = TRUE;

	var $data = array();

	var $current_tag = '';

	var $item_count = 0;

	var $image_flag = false;

	var $input_flag = false;

	var $channel_flag = false;

	var $item_flag = false;

	var $options = array(
			'CHANNELDESC' => TRUE,
 			'ITEMDESC'    => TRUE,
 			'IMAGE'       => TRUE,
 			'TEXTINPUT'   => TRUE
		);

	var $styles = array(
			'MAXITEM'       => 15,
	 		'ITEMSEPARATOR' => '<hr>',
 			'DESCSEPARATOR' => '<p>',
 			'CHANNELTITLE'  => '',
 			'CHANNELDESC'   => '',
 			'CHANNELLINK'   => '',
 			'ITEMTITLE'     => '',
 			'ITEMDESC'      => '',
 			'ITEMLINK'      => '',
 			'TEXTINPUT'     => '',
        	'SUBMIT'        => '',
        	'TEXTINPUTTITLE' => '',
        	'TEXTINPUTDESC' => '',
        	'SUBMITVALUE'   => 'GO',
        	'TABLEWIDTH'    => 300,
        	'TABLECLASS'    => '',
        	'TABLEALIGN'    => 'left',
        	'LINKTARGET'    => '_blank'
		);

	var $error = array(
			100 => 'RSS Source: Not available',
        	101 => 'RSS Source: Fehler beim Parsen',
        	102 => 'Unbekannte Output Option',
        	103 => 'Unbekannter Output Style'
		);

	var $current_error = false;

	function RSSParser()
	{
		$this->data = array();
		$this->current_tag = '';
		$this->item_count = 0;
		$this->image_flag = false;
		$this->input_flag = false;
		$this->channel_flag = false;
		$this->item_flag = false;
		$this->current_error = false;
	}


	function parseRSS($file)
	{
		$this->getRSSData($file);

		if ($this->current_error) {
			return false;
		}  else {
			return $this->getOutput();
		}
	}


    function getOutput()
    {
		if ($this->styles['MAXITEM'] < $this->item_count) {
			$this->item_count = $this->styles['MAXITEM'];
		}

		$out  = '<TABLE align="'.$this->styles['TABLEALIGN'].'" class="'.$this->styles['TABLECLASS'].'" width="'.$this->styles['TABLEWIDTH'].'" cellspacing="2" cellpadding="2" border="0">';
    	$out .= '<TR><TH>';

		// Channel - Section
		$out  .= '<TABLE width="100%" cellspacing="0" cellpadding="3" border="0">';
	    $out .= '<TR>';
		// Image - Section
    	if ($this->options['IMAGE'] == true && isset($this->data['IMAGE'])) {
	    	$out .= '<TD>';
            $out .='<A href="'.$this->data['IMAGE']['LINK'].'" target="'.$this->styles['LINKTARGET'].'">';
            $out .= '<IMG src="'.$this->data['IMAGE']['URL'].'" alt="'.@$this->data['IMAGE']['TITLE'].'" border="0"';

	        if (isset($this->data['IMAGE']['WIDTH'])) {
	        	$out .= ' width="'.$this->data['IMAGE']['WIDTH'].'"';
            }

	        if (isset($this->data['IMAGE']['HEIGHT'])) {
	        	$out .= ' height="'.$this->data['IMAGE']['HEIGHT'].'"';
            }

	        $out .= '>';
	        $out .= '</A>';
            $out .= '</TD>';
    	}
	    $out .= '<TD width="90%">';
        $out .= '<SPAN class="'.$this->styles['CHANNELTITLE'].'">'.$this->data['CHANNEL']['TITLE'].'</SPAN>';
        $out .= $this->styles['DESCSEPARATOR'];

        if ($this->options['CHANNELDESC'] == true) {
	    	$out .= '<SPAN class="'.$this->styles['CHANNELDESC'].'">'.$this->data['CHANNEL']['DESCRIPTION'].'</SPAN>';
            $out .= $this->styles['DESCSEPARATOR'];
        }

        $out .='<A href="'.$this->data['CHANNEL']['LINK'].'" target="'.$this->styles['LINKTARGET'].'" class="'.$this->styles['CHANNELLINK'].'">'.$this->data['CHANNEL']['LINK'].'</A>';
        $out .= $this->styles['DESCSEPARATOR'];
        $out .= '</TD>';
        $out .= '<TD valign="top" align="right"><a href="' . $this->fileUrl . '" target="_blank"><img src="' . _BIGACE_DIR_PUBLIC_WEB . 'system/images/rss.png" width="36" height="14" border="0"></a></TD>';
        $out .= '</TR>';
	    $out .= '</TABLE>';
        $out .= '</TH></TR>';


   		$out .= '<TR><TD>';
		$out .= $this->styles['ITEMSEPARATOR'];
    	$out .= '</TD></TR>';

    	// Item - Section
		for ($i = 1; $i <= $this->item_count; $i++)  {

   			$out .= '<TR><TD>';
            $out .='<A href="'.$this->data['ITEM'][$i]['LINK'].'" target="'.$this->styles['LINKTARGET'].'" class="'.$this->styles['ITEMLINK'].'">'.$this->data['ITEM'][$i]['TITLE'].'</A>';
    		$out .= $this->styles['DESCSEPARATOR'];

			if ($this->options['ITEMDESC'] == true && isset($this->data['ITEM'][$i]['DESCRIPTION'])) {
	    		$out .= '<SPAN class="'.$this->styles['ITEMDESC'].'">'.$this->data['ITEM'][$i]['DESCRIPTION'].'</SPAN>';
            	$out .= $this->styles['DESCSEPARATOR'];
        	}

    		$out .= '</TD></TR>';
   			$out .= '<TR><TD>';
			$out .= $this->styles['ITEMSEPARATOR'];
    		$out .= '</TD></TR>';
		}

		// Textinput - Section
    	if ($this->options['TEXTINPUT'] == true && isset($this->data['TEXTINPUT'])) {
	    	$out .= '<TR><TD>';
            $out .= '<TABLE width="'.$this->styles['TABLEWIDTH'].'" cellspacing="0" cellpadding="3" border="0">';
	    	$out .= '<TR><TD align="left">';
            $out .= '<SPAN class="'.$this->styles['TEXTINPUTTITLE'].'">'.$this->data['TEXTINPUT']['TITLE'].'</SPAN>';
            $out .= $this->styles['DESCSEPARATOR'];
	    	$out .= '<SPAN class="'.$this->styles['TEXTINPUTDESC'].'">'.$this->data['TEXTINPUT']['DESCRIPTION'].'</SPAN>';
            $out .= $this->styles['DESCSEPARATOR'];
            $out .= '<FORM action="'.$this->data['TEXTINPUT']['LINK'].'" method="post" target="'.$this->styles['LINKTARGET'].'">';
            $out .= '<INPUT type="text" class="'.$this->styles['TEXTINPUT'].'" name="'.$this->data['TEXTINPUT']['NAME'].'">';
            $out .= '<INPUT type="submit" class="'.$this->styles['SUBMIT'].'" value="'.$this->styles['SUBMITVALUE'].'">';
            $out .= '</FORM>';
            $out .= '</TD></TR>';
	    	$out .= '</TABLE>';
            $out .= '</TD></TR>';
   			$out .= '<TR><TD>';
			$out .= $this->styles['ITEMSEPARATOR'];
    		$out .= '</TD></TR>';
    	}

    	$out .= '</TABLE>';
		return $out;
    }


	//
	function OutputOptions($arr)
	{
		for (reset($arr); list($k, $v) = each($arr);) {
			if (isset($this->options[strtoupper($k)])) {
				$this->options[strtoupper($k)] = $v;
			} else  {
				$this->error(102);
			}
		}
	}


	//
	function OutputStyles($arr)
	{
		for (reset($arr); list($k, $v) = each($arr);) {
			if (isset($this->styles[strtoupper($k)])) {
				$this->styles[strtoupper($k)] = $v;
			} else  {
				$this->error(103);
			}
		}
	}


	//
	function getRSSData($file)
	{
		$this->set_parser();
		$this->xml_file($file);

		if (!$this->current_error) {
			return $this->data;
		}

		return false;
	}


	//
	function startElement($parser, $tag, $attribute)
	{
		if (strtoupper(substr($tag,0,3)) == 'RDF') {
			$tag = substr($tag,4,strlen($tag));
		}

		$this->current_tag = $this->StringToUpper($tag);

		switch($this->current_tag) {

			case 'CHANNEL':
				$this->channel_flag = true;
			break;

			case 'IMAGE':
	  			$this->image_flag = true;
			break;

			case 'TEXTINPUT':
	  			$this->input_flag = true;
			break;

			case 'ITEM':
				$this->item_flag = true;
				$this->item_count++;
			break;

			default:
			break;
		}
	}


	//
	function endElement($parser, $tag)
	{
		switch ($this->StringToUpper($tag)) {

			case 'CHANNEL':
				$this->channel_flag = false;
			break;

			case 'IMAGE':
  				$this->image_flag = false;
			break;

			case 'TEXTINPUT':
	  			$this->input_flag = false;
			break;

			case 'ITEM':
				$this->item_flag = false;
			break;

			default:
			break;
		}
	}


	//
	function getCharacterData($parser, $cdata)
	{
		if ($this->channel_flag == true && $this->item_flag == false && $this->image_flag == false && $this->input_flag == false) {
			if ($this->current_tag != 'CHANNEL')  {
				if (!isset($this->data['CHANNEL'][$this->current_tag])) {
					$this->data['CHANNEL'][$this->current_tag] = '';
				}

				$this->data['CHANNEL'][$this->current_tag] .= $cdata;
			}
		}

		if ($this->image_flag == true)  {
			if ($this->current_tag != 'IMAGE')  {
				if (!isset($this->data['IMAGE'][$this->current_tag])) {
					$this->data['IMAGE'][$this->current_tag] = '';
				}

				$this->data['IMAGE'][$this->current_tag] .= $cdata;
			}
		}

		if ($this->input_flag == true)  {
			if ($this->current_tag != 'TEXTINPUT')  {
				if (!isset($this->data['TEXTINPUT'][$this->current_tag])) {
					$this->data['TEXTINPUT'][$this->current_tag] = '';
				}

				$this->data['TEXTINPUT'][$this->current_tag] .= $cdata;
			}
		}

		if ($this->item_flag == true)  {
			if ($this->current_tag != 'ITEM')  {
				if (!isset($this->data['ITEM'][$this->item_count][$this->current_tag])) {
					$this->data['ITEM'][$this->item_count][$this->current_tag] = '';
				}

				$this->data['ITEM'][$this->item_count][$this->current_tag] .= $cdata;
			}
		}
	}


	//
	function xml_file($file)
	{
	    $this->fileUrl = $file;

		if (!($fp = @fopen($file, "r"))) {
			$this->error(100);
    	}

		while($data = @fread($fp, 4096)) {
			if (!(xml_parse($this->parser,$data))) {
				$this->error(101);
			}
    	}

		xml_parser_free($this->parser);
	}


	//
	function set_parser()
	{
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, $this->case_folding);
		xml_set_element_handler($this->parser,"startElement","endElement");
		xml_set_character_data_handler($this->parser,"getCharacterData");
	}


	//
	function StringToUpper($tagname)
	{
		if ($this->case_folding) {
			return strtoupper($tagname);
		} else {
			return $tagname;
		}
	}


	//
	function getErrorCode()
	{
		if ($this->current_error) {
			return $this->current_error;
		}

		return false;
	}


	//
	function getErrorMessage()
	{
		if ($this->current_error) {
			if (isset($this->error[$this->current_error])) {
				return $this->error[$this->current_error];
			}

			return false;
		}

		return false;
	}


	//
	function error($code)
	{
		$this->current_error = $code;
	}

}  // end of class



/**
* @package bigace.classes
*/
class RSSParserCache extends RSSParser
{
	//
	var $caching = true;

	//
	var $cache_dir = '';

	//
	var $lifetime = 3600;

	//
	var $probability = 50;

	//
	var $cache_id = '';

	//
	var $error = array(
			100 => 'RSS Source: Not avilable',
        	101 => 'RSS Source: Error while parsing',
        	102 => 'Unbekannte Output Option',
        	103 => 'Unbekannter Output Style',
        	104 => 'Cache Verzeichnis existiert nicht',
        	105 => 'Cache Directory could not be opened',
        	106 => 'Konnte Cache Datei nicht lesen',
        	107 => 'Konnte Cache Datei nicht schreiben'
		);


	//
	function RSSParserCache($dir)
	{
		if (!is_dir($dir)) {
			$this->error(104);
    	} else {
			if ( (substr($dir,0,1) != '/') ) {
				$this->cache_dir = $dir.'/';
			} else {
				$this->cache_dir = $dir;
			}

			if ($this->doGarbageCollection()) {
				$this->deleteTrash();
			}

			$this->RSSParser();
		}
	}


	//
	function parseRSS($file)
	{
		if ($this->current_error) {
			return false;
		}

		if ($this->caching) {
	        $this->setCacheId($file);

    	    if ( !$this->deleteCacheFile($this->cache_dir.$this->cache_id) ) {
            	if (!$output = $this->readCacheFile()) {
           			$this->error(106);
            	}
    	   	} else {
            	if ( $this->getRSSData($file) ) {

            		$output = $this->getOutput();

            		if ( !$this->writeCacheFile($output) ) {
            			$this->error(107);
            		}
            	}
    	   	}
        } else {
			$this->getRSSData($file);
	    	$output = $this->getOutput();
		}

		if ($this->current_error) {
			return false;
		}  else {
	    	return $output;
		}
	}


	//
	function enableCaching($caching = true)
	{
		$this->caching = $caching;
	}


	//
	function setLifetime($lt)
	{
		$this->lifetime = $lt;
	}


	//
	function setCacheId($rssfile)
	{
		$options = serialize($this->options);
		$styles = serialize($this->styles);
		$this->cache_id = md5($rssfile.$options.$styles);
	}


	//
	function deleteTrash()
	{
		if (!($dh = opendir($this->cache_dir))) {
			$this->error(105);
    	}

		while ($file = readdir($dh)) {

    		if ($file != '.' && $file != '..') {
				$this->deleteCacheFile($this->cache_dir.$file);
			}
    	}
		closedir($dh);
	}


	//
	function doGarbageCollection()
	{
		$nr = rand(1,100);

		if ($nr <= $this->probability) {
    		return true;
		} else {
			return FALSE;
		}
	}


	//
	function deleteCacheFile($file)
	{
        if (!file_exists($file)) {
        	return true;
        } else {

			if (is_file($file)){
    			if (!$fh = fopen($file, "r")) {
        			return true;
    	        }
    
    			$data = fread($fh, filesize($file));
    			$valid = explode('###', $data);
    			fclose($fh);
    
    			if (trim($valid[0]) < time()) {
    				@unlink($file);
    				return true;
    			}
		    }
		}
		return false;
	}


	//
	function writeCacheFile($output)
	{
		if (!$fh = fopen($this->cache_dir.$this->cache_id, "w")) {
        	return false;
        }

        $valid = time() + $this->lifetime;
        flock($fh,2);
        fwrite($fh, $valid."###".$output);
        flock($fh,3);
        fclose($fh);
    	return true;
	}


	//
	function readCacheFile()
	{
		if (!($fh = fopen($this->cache_dir.$this->cache_id, "r"))) {
			return false;
        }

		$data = fread($fh, filesize($this->cache_dir.$this->cache_id));
		$out = explode('###', $data);
		fclose($fh);
		return $out[1];
	}

}  // end of class

?>