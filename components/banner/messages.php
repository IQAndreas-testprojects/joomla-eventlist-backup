<?php

//Define all messages here
//define('', 'banner.php');

//Set the locale to Sweden. Is there any way to do this in a config file?
//setlocale(LC_ALL, "sv_SE");
//NEVER MIND? Automatically done by Joomla??

//MISC
//define('SAMPLE_TEXT', "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nNam pharetra posuere urna, sit amet adipiscing lacus volutpat eget.");
define('SAMPLE_LUNCH_TEXT', "(Dagens Lunch)");
define('SAMPLE_ALTLUNCH_TEXT', "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nNam pharetra posuere urna, sit amet adipiscing lacus volutpat eget.");

define('BANNER_CSS', "http://stadsaktuellt.nu/components/com_eventlist/banner/dagens.css");
define('EDIT_IMAGE', "components/com_eventlist/assets/images/calendar_edit.png");

//DATE FORMATS
// http://se2.php.net/strftime
define('DATEFORMAT_TIME_TEXT', "DAGENS LUNCH %A %e %B");
define('DATEFORMAT_ENTER_DAILY_TEXT', "%A %e %B");

//BANNER EDITING
define('EDIT_BANNER_DESIGN', 	"Ändra utseende");
define('SAVE_BANNER_DESIGN', 	"Spara");
define('PREVIEW_BANNER_DESIGN',"Preview changes");

define('ENTER_DAILY_TEXT_FOR',	"Valj text for");
define('SAVE_BANNER_TEXT',		"Spara");
define('PREVIEW_BANNER_TEXT',	"Forhandsgranska");

//LABELS - USER INTERFACE
define('LABEL_SITE_URL', 		"Hemsida: ");
define('LABEL_BACKGROUND_IMAGE',"Bakgrund bild: ");
define('LABEL_PRICE_TEXT', 		"Pris: ");
define('LABEL_TIME_TEXT', 		"Serverings tid: ");
define('LABEL_LUNCH_TEXT', 		"Dagens lunch: ");
define('LABEL_ALTLUNCH_TEXT', 	"Dagens alternativ: ");
//define('LABEL_TEXT_COLOR', 	"Text farg: ");
//define('LABEL_TEXT_CSS', 		"Text stil (CSS): ");

//LABELS - ADMIN INTERFACE
define('LABEL_NAME', 		"Namn: ");
define('LABEL_OWNER', 		"Agare: ");
define('LABEL_CATEGORY', 	"Kategori: ");
//define('LABEL_OWNER', "Agare: ");

//define('SELECT_A_USER', "Valj en anvandare");
//define('SELECT_A_CATEGORY', "Valj en kategori");


define('CLICK_TO_DISABLE', 	"Clicka for att dolja banner");
define('CLICK_TO_ENABLE', 	"Clicka for att visa banner");
define('NEW_BANNER', 		"Ny banner");
define('ADD_BANNER', 		"Spara");
define('EDIT_BANNER', 		"Ändra");
define('SAVE_BANNER', 		"Spara");
define('EDIT_REMOVE_BANNER',"Ändra/Ta bort");
define('REMOVE_BANNER', 	"Ta bort");
define('REMOVE_BANNER_PERMANENTLY', "Ta bort bannern permanent. Kan ej aterstallas!");

//ERROR MESSAGES
define('NO_BANNER_TEXT_TODAY', 	'Ingen banner text for idag - Ändra');
define('ADD_REMOVE_BANNERS', 	'Lagg till och ta bort banners');
define('NO_LUNCH_TEXT', 		'-');
define('NO_ALTLUNCH_TEXT', 		'-');

define('E_URL_TOO_SHORT', 		'Hemsida address for kort');
define('E_NO_BACKGROUND_IMAGE', 'Ingen bakgrund bild');
define('E_NOT_ADMIN', 			'Insufficient privelages. Only administrators are allowed to edit banners.');



?>
