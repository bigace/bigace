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
 */ 

/**
 * The News-Map module.
 *
 * For further information go to http://www.bigace.de/ 
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Kevin Papst 
 * @copyright Copyright (C) Kevin Papst
 * @version $Id$
 * @package bigace.addon
 * @subpackage news
 */
	
	import('classes.item.ItemProjectService');
	import('classes.image.Image');
	import('classes.image.ImageService');
	import('classes.modul.ModulService');
	import('classes.modul.Modul');
	import('classes.util.LinkHelper');
	import('classes.util.links.ThumbnailLink');
	import('classes.news.News');
		
	$modulService = new ModulService();
	$modul = new Modul($MENU->getModulID());
	
	// #########################################################################
	// To be a Modul Admin you need to own the permission: News-Map.configure
	// See modul.ini file for further infos
	if ($modul->isModulAdmin())
	{
	    import('classes.util.links.ModulAdminLink');
	    import('classes.util.LinkHelper');
	    $mdl = new ModulAdminLink();
	    $mdl->setItemID($MENU->getID());
	    $mdl->setLanguageID($MENU->getLanguageID());
		$adminUrl = LinkHelper::getUrlFromCMSLink($mdl);
	    ?>
	    <script type="text/javascript">
	    <!--
	    function openAdmin()
	    {
	        fenster = open("<?php echo $adminUrl; ?>","ModulAdmin","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=350,width=400,screenX=0,screenY=0");
	        bWidth=screen.width;
	        bHeight=screen.height;
	        fenster.moveTo((bWidth-400)/2,(bHeight-350)/2);
	    }
	    // -->
	    </script>
	    <?php
	
	    echo '<div class="modulAdminLink" align="left"><a onClick="openAdmin(); return false;" href="'.$adminUrl.'"><img src="'._BIGACE_DIR_PUBLIC_WEB.'system/images/preferences.gif" border="0" align="top">Modul Admin</a></div>';
	}
	// #########################################################################

	$apiKey 	= ConfigurationReader::getValue("news", "google.api.key");
	
	// evaluate this modules related configuration
	$config 	= $modulService->getModulProperties($MENU, $modul, array());
	$cntLat 	= $config['Map_Cnt-Lat'];
	$cntLng 	= $config['Map_Cnt-Lng'];
	$mapZoom 	= $config['Map_Zoom'];
	$mapCats 	= $config['Map_News-Cat'];
	$newsLimit 	= $config['Map_News-Amount'];
	$mapWidth 	= $config['Map_Width'];
	$mapHeight	= $config['Map_Height'];
	$mapWhZoom	= (bool)$config['Map_Wheel-Zoom'];
	
	$newsRootID = ConfigurationReader::getConfigurationValue("news", "root.id");
	$newsLang = ConfigurationReader::getConfigurationValue("news", "default.language");
	$from = 0;
	
	$categories = array();
	
	// if a news category was configured, apply it
	if(!is_null($mapCats) && strlen(trim($mapCats) > 0))
		$categories[] = $mapCats;
	
	if(is_null($newsRootID)) {
		die("news: Configuration news/root.id not set");
	}

	// define, which news will be requested
	$ir = new ItemRequest(_BIGACE_ITEM_MENU);
	$ir->setID($newsRootID);
	$ir->setLanguageID($newsLang);
	$ir->setReturnType("News");
	$ir->setOrderBy("date_2");
	$ir->setOrder('DESC');
	
	// amount of items to be fetched
	if($newsLimit != null && $newsLimit != 0)
		$ir->setLimit($from, $newsLimit);

    // only for special categories?!	
	if(count($categories) > 0) {
		foreach($categories AS $x)	{
			$ir->setCategory($x);
		} 		
	}
	
	// finally fetch all items
	$menu_info = new SimpleItemTreeWalker($ir);

    // and move them from the enumeration to a array
	$items = array();
    for ($i=0; $i < $menu_info->count(); $i++) {
        $temp = $menu_info->next();
		$items[] = $temp;
    }
        
