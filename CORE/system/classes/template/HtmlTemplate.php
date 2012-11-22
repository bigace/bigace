<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.<br>Copyright (C) Kevin Papst.
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
 * @subpackage template
 */

/**
 * @access private
 */
define("CODE_OK",         1);
/**
 * @access private
 */
define("CODE_ERROR",     -1);

/**
 * This file was originally taken from PHP 5 Pear.
 * It was customized for BIGACE - now enabled to use callback functions
 * to make it easier to design HTML with automatic translation and styling!
 * 
 * Integrated Template - IT
 *
 * Well there's not much to say about it. I needed a template class that
 * supports a single template file with multiple (nested) blocks inside and
 * a simple block API.
 *
 * The Isotemplate API is somewhat tricky for a beginner although it is the best
 * one you can build. template::parse() [phplib template = Isotemplate] requests
 * you to name a source and a target where the current block gets parsed into.
 * Source and target can be block names or even handler names. This API gives you
 * a maximum of fexibility but you always have to know what you do which is
 * quite unusual for php skripter like me.
 *
 * I noticed that I do not any control on which block gets parsed into which one.
 * If all blocks are within one file, the script knows how they are nested and in
 * which way you have to parse them. IT knows that inner1 is a child of block2, there's
 * no need to tell him about this.
 *
 * <table border>
 *   <tr>
 *     <td colspan=2>
 *       __global__
 *       <p>
 *       (hidden and automatically added)
 *     </td>
 *   </tr>
 *   <tr>
 *     <td>block1</td>
 *     <td>
 *       <table border>
 *         <tr>
 *           <td colspan=2>block2</td>
 *         </tr>
 *         <tr>
 *           <td>inner1</td>
 *           <td>inner2</td>
 *         </tr>
 *       </table>
 *     </td>
 *   </tr>
 * </table>
 *
 * To add content to block1 you simply type:
 * <code>$tpl->setCurrentBlock("block1");</code>
 * and repeat this as often as needed:
 * <code>
 *   $tpl->setVariable(...);
 *   $tpl->parseCurrentBlock();
 * </code>
 *
 * To add content to block2 you would type something like:
 * <code>
 * $tpl->setCurrentBlock("inner1");
 * $tpl->setVariable(...);
 * $tpl->parseCurrentBlock();
 *
 * $tpl->setVariable(...);
 * $tpl->parseCurrentBlock();
 *
 * $tpl->parse("block1");
 * </code>
 *
 * This will result in one repition of block1 which contains two repitions
 * of inner1. inner2 will be removed if $removeEmptyBlock is set to true which is the default.
 *
 * Usage:
 * <code>
 * $tpl = new HTML_Template_IT( [string filerootdir] );
 *
 * // load a template or set it with setTemplate()
 * $tpl->loadTemplatefile( string filename [, boolean removeUnknownVariables, boolean removeEmptyBlocks] )
 *
 * // set "global" Variables meaning variables not beeing within a (inner) block
 * $tpl->setVariable( string variablename, mixed value );
 *
 * // like with the Isotemplates there's a second way to use setVariable()
 * $tpl->setVariable( array ( string varname => mixed value ) );
 *
 * // Let's use any block, even a deeply nested one
 * $tpl->setCurrentBlock( string blockname );
 *
 * // repeat this as often as you need it.
 * $tpl->setVariable( array ( string varname => mixed value ) );
 * $tpl->parseCurrentBlock();
 *
 * // get the parsed template or print it: $tpl->show()
 * $tpl->get();
 * </code>
 *
 * @author   Ulf Wendel <uw@netuse.de>
 * @version  Id: IT.php,v 1.6 2003/03/12 02:25:16 pajoye Exp
 * @access   public
 */
class HtmlTemplate {

