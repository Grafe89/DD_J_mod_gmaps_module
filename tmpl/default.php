<?php
/**
 * @version    1-1-0-0 // Y-m-d 2017-04-06
 * @author     HR IT-Solutions Florian Häusler https://www.hr-it-solutions.com
 * @copyright  Copyright (C) 2011 - 2017 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
**/

defined('_JEXEC') or die;

JHtml::_('jQuery.Framework');

$app = JFactory::getApplication();
$input = $app->input;

$instance = new ModDD_GMaps_Module_Helper;

$isDDGMapsLocationsExtended = $instance->isDDGMapsLocationsExtended();

$items = $instance->getItems();

$sef_rewrite  = JFactory::getConfig()->get('sef_rewrite');
$active_alias = $app->getMenu()->getActive()->alias;
?>
<div class="dd_gmaps_module">
<?php
// If force_map_size is enabled
if($params->get('force_map_size')){
    $width  = intval($params->get('width')) . 'px';
    $height = intval($params->get('height')) . 'px';
    echo "<style>#dd_gmaps { width: $width; height: $height; }</style>";
}
?>
<script type="text/javascript">
    jQuery( document ).ready(function() { init_default_itemsJS(); });
    var home = new google.maps.LatLng(<?php echo $instance->paramLatLong($params); ?>),
        settingsClusterIcon = '<?php echo $instance->paramClusterMarkerImage($params); ?>',
        settingsZoomLevel = <?php  echo (int) $params->get('zoomlevel') ?>,
        GMapsLocations = [
    <?php
    foreach ( $items as $i => $item ):
        $title = htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');
        if ($isDDGMapsLocationsExtended)
        {
	        $title_link = JRoute::_($sef_rewrite ? $active_alias . '/' . $item->alias : 'index.php?option=com_dd_gmaps_locations&view=profile&profile_id=' . $item->id);
	        $title = '<a href="' . $title_link .'">' . $title .'</a>';
        }
		?>
        {   id:<?php echo isset($item->id) ? $item->id : 0; ?>,
            key:<?php echo $i; ?>,
            lat:<?php echo $item->latitude; ?>,
            lng:<?php echo $item->longitude; ?>,
            icon: '<?php echo $instance->paramMarkerImage($params); ?>',
            content:'<?php echo '<span class="info-content">' . $title . '<br>' . htmlspecialchars($item->street,ENT_QUOTES,'UTF-8') . '<br>' . htmlspecialchars($item->location,ENT_QUOTES,'UTF-8') . '</span>'; ?>'
        },<?php
    endforeach; ?>
    ];
    // Initialize Map
    var infowindow = new google.maps.InfoWindow();
    google.maps.event.addDomListener(window, 'load', initialize);
    <?php
    // Geolocate info window launcher
    if ($input->get("geolocate","STRING") == "locate")
    {
        $locationLatLng = explode(",", $input->get("locationLatLng", "", "STRING"));
        $lat = substr($locationLatLng[0], 0, 10);
        $lng = substr($locationLatLng[1], 0, 10);
        $content = "<h2>" .
            JText::_('MOD_DD_GMAPS_MODULE_YOUR_LOCATION') . "</h2><b>" .
            JText::_('MOD_DD_GMAPS_MODULE_YOUR_LATITUDE') . ":</b> $lat<br><b>" .
            JText::_('MOD_DD_GMAPS_MODULE_YOUR_LONGITUDE') . ":</b> $lng";
        $zoom = 9;
        $markertitle = JText::_('MOD_DD_GMAPS_MODULE_YOUR_LOCATION');
	    $markericon = JUri::base() . 'media/mod_dd_gmaps_module/img/marker_position.png';
        echo "launchLocateInfoWindow($lat,$lng,'$content',$zoom,'$markertitle','$markericon');";
    }

    // Show profile info window
    if ($input->get('profile_id') != 0 || $i == 0)
    {
        echo 'setTimeout(function(){
                var profileObj = jQuery.grep(GMapsLocations, function(e){ return e.id == ' . $input->get('profile_id', $i) .'; });
                launchInfoWindow(profileObj[0].key)
              }, 800);';
    } ?>
</script>
<?php
// Show fullsize
if($params->get('fullsize')): ?>
<div id="dd_gmaps_fullsize" class="pull-left">
    <button onclick="toggleFullSize()" class="btn fullsize-btn"><?php echo JText::_('MOD_DD_GMAPS_MODULE_FULLSIZE'); ?></button>
</div>
<?php endif; ?>
<div id="dd_gmaps">
    <p class="dd_gmaps_loader"><?php echo JText::_('MOD_DD_GMAPS_MODULE_MAPS_PRELOADER'); ?></p>
</div>
<div class="clear"></div>
</div>