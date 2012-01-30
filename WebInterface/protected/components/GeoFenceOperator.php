<?php 

class GeoFenceOperator {
	
	/*
	 * geofence[i]['latitude']
	 * geofence[i]['longitude']
	 * 
	 * point['latitude'],
	 * point['longitude'] 
	 * */
	public static function  isGeoFenceContainsLocation($geoFence, $point) {

		// Raycast point in polygon method
		$numPoints = count($geoFence); //MAP_OPERATOR.getPointNumberOfGeoFencePath(geoFence);
		$inPoly = false;
		$j = $numPoints-1;
		
		for($i = 0; $i < $numPoints; $i++) 
		{ 
			if ($geoFence[$i]['longitude'] < $point['longitude'] 
				&& $geoFence[$j]['longitude'] >= $point['longitude'] 
				|| $geoFence[$j]['longitude'] < $point['longitude'] 
				&& $geoFence[$i]['longitude']  >= $point['longitude'])	
			 {
				if ($geoFence[$i]['latitude'] + ($point['longitude'] - $geoFence[$i]['longitude']) / ($geoFence[$j]['longitude'] - $geoFence[$i]['longitude']) * ($geoFence[$j]['latitude'] - $geoFence[$i]['latitude']) <$point['latitude']) {
					$inPoly = !$inPoly;
				}
			}
			$j = $i;
		}
		return $inPoly;
	}
}
/*
$geoFence = array(
					array('latitude'=>38.445388,
						 'longitude'=>-85.341797),
					array('latitude'=>35.353216,
						 'longitude'=>-94.438477),
					array('latitude'=>41.228249,
						 'longitude'=>-96.031494),
				);
$point = array('latitude'=>35.35326,
				'longitude'=>-94.43846);

if (GeoFenceOperator::isGeoFenceContainsLocation($geoFence, $point))
{
	echo "true";
}
else 
{
	echo "false";
}
*/



?>