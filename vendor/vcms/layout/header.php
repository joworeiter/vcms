<?php
echo '<!DOCTYPE html>' . PHP_EOL;
echo '<html lang="de">' . PHP_EOL;
echo '  <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# business: http://ogp.me/ns/business#">' . PHP_EOL;
echo '    <meta charset="utf-8" />' . PHP_EOL;
echo '    <meta http-equiv="X-UA-Compatible" content="IE=edge" />' . PHP_EOL;
echo '    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">' . PHP_EOL;
echo '    <title>' .getPageTitle(). '</title>' . PHP_EOL;
echo '    <meta name="description" content="' .$libConfig->seiteBeschreibung. '" />' . PHP_EOL;
echo '    <meta name="keywords" content="' .$libConfig->seiteKeywords. '" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/bootstrap-override.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/screen.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/calendar/calendar.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/gallery/gallery.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/navigation/navigation.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/person/person.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="vendor/vcms/styles/timeline/timeline.css" />' . PHP_EOL;
echo '    <link rel="stylesheet" href="custom/styles/screen.css" />' . PHP_EOL;
echo '    <link rel="canonical" href="' .getPageCanonicalUrl(). '"/>' . PHP_EOL;
echo '    <script src="vendor/jquery/jquery.min.js"></script>' . PHP_EOL;
echo '    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>' . PHP_EOL;
echo '    <script src="vendor/vcms/styles/gallery/modal.js"></script>' . PHP_EOL;
echo '    <script src="vendor/vcms/styles/screen.js"></script>' . PHP_EOL;

if(is_array($libGlobal->module->getHeaderStrings())){
	foreach($libGlobal->module->getHeaderStrings() as $headerString){
		echo '    ' .$headerString. PHP_EOL;
	}
}

/*
* robots
*/
if($libGlobal->page->hasAccessRestriction()){
	echo '    <meta name="robots" content="noindex, nofollow, noarchive" />' . PHP_EOL;
} else {
	echo '    <meta name="robots" content="index, follow, noarchive" />' . PHP_EOL;
}

/*
* Opengraph / Facebook meta data
*/
if($libGenericStorage->loadValue('base_core', 'fbAppId')){
	echo '    <meta property="fb:app_id" content="' .$libGenericStorage->loadValue('base_core', 'fbAppId'). '"/>' . PHP_EOL;
}

echo '    <meta property="og:type" content="business.business"/>' . PHP_EOL;
echo '    <meta property="og:url" content="' .getPageOgUrl(). '"/>' . PHP_EOL;
echo '    <meta property="og:title" content="' .getPageTitle(). '"/>' . PHP_EOL;
echo '    <meta property="og:image" content="' .$libGlobal->getSiteUrl(). '/custom/styles/og_image.jpg"/>' . PHP_EOL;
echo '    <meta property="og:image:type" content="image/jpeg" />' . PHP_EOL;
echo '    <meta property="og:image:height" content="265"/>' . PHP_EOL;
echo '    <meta property="og:image:width" content="265"/>' . PHP_EOL;
echo '    <meta property="og:site_name" content="' .$libGlobal->getSiteUrlAuthority(). '"/>' . PHP_EOL;
echo '    <meta property="og:description" content="' .$libConfig->seiteBeschreibung. '"/>' . PHP_EOL;
echo '    <meta property="business:contact_data:street_address" content="' .$libConfig->verbindungStrasse. '"/>' . PHP_EOL;
echo '    <meta property="business:contact_data:locality" content="' .$libConfig->verbindungOrt. '"/>' . PHP_EOL;
echo '    <meta property="business:contact_data:postal_code" content="' .$libConfig->verbindungPlz. '"/>' . PHP_EOL;
echo '    <meta property="business:contact_data:country_name" content="' .$libConfig->verbindungLand. '"/>' . PHP_EOL;
echo '  </head>' . PHP_EOL;
echo '  <body>' . PHP_EOL;

$libMenuRenderer = new \vcms\LibMenuRenderer();
$libMenuRenderer->printNavbar($libMenuInternet, $libMenuIntranet, $libMenuAdministration, $libGlobal->pid, $libAuth->getGruppe(), $libAuth->getAemter());

if($libGlobal->page->isContainerEnabled()){
	echo '    <main id="content">' . PHP_EOL;
	echo '      <div id="container" class="container">' . PHP_EOL;
}



function getPageTitle(){
	global $libGlobal, $libConfig, $libTime, $libDb;

	$result = '';

	if($libGlobal->pid == $libConfig->defaultHome){
		$result = $libConfig->verbindungName;
	} else if(isEventPage()){
		$stmt = $libDb->prepare("SELECT titel, datum, intern FROM base_veranstaltung WHERE id=:id");
		$stmt->bindValue(':id', $_REQUEST['eventid'], PDO::PARAM_INT);
		$stmt->execute();
		$event = $stmt->fetch(PDO::FETCH_ASSOC);

		if($event['titel'] != '' && $event['intern'] == 0){
			$result = $libConfig->verbindungName. ' - ' .$event['titel']. ' am ' .$libTime->formatDateString($event['datum']);
		} else {
			$result = $libConfig->verbindungName. ' - ' .$libGlobal->page->getTitle();
		}
	} else {
		$result = $libConfig->verbindungName. ' - ' .$libGlobal->page->getTitle();
	}

	return $result;
}

function getPageCanonicalUrl(){
	global $libGlobal, $libConfig;

	$result = '';

	if($libGlobal->pid == $libConfig->defaultHome){
		$result = $libGlobal->getSiteUrl(). '/';
	} else if(isEventPage()){
		$result = $libGlobal->getSiteUrl(). '/index.php?pid=' .$libGlobal->pid. '&amp;eventid=' .$_REQUEST['eventid'];
	} else {
		$result = $libGlobal->getSiteUrl(). '/index.php?pid=' .$libGlobal->pid;
	}

	return $result;
}

function getPageOgUrl(){
	global $libGlobal;

	$result = '';

	if(isEventPage()){
		$result = $libGlobal->getSiteUrl(). '/index.php?pid=' .$libGlobal->pid. '&amp;eventid=' .$_REQUEST['eventid'];
	} else {
		$result = $libGlobal->getSiteUrl(). '/';
	}

	return $result;
}

function isEventPage(){
	global $libGlobal;

	return $libGlobal->page->getPid() == 'semesterprogramm_event' 
			&& isset($_REQUEST['eventid']) && is_numeric($_REQUEST['eventid']);
}