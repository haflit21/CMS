<?php
namespace CMS\FrontBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Environment;

class GoogleMapExtension extends Twig_Extension
{

    public function getFilters()
    {
        return array('googleMap' => new Twig_Filter_Method($this, 'googleMapFilter'));
    }

    public function googleMapFilter($value) {
    	
    	preg_match_all('/\[%%map id:(.*), lat:([0-9\.]*), long:(.*)%%\]/', $value, $values, PREG_PATTERN_ORDER);
    	
    	$script_gen = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjpBan6E_y5Ki3Emwsjj4CDcNkMlcsXiM&amp;sensor=false"></script>'.chr(10).chr(13);
	    $script_gen .= '<script type="text/javascript" src="/bundles/cmsfront/js/googlemap.js"></script>'.chr(10).chr(13);
	    $length = count($values[0]);
    	for($i=$length-1; $i >= 0; $i--) {
    		$html = '';
	    	$value_tag = $values[0][$i];

	    	$id_div = $values[1][$i];
	    	$lat = $values[2][$i];
	    	$long = $values[3][$i];
	    	$html .= '<div class="map" id="'.$id_div.'"></div>'.chr(10).chr(13);


	    	
	    	$script = '<script type="text/javascript">'.chr(10).chr(13);
	    	$script .= 'var markersArray = [];';
	    	$script .= 'var map;';
	    	$script .= 'var center = new google.maps.LatLng('.$lat.','.$long.');'.chr(10).chr(13);
	    	$script .= 'var id = "'.$id_div.'";'.chr(10).chr(13);
	    	$script .= 'var myLatLng;'.chr(10).chr(13); 
	    	$script .= '$(document).ready(function() { initialize("'.$id_div.'",new google.maps.LatLng('.$lat.','.$long.')); });'.chr(10).chr(13);
	    	$script .= 'showOverlays();'.chr(10).chr(13);
	    	$script .= '</script>'.chr(10).chr(13);

	    	$html .= $script;
	    	$value = str_replace($value_tag, $html, $value);
    	}
    	$value = $script_gen.$value;
    	return $value;

    }

    public function getName()
    {
        return 'googleMap_extension';
    }
}    