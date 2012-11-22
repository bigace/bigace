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
 * @package addon.smarty
 * @subpackage function
 */ 

import('classes.util.ChartForm');

/**
 * Using templates need to include:
 *
 * <script language="javascript" type="text/javascript" src="../protochart/prototype.js"></script>
 * <script language="javascript" type="text/javascript" src="../protochart/ProtoChart.js"></script>
 * <!--[if IE]><script language="javascript" type="text/javascript" src="../protochart/excanvas.js"></script><![endif]-->
 * 
 * Parameter:
 * - name (string, required)
 * - js (boolean)
 * - type (string: bar, pie, line)
 * - width (int)
 * - height (int) 
 * 
 * For further information visit {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package addon.smarty
 * @subpackage function
 */
function smarty_function_protochart($params, &$smarty)
{
	if(!isset($params['name'])) {
		$smarty->trigger_error("protochart: 'name' attribute is missing");
		return;
	}
	
	$includeJS = (isset($params['js']) ? $params['js'] : true);
	$width = (isset($params['width']) ? $params['width'] : 450);
	$height = (isset($params['height']) ? $params['width'] : 270);
	$type = (isset($params['type']) ? $params['type'] : "pie");
	$chartID = $params['name'];

	static $id;
	if(is_null($id)) {
		$id = 0;
		if($includeJS) {
			echo '
				<script language="javascript" type="text/javascript" src="'._BIGACE_DIR_ADDON_WEB.'charts/prototype.js"></script>
				<script language="javascript" type="text/javascript" src="'._BIGACE_DIR_ADDON_WEB.'charts/ProtoChart.js"></script>
				<!--[if IE]><script language="javascript" type="text/javascript" src="'._BIGACE_DIR_ADDON_WEB.'charts/excanvas.js"></script><![endif]-->
			';
		}
	}
	$id++;

	$FORM = new ChartForm();
	$entries = $FORM->get_generic_entries_by_name($chartID);
?>
	<script language="javascript" type="text/javascript">
		Event.observe(window, 'load', function() {
			new Proto.Chart($('piechart<?php echo $id;?>'), 
			<?php
				echo '
						[ ';
				foreach($entries AS $e) {
					echo ' { data: [['.$id.','.$e['value'].']], label: "'.$e['name'].'"}, ';
				}
				echo '
						], ';

				switch($type) {
					case "bar":
						echo '{
							 	xaxis: {
							 		min: 0,
							 		max: '.count($entries).',
							 		ticks: [ ';
						for($a = 0; $a < count($entries); $a++) {
							echo ' ['.$a.', "'.$entries[$a]["name"].'"], ';
						}
						echo '
								 		]
								 	},
								 	legend: {
										show: true
								 	},
								 	points: {
								 		show: true
								 	},
								 	bars: {
								 		show: true
								 	}
						}';
						break;
					case "line":
						echo '{
							legend: {show: true},
							points: {show: true},
							lines: {show: true}
						}';
						break;
					case "pie":
					default: echo '
						{ pies: {show: true, autoScale: true}, legend: {show: true} }';
						break;
				}
			?>
			);		
		});
	</script>
	<?php
	echo '<div id="piechart'.$id.'" style="width:'.$width.'px;height:'.$height.'px"></div>';
}

?>