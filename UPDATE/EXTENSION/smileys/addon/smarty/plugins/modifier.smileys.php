<?php

import('classes.parser.Smileys');

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     smileys
 * 
 * Parses gien code an replaces smileys like ;) and :smile: into
 * working HTML <img src=""> TAGs.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * -------------------------------------------------------------
 * Parameter:
 * 'username' = for statistic purpose
 * 'link'     = the URL to be saved
 * 'title     = name for the link
 */
function smarty_modifier_smileys($code, $textual = false)
{
	return Smileys::parseCode($code, $textual);
}

?>