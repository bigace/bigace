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
 * @package bigace.classes
 * @subpackage util
 */

/**
 * DEVELOPMENT VERSION, NOT FOR PUBLIC USE
 * CLASS ContentTags - Usefull for working with tags in content
 * Methods:
 *     Null ContentTag(string $content) - Constructor, build up the class object over given $content
 *     Null EnumerateTags() - Fills tagsInfo structure (Array containing 'caption', 'text', 'start', 'end', 'replacer' fields)
 *     Bool SetTag(string $tag) - Set tag you want work with (with or without brackets)
 *     Array GetTagsInfo() - Return tagsInfo structure, containing tags caption, text, start position, end position and tag replacer
 *     Array GetIdByCaption(string $caption) - Return IDs of tags with given tag $caption
 *     Array GetIdByText(string $text) - Return IDs of tags with given tag $text
 *     Array GetIdByStart(integer $start) - Return IDs of tags with given tag start position $start
 *     Array GetIdByEnd(integer $end) - Return IDs of tags with given tag end position $end (last close tag symbol position)
 *     Array GetIdByReplacer(string $replacer) - Return IDs of tags with given tag $replacer
 *     Null SetReplacer(string $replacer, integer $id) - Set replacer $replacer for tags with ID $id
 *     String ReplaceTags() - Return content with tags replaced with replacer wich set via SetReplacer()
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Oleg Selin, Kevin Papst 
 * @copyright Copyright (C) Oleg Selin, Kevin Papst
 * @version $Id$
 * @package bigace.classes
 * @subpackage util
 */
class ContentTags {
    private $content = null;
    private $start = 0;
    private $tag = null;
    private $tagsInfo = array(0 => array('caption' => 'caption', 'text' => 'text', 'start' =>0, 'end' =>0, 'replacer'=>'replacer'));

    public function __construct($content) {
        $this->content = $content;
    }

    public function SetTag($tag) {
        if ($tag != null) {
            $this->tag = $tag;
            if (stripos($this->tag, '[') === false && stripos($this->tag, ']') === false) {
                $this->tag = '['.$this->tag.']';
            }
            $this->EnumerateTags();
            return true;
        }
        return false;
    }

    public function EnumerateTags() {
        $i = 0;
        $content = $this->content;
        $start = $this->start;
        do {
            if (!$this->tag) {
                $tagstart = stripos($content, '[', $start);
                $tagend = stripos($content, ']', $tagstart);
                $tag = substr($content, $tagstart, $tagend - $tagstart + 1);
            } else {
                $tag = $this->tag;
            }
            $closetag = '[/'.substr_replace($tag, '', 0, 1);
            $tagopen = stripos($content, $tag, $start);
            $tagclose = stripos($content, $closetag, $tagopen);
            if ($tagclose !== false && $tagopen !== false) {
                $taginfo[$i]['caption'] = substr_replace(substr_replace($tag, '', 0, 1), '', -1, 1);
                $taginfo[$i]['text'] = substr($content, $tagopen + strlen($tag), $tagclose - $tagopen - strlen($tag));
                $taginfo[$i]['start'] = $tagopen;
                $taginfo[$i]['end'] = $tagclose  + strlen($closetag);
                $taginfo[$i]['replacer'] = $tag.$taginfo[$i]['text'].$closetag;
                $i++;
            }
            $start = $tagclose + strlen($tag) + 1;
        } while ($tagopen);
        $this->tagsInfo = $taginfo;
    }

    public function GetTagsInfo() {
        return $this->tagsInfo;
    }

    public function GetIdByCaption($caption = null) {
        if ($caption === null) { return null; }
        for ($i = 0; $i < count($this->tagsInfo); $i++) {
            if ($this->tagsInfo[$i]['caption'] == $caption) { $id[] = $i; }
        }
        return $id;
    }

    public function GetIdByText($text = null) {
        if ($text === null) { return null; }
        for ($i = 0; $i < count($this->tagsInfo); $i++) {
            if ($this->tagsInfo[$i]['text'] == $text) { $id[] = $i; }
        }
        return $id;
    }

    public function GetIdByStart($start = null) {
        if ($start === null) { return null; }
        for ($i = 0; $i < count($this->tagsInfo); $i++) {
            if ($this->tagsInfo[$i]['start'] == $start) { $id[] = $i; }
        }
        return $id;
    }

    public function GetIdByEnd($end = null) {
        if ($end === null) { return null; }
        for ($i = 0; $i < count($this->tagsInfo); $i++) {
            if ($this->tagsInfo[$i]['end'] == $end) { $id[] = $i; }
        }
        return $id;
    }

    public function GetIdByReplacer($replacer = null) {
        if ($replacer === null) { return null; }
        for ($i = 0; $i < count($this->tagsInfo); $i++) {
            if ($this->tagsInfo[$i]['replacer'] == $replacer) { $id[] = $i; }
        }
        return $id;
    }

    public function SetReplacer($replacer, $ids = null) {
        if ($replacer === null) { return false; }
        if ($ids === null) {
            for ($i = 0; $i < count($this->tagsInfo); $i++) {
                $this->tagsInfo[$i]['replacer'] = $replacer;
            }
        } else {
            for ($i = 0; $i < count($this->tagsInfo); $i++) {
                if (!is_array($ids)) { $ids = array($ids); }
                foreach ($ids as $id) {
                    if ($i == $id) $this->tagsInfo[$i]['replacer'] = $replacer;
                }
            }
            unset($id);
        }
    }

    public function ReplaceTags() {
        $content = $this->content;
        $shift = 0;
        if ($this->tag === null) {
            for ($i = 0; $i < count($this->tagsInfo); $i++) {
                // preg_replace ?
                $content = substr_replace($content, $this->tagsInfo[$i]['replacer'], $this->tagsInfo[$i]['start'] + $shift, $this->tagsInfo[$i]['end'] - $this->tagsInfo[$i]['start']);
                $shift = $shift + strlen($this->tagsInfo[$i]['replacer']) - ($this->tagsInfo[$i]['end'] - $this->tagsInfo[$i]['start']);
            }
        } else {
             for ($i = 0; $i < count($this->tagsInfo); $i++) {
                if ('['.$this->tagsInfo[$i]['caption'].']' == $this->tag) {
                    $content = substr_replace($content, $this->tagsInfo[$i]['replacer'], $this->tagsInfo[$i]['start'] + $shift, $this->tagsInfo[$i]['end'] - $this->tagsInfo[$i]['start']);
                    $shift = $shift + strlen($this->tagsInfo[$i]['replacer']) - ($this->tagsInfo[$i]['end'] - $this->tagsInfo[$i]['start']);
                }
            }
        }
        return $content;
    }
}
