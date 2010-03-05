<?php
/**
 * Google Maps v3
 * Display the map based on the data passed.
 *
 * @package	GoogleMap Helper
 * @author	Author: Chuck Burgess <cdburgess@gmail.com>
 * @license	License: http://creativecommons.org/licenses/by-sa/3.0/
 * @copyright	Copyright: (c)2010 Chuck Burgess. All Rights Reserved.
 *
 * @internal GoogleMaps V3
 * 
 * - We use the first Lat/Lon coordinates as the centering coordinates
 * - All points are passed in an array of arrays
 *	- name
 *	- lat
 *	- lon
 * @example Minimum Requirements (will also handle street, city, state, zipcode, & description)
 * $points = array(
 *	[0] => array(
 *		'name' => 'My Point 1',
 *		'latitude' => '{lat_coordinate}',
 *		'longitude' => '{lon_coordinate'},
 *	),
 *	[1] => array(
*		'name' => 'My Point 2',
 *		'latitude' => '{lat_coordinate}',
 *		'longitude' => '{lon_coordinate'},
 *	),
 * );
 */
class GoogleMapHelper extends Helper
{
	/**
 	* Other helpers used by GoogleMapHelper
 	*
 	* @var array
 	* @access public
 	*/
	var $helpers = array('Html','Javascript');
	
	/**
	 * Googl Maps base URL
	 *
	 * @var string
	 * @access public
	 */
	private $base_url = "http://maps.google.com/maps";
	
	/**
	 * ShowMap
	 *
	 * Will display a map on the webpage in the location you call it.
	 *
	 * @param array $points		Each point on the map you want to put a marker
	 * @param array $style		Formatting information for the map
	 * @access public
	 */
	function show_map($points, $api_key = null, $style = array('zoom' => 14, 'width' => '500px', 'height' => '300px', 'class' => 'map', 'id' => 'map_canvas', 'map_type' => 'ROADMAP'))
	{
		// if multiple points are set, map them
		if(is_array($points))
		{
			$count = 0;
			
			// process each item in the array
			foreach($points as $marker)
			{	
				$count ++;
				$content = '<b>'.$marker['name'].'</b><br />'.$marker['street'].'<br />'.$marker['city'].', '.$marker['state'].' '.$marker['zipcode'].'<br /><br />'.$marker['description'];
				$markers[] = "['".$content ."', ".$marker['latitude'].", ".$marker['longitude'].", ".$count."]";
			}
			// build the points to mark on the map
			$point_data = 'var points = [ '.implode(",",$markers).'];';
			
			// set the style data for the map
			$style_data = 'width:'.$style['width'].'; height:'.$style['height'].';';
			
			// set the javascript for google maps
			$map = $this->Javascript->link($this->base_url.'/api/js?sensor=false');
			$map .= '
			<script type="text/javascript">
				var infowindow = new google.maps.InfoWindow;
				(function () {
				  google.maps.Map.prototype.markers = new Array();
				  google.maps.Map.prototype.addMarker = function(marker) {
					this.markers[this.markers.length] = marker;
				  };
				  google.maps.Map.prototype.getMarkers = function() {
					return this.markers
				  };
				  google.maps.Map.prototype.clearMarkers = function() {
					if(infowindow) {
					  infowindow.close();
					}
					
					for(var i=0; i<this.markers.length; i++){
					  this.markers[i].set_map(null);
					}
				  };
				})();

				var map;
			  
				'.$point_data.'

				function initialize() {
				      var myOptions = {
					zoom: '.$style['zoom'].',
					center: new google.maps.LatLng('.$points[0]['latitude'].','.$points[0]['longitude'].'),
					mapTypeId: google.maps.MapTypeId.'.$style['map_type'].'
				      };
				      
				      map = new google.maps.Map(document.getElementById("'.$style['id'].'"), myOptions);
				      
				      for (var i = 0; i < points.length; i++) {
					      var point = points[i];
					      var myLatLng = new google.maps.LatLng(point[1], point[2]);
					      map.addMarker(createMarker(point[0], myLatLng));
					      
				      }
				      console.log(map.getMarkers());
				}
			  
				function createMarker(name, myLatLng) {
				      var marker = new google.maps.Marker({
					position: myLatLng,
					map: map
				      });
				      google.maps.event.addListener(marker, "click", function() {
					if (infowindow) infowindow.close();
					infowindow = new google.maps.InfoWindow({content: name});
					infowindow.open(map, marker);
				      });
				      return marker;
				}
				window.onload = initialize();
			</script>
			';
			
			$map = $this->Html->div($style['class'], $map, array('id' => $style['id'], 'style' => $style_data));
		}
		return $map;
	}

}
?>
