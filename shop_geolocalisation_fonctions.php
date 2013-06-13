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
	
function pays_defaut(){
      include_spip('inc/config');
      $code_pays=lire_config('shop_geolocalisation/pays_defaut','BE');
    
    return $code_pays;
}

?>