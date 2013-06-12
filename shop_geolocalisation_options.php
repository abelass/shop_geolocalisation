<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Définition de quelques valeurs de base : pays, langue et devise de l'internaute

// On teste si le navigateur accepte les cookies, sinon rien	

if($_COOKIE['geo_test']){
    include_spip('inc/ip_pays');
    lancer_geolocalisation();
}

?>