?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $apiKey; ?>" type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[

    var map = null;
    var geocoder = null;
    var points = new Array();

    function load() 
    {
      if (GBrowserIsCompatible()) 
      {
        map = new GMap2(document.getElementById("map"));
        map.setCenter(new GLatLng(<?php echo $cntLat; ?>,<?php echo $cntLng; ?>), <?php echo $mapZoom; ?>);
        map.addControl(new GMapTypeControl());
        map.addControl(new GLargeMapControl());
        <?php if($mapWhZoom) { ?>
        map.enableScrollWheelZoom();
        <?php } ?>

        geocoder = new GClientGeocoder();
        var bounds = map.getBounds();
        var southWest = bounds.getSouthWest();
        var northEast = bounds.getNorthEast();
        var lngSpan = northEast.lng() - southWest.lng();
        var latSpan = northEast.lat() - southWest.lat();

        <?php
        $ips = new ItemProjectService(_BIGACE_ITEM_MENU);

        foreach($items AS $newsMarker) 
        {
            if($ips->existsProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_width') && 
               $ips->existsProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_length')) 
            {
                $width = $ips->getProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_width');          
                $length = $ips->getProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_length');
                if(strlen($width) > 0 && strlen($length) > 0) 
                {
                ?> 
                points["<?php echo $newsMarker->getID(); ?>"] = new GLatLng(<?php echo $width; ?>, <?php echo $length; ?>);
                showNews2(points["<?php echo $newsMarker->getID(); ?>"], "<?php echo $newsMarker->getID(); ?>");
                <?php
                }
            }
        }
        ?>        
      }
    }

    function showNews2(point,newsID) {
        var marker = new GMarker(point);
        map.addOverlay(marker);
        GEvent.addListener(marker, "mouseover", function() {
            var foo = document.getElementById('news'+newsID).innerHTML;
            map.openInfoWindowHtml(point, foo.toString());
        });
    }
    //]]>
    </script>

    <table border="0" cellspacing="6" cellpadding="0" width="100%">
    <tr>
        <td width="200" valign="top">
        <div class="shortiesOuter" style="height: <?php echo $mapHeight; ?>;">
        <div id="shorties" style="height: <?php echo $mapHeight; ?>">
        <?php
          foreach($items AS $newsMarker) 
          {
                if($ips->existsProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_width') && 
                   $ips->existsProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_length')) 
                {
                    $width = $ips->getProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_width');          
                    $length = $ips->getProjectText($newsMarker->getID(), $newsMarker->getLanguageID(), 'gps_length');
                    if(strlen($width) > 0 && strlen($length) > 0) 
                    {          
                        echo "<h1>".$newsMarker->getTitle()."</h1>";
                        echo "<p>".$newsMarker->getTeaser().'<br/><a href="#" onclick="map.panTo(points[\''.$newsMarker->getID().'\'])">Anzeigen</a></p>';
                        echo "<hr/>";
                    }
                }
          }
        ?>
        </div>
        </div>
        </td>
        <td valign="top">
            <div id="map" style="width: <?php echo $mapWidth; ?>; height: <?php echo $mapHeight; ?>"></div>
        </td>
    </tr>
    </table>

    <script type="text/javascript">
    //<![CDATA[
        window.onload = function() {
        load();
        }

        window.onunload = function() {
        GUnload();
        }
    //]]>
    </script>
    
    <div class="newsMapEntries">
          <?php
          $is = new ImageService();
          foreach($items AS $newsEntry) 
          {
            $newsURL = LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem($newsEntry));
            $thumbURL = null;	
    		if(!is_null($newsEntry->getImageID()) || strlen($newsEntry->getImageID()) > 0) 
    		{
    		    $newsImage = $is->getClass($newsEntry->getImageID());
    		    if($newsImage->exists()) {
        		    $thumbLink = new ThumbnailLink(); 
        		    $thumbLink->setWidth("100");
        		    $thumbLink->setHeight("150");
                    $thumbLink->setItemID($newsImage->getID());
                    $thumbLink->setLanguageID($newsImage->getLanguageID());
                    $thumbLink->setUniqueName($newsImage->getUniqueName());
        		    $thumbURL = LinkHelper::getUrlFromCMSLink($thumbLink);
        		}
    		}
            ?>
            
	    	<div id="news<?php echo $newsEntry->getID(); ?>">
	    		<div class="newsMarker"><?php 
	    		    if(!is_null($thumbURL)) {
	    		    echo '<img src="'.$thumbURL.'" alt="" title="'.$newsEntry->getTitle().'" />';
    	    		}
	    		?>
	    		    <h2><a href="<?php echo $newsURL; ?>" title="<?php echo $newsEntry->getTitle(); ?>"><?php echo $newsEntry->getTitle(); ?></a></h2>
	    			<p><?php echo $newsEntry->getTeaser(); ?></p>
	    			<p class="articleLink"><a href="<?php echo $newsURL; ?>" title="<?php echo $newsEntry->getTitle(); ?>">Vollständigen Artikel lesen</a></p>
	    		</div>
	    	</div>
	    	
            <?php
          }
          ?>        
    </div>
    