    /**
     * @access private
     */
    var $clearCache = false;
    /**
     * @access private
     */
    var $openingDelimiter = "{";
    /**
     * @access private
     */
    var $closingDelimiter     = "}";
    /**
     * @access private
     */
    var $blocknameRegExp    = "[0-9A-Za-z_-]+";
    /**
     * @access private
     */
    var $variablenameRegExp    = "[0-9A-Za-z_-]+";
    /**
     * @access private
     */
    var $variablesRegExp = "";
    /**
     * @access private
     */
    var $removeVariablesRegExp = "";
    /**
     * @access private
     */
    var $removeUnknownVariables = true;
    /**
     * @access private
     */
    var $removeEmptyBlocks = true;
    /**
     * @access private
     */
    var $blockRegExp = "";
    /**
     * @access private
     */
    var $currentBlock = "__global__";
    /**
     * @access private
     */
    var $template = "";
    /**
     * @access private
     */
    var $blocklist = array();
    /**
     * @access private
     */
    var $blockdata = array();
    /**
     * @access private
     */
    var $blockvariables = array();
    /**
     * @access private
     */
    var $blockinner = array();
    /**
     * @access private
     */
    var $touchedBlocks = array();
    /**
     * @access private
     */
    var $_hiddenBlocks = array();
    /**
     * @access private
     */
    var $variableCache = array();
    /**
     * @access private
     */
    var $clearCacheOnParse = false;
    /**
     * @access private
     */
    var $fileRoot = "";
    /**
     * @access private
     */
    var $flagBlocktrouble = false;
    /**
     * @access private
     */
    var $flagGlobalParsed = false;
    /**
     * @access private
     */
    var $flagCacheTemplatefile = true;
    /**
     * @access private
     */
    var $lastTemplatefile = "";
    /**
     * @access private
     */
    var $_options = array(
        'preserve_data' => false,
        'use_preg'      => true
    );
    
    /**
     * @access private
     */
    var $callbackBlocknames = array();

    function setCallbackBlocknames($blockarray) {
      $this->callbackBlocknames = $blockarray;
    }

    function setCallbackBlockname($name, $callback) {
      $this->callbackBlocknames[$name] = $callback;
    }


    /**
     * Builds some complex regular expressions and optinally sets the
     * file root directory.
     *
     * Make sure that you call this constructor if you derive your template
     * class from this one.
     *
     * @param    string    File root directory, prefix for all filenames
     *                     given to the object.
     * @see      setRoot()
     */
    function HtmlTemplate($root = "", $options=null) {
        if(!is_null($options)){
            $this->setOptions($options);
        }
        $this->variablesRegExp = "@" . $this->openingDelimiter .
                                 "(" . $this->variablenameRegExp . ")" .
                                 $this->closingDelimiter . "@sm";
        $this->removeVariablesRegExp = "@" . $this->openingDelimiter .
                                       "\s*(" . $this->variablenameRegExp .
                                       ")\s*" . $this->closingDelimiter ."@sm";

        $this->blockRegExp = '@<!--\s+BEGIN\s+(' . $this->blocknameRegExp .
                             ')\s+-->(.*)<!--\s+END\s+\1\s+-->@sm';

        $this->setRoot($root);
    } // end constructor
    

    /**
     * Sets the file root. The file root gets prefixed to all filenames passed
     * to the object.
     *
     * Make sure that you override this function when using the class
     * on windows.
     */
    function setRoot($root) {

        if ("" != $root && "/" != substr($root, -1))
            $root .= "/";

        $this->fileRoot = $root;

    } // end func setRoot


    /**
     * Sets the option for the template class
     *
     * @access public
     * @param  string  option name
     * @param  mixed   option value
     * @return mixed   CODE_OK on success, error object on failure
     */
    function setOption($option, $value)
    {
        if (isset($this->_options[$option])) {
            $this->_options[$option] = $value;
            return CODE_OK;
        }
        return CODE_ERROR;
    }

    /**
     * Sets the options for the template class
     *
     * @access public
     * @param  string  options array of options
     *                 default value:
     *                   'preserve_data' => false,
     *                   'use_preg'      => true
     * @param  mixed   option value
     * @return mixed   CODE_OK on success, error object on failure
     * @see $options
     */
    function setOptions($options)
    {
        foreach($options as $option=>$value){
          $this->setOption($option, $value);
        }
    }

    /**
     * Print a certain block with all replacements done.
     * @brother get()
     */
    function show($block = "__global__") {
        print $this->get($block);
    } // end func show

