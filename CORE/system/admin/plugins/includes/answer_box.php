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
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 */

loadClass('administration', 'AnswerViewDefinition');


/**
 * Display a Short Answer Box to the User, providing some infos 
 * what happended, and where to go in the next step.
 * 
 * @param String the Title of this Answer
 * @param array an Array with Key Value pairs
 * @param String the Link to next Page
 * @param array Key-Value Pairs for hidden input types
 * @param String the Title of the Submit Button (default Translation for 'next')
 * @param String Icon name in the Style directory (default null)
 * 
 * @deprecated use <code>displayAnswerViewDefinition</code> instead
 */
function displayAnswer($title, $message, $link, $hidden = array(), $submitLabel = '', $icon = null)
{
	$answerDef = new AnswerViewDefinition();
	$answerDef->setStateIcon('success.png');
	$answerDef->setTitle($title);
	$answerDef->setLink($link);
	$answerDef->setMessageValues($message);
	$answerDef->setHiddenValues($hidden);
	
    if ($submitLabel != '') {
    	$answerDef->setButtonLabel($submitLabel);
    }

    if ($icon != '') {
    	$answerDef->setTitleIcon($icon);
    }
    
	displayAnswerViewDefinition($answerDef);
}

/**
 * Display a Answer Box using the AnswerViewDefinition Class as Input Parameter.
 * @param AnswerViewDefinition definition of the answer
 */
function displayAnswerViewDefinition($definition)
{
	if (is_object($definition) && strcasecmp(get_class($definition), 'AnswerViewDefinition') == 0) 
	{
		// load template
	    $tpl = $GLOBALS['TEMPLATE_SERVICE']->loadTemplatefile("AdminFeedbackView.tpl.html", false, true);
		
        if($definition->getTitleIcon() != null) {
            $tpl->setVariable("TITLE_ICON", $GLOBALS['_BIGACE']['style']['DIR'].$definition->getTitleIcon());
        } else {
            $tpl->setVariable("TITLE_ICON", _BIGACE_DIR_PUBLIC_WEB.'system/images/empty.gif');
        }

        if($definition->getStateIcon() != null) {
		  $tpl->setVariable("STATE_ICON", $GLOBALS['_BIGACE']['style']['DIR'].$definition->getStateIcon());
        } else {
            $tpl->setVariable("STATE_ICON", _BIGACE_DIR_PUBLIC_WEB.'system/images/empty.gif');
        }
          
		$tpl->setVariable("LINK", $definition->getLink());
		$tpl->setVariable("TITLE", $definition->getTitle());
		$tpl->setVariable("BUTTON_TITLE", $definition->getButtonLabel());
		
		foreach($definition->getHiddenValues() AS $key => $value)
		{
			$tpl->setCurrentBlock("hidden");
			$tpl->setVariable("KEY", $key);
			$tpl->setVariable("VALUE", $value);
			$tpl->parseCurrentBlock("hidden");
		}
	
		foreach($definition->getMessageValues() AS $name => $value)
		{
			$tpl->setCurrentBlock("entryRow");
			$tpl->setVariable("NAME", $name);
			$tpl->setVariable("ENTRY", $value);
			$tpl->parseCurrentBlock("entryRow");
		}
	
		$tpl->show(); 		
	}
	else 
	{
		$GLOBALS['LOGGER']->logError('Tried to call displayAnswerViewDefinition() with wrong Parameter Type!');
	}
}

?>