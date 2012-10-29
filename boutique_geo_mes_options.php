<?php

// Définition de quelques valeurs de base : pays, langue et devise de l'internaute

include_spip('inc/cookie');
spip_setcookie('spip_test','ok');	
$GLOBALS['nombre_de_logs'] = 100; // 4 fichiers au plus
$GLOBALS['taille_des_logs'] = 1000; // de 100ko au plus


// On teste si le navigateur accepte les cookies, sinon rien	
if($_COOKIE['spip_test']){
	include_spip('inc/correspondances');
	
	$ip =$_SERVER['REMOTE_ADDR'];
	
	//ips de test
	//USA
	//$ip ='209.85.216.179';
	//France
	//$ip= '94.23.227.170'; 
	 //Suisse	
	//$ip= '212.147.58.119';
	
	
	// Definir la langue selon l'origine (pays du visiteur)
	if(!$_COOKIE['spip_lang']) determiner_langue_pays($ip);
		
		
	if(!$_COOKIE['spip_devise']){
		if(!$pays =$_COOKIE['spip_pays'])$pays=ip_pays($ip);	
		determiner_devise_pays($pays);
		}	
	
	}
	
	
//Détermine la devise par rapport au pays
function determiner_devise_pays($pays){
	$devises= pays_devise();
	$devise=$devises[$pays];
	if(!$devise)$devise=lire_config('boutique/devise_default');
	spip_setcookie('spip_devise',$devise,time()+6*40*24*3600);	
}




//Détermine la langue correspondante du pays
function determiner_langue_pays($ip){

	
	// Si un cookie pays existe on récupère sa valeur, sinon on le détermine
	if(!$pays =$_COOKIE['spip_pays'])$pays=ip_pays($ip);

	//On cherche la langue correpondante
	$langues=pays_langue();
	$langue = $langues[$pays];
	
	//Si on ne trouve pas de correpondance on prend la langue par defaut
	if(!$langue) $langue =lire_config('langue_site');
	include_spip('ecrire/inc/lang');
	changer_langue($langue);
	global $spip_lang;
				
	//$self= self();	

	//Si on ne trouve pas de correpondance on prend la langue par defaut

	spip_setcookie('spip_lang', $langue,time()+6*40*24*3600);	
	
	// Si on a réussi à poser un cookie on renvoie
	$destination = parametre_url(self(),'lang',$langue,'&');
	include_spip('inc/headers');	
	redirige_par_entete($destination);
}


//Détermine le code pays

function ip_pays($ip){
	$a=donnees_ip($ip);
	preg_match("/\((.*?)\)/",$a['Country'],$match);
	$pays= $match[1];	
	
	if(!$_COOKIE['spip_pays'])spip_setcookie('spip_pays',$pays,time()+6*40*24*3600);	
	
	$pattern = '/^Unknown/';
	preg_match($pattern, $pays, $matches);
	if($erreur=$matches[0])spip_log("ip: $ip - code_pays:$pays",'detection_pays_erreur');
	else spip_log("ip: $ip - code_pays:$pays",'detection_pays');
	
return $pays;
}


// récupérer les donnes de l'ip

function donnees_ip($ip){

	$url='http://api.hostip.info/get_html.php?ip='.$ip.'&position=true';
 
	$data=file_get_contents($url);

	$a=array();
	$keys=array('Country','City','Latitude','Longitude','IP');
	$keycount=count($keys);
	for ($r=0; $r < $keycount ; $r++)
	{
		$sstr= substr ($data, strpos($data, $keys[$r]), strlen($data));
		if ( $r < ($keycount-1))
			$sstr = substr ($sstr, 0, strpos($sstr,$keys[$r+1]));
		$s=explode (':',$sstr);
		$a[$keys[$r]] = trim($s[1]);
	}
 
return $a;
}
?>