<?php
/**
 * Utilisations de pipelines par Sho Geolocalisation
 *
 * @plugin     Shop Geolocalisation
 * @copyright  2013
 * @author     Rainer MÜller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_geolocalisation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function shop_geolocalisation_insert_head($flux){
    include_spip('inc/cookie');    
    if(!$_COOKIE['geo_pays']){
    $flux .= <<<EOF
<script>
<!--
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        if(!$.cookie('geo_pays')){
            $.getJSON('http://ws.geonames.org/countryCode', {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                type: 'JSON'
            }, function(result) {load('/spip.php?action=geolocalisation&pays='+result.countryCode','',function(){
                        $.cookie('geo_pays',result.countryCode, { expires: 2592000});
                        $.cookie('geo_test','ok', { expires: 2592000});            
                        }            
                    );     

                }
                
             ); 
 
        }
    });
}
else{load('/spip.php?action=geolocalisation&pays='+result.countryCode','',function(){
                        $.cookie('geo_pays',result.countryCode, { expires: 2592000});
                        $.cookie('geo_test','ok', { expires: 2592000});            
                        }            
                    );  
}
-->     
</script>

EOF;
    }
    return $flux;
}

?>