<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// set default timezone

date_default_timezone_set('America/New_York');

/* OPM CONFIG OPTIONS */

$config['localIP'] = '72.52.140.17'; // local IP address as seen by PHP, for image security 

// defaults

$config['searchPerPage'] = 25;

$config['defaultPurchaseLength'] = 60; // for account purchases, in days


// visual / thumbnail sizing

$config['visualWidth'] = 1100; // required width of uploaded images (visuals)
$config['visualHeight'] = 1100; // required height of uploaded images (visuals)


// is test server?

$config['testServer'] = false;
$config['debugMode'] = false;

// server abs path 

$config['webrootPath'] = "/var/www/html/";

// path to templates directory

$config['templatePath'] = "/home/bravado/templates/";

// opm log file

$config['logPath'] = $config['webrootPath'] . "resources/logs/opmLog.txt";


// mail config!

$config['testEmailFlag'] = false;
$config['testEmailFlagSubjectText'] = "*TEST* ";
$config['testEmailFlagBodyText'] = "\n\n--- THIS EMAIL IS A TEST FROM THE OPM 2.0 SYSTEM --\n\n  ";

$config['supportEmail'] = "opm@bravado.com";

$config['mailSMTP'] = true;
 
$config['mailHost'] = "Bravadomaildrop.umusic.com";
//$config['mailHost'] = "167.167.42.79";
//$config['mailHost'] = "MAILDROPDMZPUB.USFSH.UMUSIC.COM";
$config['mailPort'] = 25;
$config['mailUser'] = "";  // SMTP username
$config['mailPass'] = ""; // SMTP password


$config['mailFrom'] = "opm@bravado.com";
$config['mailFromName'] = "Bravado OPM";

// page titles

$config['title_prepend'] = "Bravado OPM -";

// what to redirect to on login

$config['startPage'] = "/";

// Absolute path to files dir for storing masterfiles/separations!

$config['fileUploadPath'] = "/files/";

// where to put property archives - should be web accessible.

$config['fileArchivePath'] = "/files/archive/";

// ftp area for invoice + product exports

$config['ftpPath'] = "/home/bravado/";

// temporary directory to use for zip files.

$config['tmpDir'] = "/tmp/";


// SUPER ADMINS - an array of users that are auto granted all permissions.

$config['superAdmins'] = array(1,194,183,2168);


// usergroups - make sure these are accurate!

$config['bravadoInternalGroupID'] = 1;
$config['administratorsGroupID'] = 8;
$config['internationalUmgGroupID'] = 504;
$config['propertyContactsGroupID'] = 2;
$config['designersGroupID'] = 3;
$config['separatorsGroupID'] = 4;
$config['screenprintersGroupID'] = 5;
$config['externalViewingGroupID'] = 185;
$config['licenseeGroupID'] = 231;
$config['productManagersGroupID'] = 336;
$config['viewOnlyPropGroupID'] = 591;



// invoice statuses - make sure these are accurate!

$config['invStatusInProgress'] = 1;
$config['invStatusSubmitted'] = 2;
$config['invStatusApproved'] = 3;
$config['invStatusPaid'] = 4;
$config['invStatusDeleted'] = 5;
$config['invStatusPreapproved'] = 6;
$config['invStatusSentToNavision'] = 7;

// approval statuses

$config['appStatusApproved'] = 1;
$config['appStatusApprovedWComments'] = 2;
$config['appStatusRejected'] = 3;
$config['appStatusExpired'] = 4;
$config['appStatusSubmitted'] = 5;
$config['appStatusAwaitingRevisions'] = 6;


/* THIS LIST DETERMINES IF INDIVIDUAL CONTACT STATUSES ARE SHOWN (don't show them if this product was merely submitted by a licensee and not ready for contact approval!) */

$config['phase2Statuses'][] = 0;
$config['phase2Statuses'][] = $config['appStatusApproved'];
$config['phase2Statuses'][] = $config['appStatusApprovedWComments'];
$config['phase2Statuses'][] = $config['appStatusRejected'];
$config['phase2Statuses'][] = $config['appStatusExpired'];
$config['phase2Statuses'][] = $config['appStatusAwaitingRevisions'];

// invoice charge types

$config['invCTDesignApproval'] = 3;
$config['invCTSeparations'] = 1;
$config['invCTOther'] = 7;

// exploitation statuses

$config['exStatusActive'] = 1;
$config['exStatusInactive'] = 2;

// usage statuses

$config['usageStatusCleared'] = 1;
$config['usageStatusUncleared'] = 2;

// default user preferences - none.

$config['defaultPrefs'] = array();

// default designer prefs = none.

$config['defaultDesignerPrefs'] = array();

// default property contact prefs

$config['defaultPropertyContactPrefs'][] = 1;
$config['defaultPropertyContactPrefs'][] = 3;

// default licensee prefs

$config['defaultLicenseePrefs'][] = 12;

// currency stuff

$config['USDollarsCurrencyID'] = 1;

// Password Decryption Functions

$config['CRYPT_SALT'] = 85; # any number ranging 1-255
$config['START_CHAR_CODE'] = 100; # 'd' letter

// pdf config!

// UG Display
// The below UGs will not be displayed in the standard way. They need their own multiple selects.

$config['MultipleSelectUGs'][] = $config['separatorsGroupID'];
$config['MultipleSelectUGs'][] = $config['screenprintersGroupID'];

// children will not be shown on involvement page

$config['hideChildUGs'][] = $config['licenseeGroupID'];


// COMPANY IDS

$config['BravadoCompanyID'] = 1;

//$config['fonts_path']= base_url() . "resources/font/"; 


?>
