﻿<?php

//Define all messages here
//define('', 'banner.php');

//Set the locale to Sweden. Is there any way to do this in a config file?
//setlocale(LC_ALL, "sv_SE");
//NEVER MIND? Automatically done by Joomla??

//MISC
//define('SAMPLE_TEXT', "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nNam pharetra posuere urna, sit amet adipiscing lacus volutpat eget.");
define('SAMPLE_LUNCH_TEXT', "(Dagens Lunch)");
define('SAMPLE_ALTLUNCH_TEXT', "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nNam pharetra posuere urna, sit amet adipiscing lacus volutpat eget.");

define('BANNER_IMAGE_ROOT_URL', "http://stadsaktuellt.nu/images/banners/");
define('BANNER_CSS', "http://stadsaktuellt.nu/components/com_eventlist/banner/dagens.css");
define('EDIT_IMAGE', "components/com_eventlist/assets/images/calendar_edit.png");

//DATE FORMATS
// http://se2.php.net/strftime
define('DATEFORMAT_MONTH_TEXT', "%B %Y");
define('DATEFORMAT_TIME_TEXT', "DAGENS LUNCH %A %e %B");
define('DATEFORMAT_ENTER_DAILY_TEXT', "%A %e %B");

//BANNER EDITING
define('EDIT_BANNER_DESIGN', 	"Ändra annonsens utseende");
define('SAVE_BANNER_DESIGN', 	"Spara");
define('PREVIEW_BANNER_DESIGN',"Förhandsgranska ändringarna");

define('ENTER_DAILY_TEXT_FOR',	"Välj text för");
define('SAVE_BANNER_TEXT',		"Spara");
define('PREVIEW_BANNER_TEXT',	"Förhandsgranska");

//LABELS - USER INTERFACE
define('LABEL_SITE_URL', 		"Hemsida: ");
define('LABEL_BACKGROUND_IMAGE',"Bakgrundsbild: ");
define('LABEL_PRICE_TEXT', 		"Pris: ");
define('LABEL_TIME_TEXT', 		"Serveringstid: ");
define('LABEL_LUNCH_TEXT', 		"Dagens lunch: ");
define('LABEL_ALTLUNCH_TEXT', 	"Dagens alternativ: ");
//define('LABEL_TEXT_COLOR', 	"Textfärg: ");
//define('LABEL_TEXT_CSS', 		"Textstil (CSS): ");

//LABELS - ADMIN INTERFACE
define('LABEL_NAME', 		"Namn: ");
define('LABEL_OWNER', 		"Annonsör: ");
define('LABEL_CATEGORY', 	"Kategori: ");
//define('LABEL_OWNER', "Annonsör: ");

//PREFIXES
define('PREFIX_TIME_TEXT', 		"Serveras ");


//define('SELECT_A_USER', "Välj en användare");
//define('SELECT_A_CATEGORY', "Välj en kategori");


define('CLICK_TO_DISABLE', 	"Clicka för att dölja banner");
define('CLICK_TO_ENABLE', 	"Clicka för att visa banner");
define('NEW_BANNER', 		"Ny banner");
define('ADD_BANNER', 		"Spara");
define('EDIT_BANNER', 		"Ändra");
define('SAVE_BANNER', 		"Spara");
define('EDIT_REMOVE_BANNER',"Ändra/Ta bort");
define('REMOVE_BANNER', 	"Ta bort");
define('REMOVE_BANNER_PERMANENTLY', "Ta bort bannern permanent. Kan ej återställas!");

//ERROR MESSAGES
define('NO_BANNER_TEXT_TODAY', 	'Ingen annonstext för idag - ändra');
define('ADD_REMOVE_BANNERS', 	'Lägg till och ta bort banners');
define('NO_LUNCH_TEXT', 		'-');
define('NO_ALTLUNCH_TEXT', 		'-');

define('E_URL_TOO_SHORT', 		'Hemsidesaddress för kort');
define('E_NO_BACKGROUND_IMAGE', 'Ingen bakgrundsbild');
define('E_NOT_ADMIN', 			'Insufficient privelages. Only administrators are allowed to edit banners.');



?>
