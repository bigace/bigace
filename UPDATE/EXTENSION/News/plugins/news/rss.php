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
 */

/**
 * This script displays the latest created News, by simply loading and parsing 
 * the configured News-RSS Template.
 * Handling it this way, allows the User to change the Feed via Administration.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 */

require_once(dirname(__FILE__).'/../../system/libs/init_session.inc.php');

// load required smarty classes
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');

header('Content-type: application/rss+xml');

// the template we use to render the rss feed
$tpl = ConfigurationReader::getConfigurationValue('news', 'rss.latest.template', 'News-RSS-Latest');
$SMARTY_TPL = new SmartyTemplate($tpl); 

// for correct date formatting required!
setlocale(LC_TIME, "en_EN");

// initialize smarty and siplay the feed
$smarty = BigaceSmarty::getSmarty();
$smarty->display($SMARTY_TPL->getFilename());

?>