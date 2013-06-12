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
    
    echo serialize($a);
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
    
    /*
 $ip = $_SERVER['REMOTE_ADDR']; 
 remember chmod 0777 for folder 'cache' 
$file = "./cache/".$ip; 
if(!file_exists($file)) { 
    // request 
    $json = file_get_contents("http://api.easyjquery.com/ips/?ip=".$ip."&full=true"); 
    $f = fopen($file,"w+"); 
    fwrite($f,$json); 
    fclose($f); 
} else { 
    $json = file_get_contents($file); 
} 

$json = json_decode($json,true); 
echo "<pre>"; 
print_r($json); 
     * 
     * return     [ContinentCode] => NA
    [ContinentName] => North America
    [CountryCode2] => US
    [CountryCode3] => USA
    [COUNTRY] => US
    [CountryName] => United States
    [RegionName] => California
    [CityName] => Garden Grove
    [CityLatitude] => 33.7831
    [CityLongitude] => -118.0271
    [CountryLatitude] => 38
    [CountryLongitude] => -98
    [LocalTimeZone] => America/Los_Angeles
    [REMOTE_ADDR] => 77.93.210.64
    [HTTP_X_FORWARDED_FOR] => 
    [CallingCode] => 1
    [Population] => 307,212,123 (3)
    [AreaSqKm] => 9,826,675 (4)
    [GDP_USD] => 14.26 Trillion (1)
    [Capital] => Washington, D.C.
    [Electrical] => 120 V,60 Hz Type A Type B
    [Languages] => English 82.1%, Spanish 10.7%, other Indo-European 3.8%, Asian and Pacific island 2.7%, other 0.7% (2000 census)
    [Currency] => US Dollar (USD)
    [Flag] => http://api.easyjquery.com/proips/flags/US.jpg
     * 
     * autres .http://chir.ag/projects/geoiploc/
     * 
http://ipinfodb.com/ipinfodb_api_code.php
ipQuery = mysql_query("SELECT * FROM `ip_group_city` where `ip_start` <= INET_ATON('$ip') order by ip_start desc limit 1;", $con);
$ipData = mysql_fetch_array($ipQuery);
$nbResults = (bool)mysql_num_rows($ipQuery);
$geolocationArr['Ip'] = $ip;
if ($nbResults) {
  $geolocationArr['Status'] = 'OK';
} else {
  $geolocationArr['Status'] = 'IP NOT FOUND IN DATABASE, SORRY!';
}                     
$geolocationArr['CountryCode'] = $ipData['country_code'];
$geolocationArr['CountryName'] = utf8_encode($ipData['country_name']);
$geolocationArr['RegionCode'] = $ipData['region_code'];
$geolocationArr['RegionName'] = utf8_encode($ipData['region_name']);
$geolocationArr['City'] = utf8_encode($ipData['city']);
$geolocationArr['ZipPostalCode'] = $ipData['zipcode'];
$geolocationArr['Latitude'] = $ipData['latitude'];
$geolocationArr['Longitude'] = $ipData['longitude'];
 
if (!$newTimezone) {
  $geolocationArr['Timezone'] = $ipData['timezone'];
  $geolocationArr['Gmtoffset'] = $ipData['gmtOffset'];
  $geolocationArr['Dstoffset'] = $ipData['dstOffset'];
} else {
  if ($showNewTimezone) {
    if ($nbResults) {
      mysql_select_db('troquez', $con);
      $tzQuery = mysql_query("SELECT tzd.gmtoff as gmtoff, tzd.isdst as isdst, tz.name as name FROM `timezones_data` tzd JOIN `timezones` tz ON tz.id = tzd.timezone WHERE tzd.timezone = (SELECT `timezone` FROM `fips_regions` WHERE `country_code` = '" . mysql_real_escape_string($ipData['country_code'], $con) . "' AND `code` = '" . mysql_real_escape_string($ipData['region_code'], $con) . "' ) AND tzd.start < UNIX_TIMESTAMP( now( ) ) ORDER BY tzd.start DESC LIMIT 1", $con);
      $tzData = mysql_fetch_array($tzQuery);
    }
    $geolocationArr['TimezoneName'] = $tzData['name'];
    $geolocationArr['Gmtoffset'] = $tzData['gmtoff'];
    $geolocationArr['Isdst'] = $tzData['isdst'];
  }
}
     * 
     */
 
return $a;
}
?>