    /**
     * Returns a block with all replacements done.
     *
     * @param    string     name of the block
     * @return   string
     * @access   public
     * @see      show()
     */
    function get($block = "__global__") {

        if ("__global__" == $block && !$this->flagGlobalParsed)
            $this->parse("__global__");

        if (!isset($this->blocklist[$block])) {
            return "";
        }

        if (!isset($this->blockdata[$block])) {
            return '';

        } else {
            $ret = $this->blockdata[$block];
            if ($this->clearCache) {
                unset($this->blockdata[$block]);
            }
            if ($this->_options['preserve_data']) {
                $ret = str_replace(
                        $this->openingDelimiter .
                        '%preserved%' . $this->closingDelimiter,
                        $this->openingDelimiter,
                        $ret
                    );
            }
            return $ret;
        }
    } // end func get()

    /**
     * Parses the given block.
     *
     * @param    string    name of the block to be parsed
     * @access   public
     * @see      parseCurrentBlock()
     */
    function parse($block = "__global__", $flag_recursion = false)
    {
        static $regs, $values;

        if (!isset($this->blocklist[$block])) {
        // return error code
            return null;
        }

        if ("__global__" == $block) {
            $this->flagGlobalParsed = true;
        }

        if (!$flag_recursion) {
            $regs   = array();
            $values = array();
        }
        $outer = $this->blocklist[$block];
        $empty = true;

        if ($this->clearCacheOnParse) {

            foreach ($this->variableCache as $name => $value) {
                $regs[] = $this->openingDelimiter .
                          $name . $this->closingDelimiter;
                $values[] = $value;
                $empty = false;
            }
            $this->variableCache = array();

        } else {

            foreach ($this->blockvariables[$block] as $allowedvar => $v) {
                if (isset($this->variableCache[$allowedvar])) {
                   $regs[]   = $this->openingDelimiter .
                               $allowedvar . $this->closingDelimiter;
                               
                   //$values[] = $this->variableCache[$allowedvar];
                   
                   // BIGACE CUSTOMIZING
                   $values[] = $this->variableCache[$allowedvar];
                   $delFromCache = true;

                    $pos = strpos ($allowedvar, "_");
                    if ($pos > 0) { 
                      if (isset($this->callbackBlocknames[substr($allowedvar,0,$pos)])) {
                        $delFromCache = false;
                      }
                    }

                   if ($delFromCache) {
                      unset($this->variableCache[$allowedvar]);
                   }
                   if ($delFromCache || strpos ($block, "auto") !== false) {
                    $empty = false;
                   }
                   // BIGACE CUSTOMIZING
 
                   //unset($this->variableCache[$allowedvar]);
                   //$empty = false;
                }
                

            }

        }

        if (isset($this->blockinner[$block])) {

            foreach ($this->blockinner[$block] as $k => $innerblock) {

                $this->parse($innerblock, true);
                if ("" != $this->blockdata[$innerblock]) {
                    $empty = false;
                }

                $placeholder = $this->openingDelimiter . "__" .
                                $innerblock . "__" . $this->closingDelimiter;
                $outer = str_replace(
                                    $placeholder,
                                    $this->blockdata[$innerblock], $outer
                        );
                $this->blockdata[$innerblock] = "";
            }

        }

        if (!$flag_recursion && 0 != count($values)) {
            if ($this->_options['use_preg']) {
                $regs        = array_map(array(
                                    &$this, '_addPregDelimiters'),
                                    $regs
                                );
                $funcReplace = 'preg_replace';
            } else {
                $funcReplace = 'str_replace';
            }
            if ($this->_options['preserve_data']) {
                $values = array_map(
                            array(&$this, '_preserveOpeningDelimiter'), $values
                        );
            }

            $outer = $funcReplace($regs, $values, $outer);

            if ($this->removeUnknownVariables) {
                $outer = preg_replace($this->removeVariablesRegExp, "", $outer);
            }
        }
        if ($empty) {

            if (!$this->removeEmptyBlocks) {

                $this->blockdata[$block ].= $outer;

            } else {

                if (isset($this->touchedBlocks[$block])) {
                    $this->blockdata[$block] .= $outer;
                    unset($this->touchedBlocks[$block]);
                }

            }

        } else {

            $this->blockdata[$block] .= $outer;

        }

        return $empty;
    } // end func parse

    /**
     * Parses the current block
     * @see      parse(), setCurrentBlock(), $currentBlock
     * @access   public
     */
    function parseCurrentBlock() {
        return $this->parse($this->currentBlock);
    } // end func parseCurrentBlock

    /**
     * Sets a variable value.
     *
     * The function can be used eighter like setVariable( "varname", "value")
     * or with one array $variables["varname"] = "value"
     * given setVariable($variables) quite like phplib templates set_var().
     *
     * @param    mixed     string with the variable name or an array
     *                     %variables["varname"] = "value"
     * @param    string    value of the variable or empty if $variable
     *                     is an array.
     * @param    string    prefix for variable names
     * @access   public
     */
    function setVariable($variable, $value = "") {

        if (is_array($variable)) {

            $this->variableCache = array_merge(
                                            $this->variableCache, $variable
                                    );

        } else {

            $this->variableCache[$variable] = $value;

        }

    } // end func setVariable

    /**
     * Sets the name of the current block that is the block where variables
     * are added.
     *
     * @param    string      name of the block
     * @return   boolean     false on failure, otherwise true
     * @access   public
     */
    function setCurrentBlock($block = "__global__") {

        if (!isset($this->blocklist[$block])) {
            return CODE_ERROR;
        }

        $this->currentBlock = $block;

        return CODE_OK;
    } // end func setCurrentBlock

    /**
     * Preserves an empty block even if removeEmptyBlocks is true.
     *
     * @param    string      name of the block
     * @return   boolean     false on false, otherwise true
     * @access   public
     * @see      $removeEmptyBlocks
     */
    function touchBlock($block) {

        if (!isset($this->blocklist[$block])) {
            return CODE_ERROR;
        }

        $this->touchedBlocks[$block] = true;

        return true;
    } // end func touchBlock

    /**
     * Clears all datafields of the object and rebuild the internal blocklist
     *
     * LoadTemplatefile() and setTemplate() automatically call this function
     * when a new template is given. Don't use this function
     * unless you know what you're doing.
     *
     * @access   public
     * @see      free()
     */
    function init() {

        $this->free();
        $this->findBlocks($this->template);
        // we don't need it any more
        $this->template = '';
        $this->buildBlockvariablelist();

    } // end func init

    /**
     * Clears all datafields of the object.
     *
     * Don't use this function unless you know what you're doing.
     *
     * @access   public
     * @see      init()
     */
    function free() {

        $this->currentBlock = "__global__";

        $this->variableCache    = array();
        $this->blocklookup      = array();
        $this->touchedBlocks    = array();

        $this->flagBlocktrouble = false;
        $this->flagGlobalParsed = false;

    } // end func free

    /**
     * Sets the template.
     *
     * You can eighter load a template file from disk with
     * LoadTemplatefile() or set the template manually using this function.
     *
     * @param        string      template content
     * @param        boolean     remove unknown/unused variables?
     * @param        boolean     remove empty blocks?
     * @see          LoadTemplatefile(), $template
     * @access       public
     */
    function setTemplate( $template, $removeUnknownVariables = true,
                          $removeEmptyBlocks = true
    ) {

        $this->removeUnknownVariables = $removeUnknownVariables;
        $this->removeEmptyBlocks = $removeEmptyBlocks;

        if ("" == $template && $this->flagCacheTemplatefile) {

            $this->variableCache = array();
            $this->blockdata = array();
            $this->touchedBlocks = array();
            $this->currentBlock = "__global__";

        } else {

            $this->template = '<!-- BEGIN __global__ -->' . $template .
                              '<!-- END __global__ -->';
            $this->init();

        }

        if ($this->flagBlocktrouble)
            return false;

        return true;
    } // end func setTemplate

    /**
     * Reads a template file from the disk.
     *
     * @param    string      name of the template file
     * @param    bool        how to handle unknown variables.
     * @param    bool        how to handle empty blocks.
     * @access   public
     * @return   boolean    false on failure, otherwise true
     * @see      $template, setTemplate(), $removeUnknownVariables,
     *           $removeEmptyBlocks
     */
    function loadTemplatefile( $filename,
                               $removeUnknownVariables = true,
                               $removeEmptyBlocks = true ) {

        $template = "";
        if (!$this->flagCacheTemplatefile ||
            $this->lastTemplatefile != $filename
        ){
            $template = $this->getfile($filename);
        }
        $this->lastTemplatefile = $filename;

        return $template!=""?
                $this->setTemplate(
                        $template,$removeUnknownVariables, $removeEmptyBlocks
                    ):false;
    } // end func LoadTemplatefile

    /**
     * Build a list of all variables within of a block
     */
    function buildBlockvariablelist() {

        foreach ($this->blocklist as $name => $content) {
            preg_match_all( $this->variablesRegExp, $content, $regs );

            if (0 != count($regs[1])) {

                foreach ($regs[1] as $k => $var) {
                  $this->blockvariables[$name][$var] = true;

                  // BIGACE CUSTOMIZING !!!!
                  $pos = strpos ($var, "_");
                  if ($pos > 0) { 
                    $val = substr($var,0,$pos);
                    if (isset($this->callbackBlocknames[$val])) {
                      $this->setVariable($var, $this->callbackBlocknames[$val](substr($var,$pos+1)));
                    }
                  }
                  // BIGACE CUSTOMIZING !!!!
                }
            } else {
                $this->blockvariables[$name] = array();

            }
        }
    } 

    /**
     * Returns a list of all global variables
     */
    function getGlobalvariables() {

        $regs   = array();
        $values = array();

        foreach ($this->blockvariables["__global__"] as $allowedvar => $v) {

            if (isset($this->variableCache[$allowedvar])) {
                $regs[]   = "@" . $this->openingDelimiter .
                            $allowedvar . $this->closingDelimiter."@";
                $values[] = $this->variableCache[$allowedvar];
                unset($this->variableCache[$allowedvar]);
            }

        }

        return array($regs, $values);
    } // end func getGlobalvariables

    /**
     * Recusively builds a list of all blocks within the template.
     *
     * @param    string    string that gets scanned
     * @see      $blocklist
     */
    function findBlocks($string) {

        $blocklist = array();

        if (
            preg_match_all($this->blockRegExp, $string, $regs, PREG_SET_ORDER)
        ) {

            foreach ($regs as $k => $match) {

                $blockname         = $match[1];
                $blockcontent = $match[2];

                if (isset($this->blocklist[$blockname])) {
                    $this->flagBlocktrouble = true;
                }

                $this->blocklist[$blockname] = $blockcontent;
                $this->blockdata[$blockname] = "";

                $blocklist[] = $blockname;

                $inner = $this->findBlocks($blockcontent);
                foreach ($inner as $k => $name) {

                    $pattern = sprintf(
                        '@<!--\s+BEGIN\s+%s\s+-->(.*)<!--\s+END\s+%s\s+-->@sm',
                        $name,
                        $name
                    );

                    $this->blocklist[$blockname] = preg_replace(
                                        $pattern,
                                        $this->openingDelimiter .
                                        "__" . $name . "__" .
                                        $this->closingDelimiter,
                                        $this->blocklist[$blockname]
                               );
                    $this->blockinner[$blockname][] = $name;
                    $this->blockparents[$name] = $blockname;

                }

            }

        }

        return $blocklist;
    } // end func findBlocks

    /**
     * Reads a file from disk and returns its content.
     * @param    string    Filename
     * @return   string    Filecontent
     */
    function getFile($filename) {

        if ("/" == $filename{0} && "/" == substr($this->fileRoot, -1))
            $filename = substr($filename, 1);

        $filename = $this->fileRoot . $filename;

        if (!($fh = @fopen($filename, "r"))) {
        	$GLOBALS['LOGGER']->logError("Could not find Template: " . $filename);
            return "";
        }

        $content = fread($fh, filesize($filename));
        fclose($fh);

        return preg_replace(
            "#<!-- INCLUDE (.*) -->#ime", "\$this->getFile('\\1')", $content
        );
    } // end func getFile


    /**
     * Adds delimiters to a string, so it can be used as a pattern
     * in preg_* functions
     */
    function _addPregDelimiters($str)
    {
        return '@' . $str . '@';
    }


   /**
    * Replaces an opening delimiter by a special string
    */
    function _preserveOpeningDelimiter($str)
    {
        return (false === strpos($str, $this->openingDelimiter))?
                $str:
                str_replace(
                    $this->openingDelimiter,
                    $this->openingDelimiter .
                    '%preserved%' . $this->closingDelimiter,
                    $str
                );
    }

} // end class IntegratedTemplate
?>