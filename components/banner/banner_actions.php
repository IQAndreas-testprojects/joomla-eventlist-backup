<?php

//Define the constant that allows other pages to be viewed
define('BANNER', JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner');

//Define all redirect pages
define('EDIT_BANNER',		   BANNER.DS.'edit_banner.html.php');
define('EDIT_BANNER_SETTINGS',  BANNER.DS.'edit_banner_settings.html.php');

//NOTE: This page is made to be embedded INSIDE "EDIT_BANNER",
//not as standalone!
define('EDIT_BANNER_TEXT_SINGLE',  	BANNER.DS.'edit_banner_text_single.html.php');
define('EDIT_BANNER_TEXT_SPAN',  	BANNER.DS.'edit_banner_text_span.html.php');


//Import the databse directly - it will be used in every function anyway
require_once(BANNER.DS.'banner.db.php');

require_once(BANNER.DS.'messages.php');


class BannerActions
{

	static $errorLog = array();
	public static function getErrors($part)
	{
		return BannerActions::$errorLog[$part]; 
	}
	public static function addError($part, $msg)
	{
		BannerActions::$errorLog[$part] .= $msg . "<br/>";
	}
	public static function hasErrors()
	{
		if (count(BannerActions::$errorLog) > 0)
			{ return true; }
			
		//else 
		return false;
	}

	// ---------------- EDITING REDIRECTION ----------------------

	static function editBanner()
	{
		//Check if the owner is even allowed to edit the banner
		$banner = BannerDatabase::getBanner(JRequest::getInt('id'));

		if (($banner) && BannerActions::allowedToEdit($banner->owner))
		{
		
			if (JRequest::getCmd('save'))
			{
				switch (JRequest::getCmd('action'))
				{
					case 'settings' :
						return BannerActions::saveBannerSettings($banner);
						break;
					case 'single_text' :
						return BannerActions::saveBannerSingleText($banner);
						break;
					case 'span_text' :
						return BannerActions::saveBannerSpanText($banner);
						break;
						
					case 'add_text' :
						//Only addable in span!!
						return BannerActions::saveAddBannerText($banner);
						break;

					default :
						//Unknown command, just return to the default eventlist page
						return EDIT_BANNER;
				}
			}
			elseif (JRequest::getCmd('preview'))
			{
				//Here is something nifty for ya!
				//Since the JRequest data already contains the neccessary items
				//all you have to do is navigate to the page and the POST data
				//will do the rest of the work. Glorious, eh?
				
				if (JRequest::getCmd('action') == 'settings')
				{
					BannerActions::validateBannerSettings();
					return EDIT_BANNER_SETTINGS;
				}
				elseif (JRequest::getCmd('action') == 'add_text')
				{
					//Originally for security, but with span banners,
					//there are no open holes that this fixes. 
					//Instead, it just makes things easier.
					BannerActions::$editTextID = JRequest::getInt('text_id');
					
					//Same as a standard SpanBanner test
					BannerActions::validateBannerSpanText();
					
					return EDIT_BANNER;
				}
				elseif (JRequest::getCmd('action') == 'single_text')
				{
					BannerActions::validateBannerText();
					
					$phpDate = mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'));
					//$mysqlDate = date( 'Y-m-d', $phpDate );
			
					$textID = BannerDatabase::getIdByDate($banner->id, $phpDate);
			
					//For security (and efficiency), use this rather than JRequest
					BannerActions::$editTextID = $textID;
					
					//Set the text of the date - for visible purposes only
					$date_text = $banner->date_text . " " . strftime($banner->date_format, $phpDate);
					JRequest::setVar('date_text', $date_text);
			
					return EDIT_BANNER;
				}
				elseif (JRequest::getCmd('action') == 'span_text')
				{
					//BannerActions::validateBannerText(); //No validation needed!
					BannerActions::validateBannerSpanText();
					
					//$phpDate = mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'));
					//$mysqlDate = date( 'Y-m-d', $phpDate );
			
					//$textID = BannerDatabase::getIdByDate($banner->id, $mysqlDate);
			
					//Originally for security, but with span banners,
					//there are no open holes that this fixes. 
					//Instead, it just makes things easier.
					BannerActions::$editTextID = JRequest::getInt('text_id');
					
					//Set the text of the date - for visible purposes only
					$date_text = $banner->date_text . " " . strftime($banner->date_format); //NOW by default
					JRequest::setVar('date_text', $date_text);
			
					return EDIT_BANNER;
				}				
				//else
				return EDIT_BANNER;
			}
			else
			{ 
				//Rather than save the results, since "status!=complete",
				//bring up the dialogs to modify the results
				//and prepopulate the fields with JRequest data

				switch (JRequest::getCmd('action'))
				{				
					case 'settings' :
						return BannerActions::editBannerSettings($banner);
						break;
					case 'single_text' :
						return BannerActions::editBannerSingleText($banner);
						break;
					case 'span_text' :
						return BannerActions::editBannerSpanText($banner);
						break;
						
					case 'add_text' :
						//Add new banner text
						//Only addable in span!!
						return BannerActions::addBannerText($banner);
						break;

					default :
						//No or unknown command, 
						//INSTEAD, show the edit banner page
						return EDIT_BANNER;
				}
			}
		}
		else
		{
			//Not allowed to edit pages you don't own
			//Just return to the default eventlist page
			return "";
		}
	}

	static function allowedToEdit($banner_owner)
	{
		$user =& JFactory::getUser();

		if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
			{ return true; }

		if ($user->id == $banner->owner)
			{ return true; }

		//else
		return false;
	}

	// ---------------- ADDING ----------------------
	public static function addBannerText($banner)
	{
		JRequest::setVar('id', $banner->id);
		
		//Originally for security, but with span banners,
		//there are no open holes that this fixes. 
		//Instead, it just makes things easier.
		//I had to do a little workaround here since there is no "text_id"
		//for this new text YET. We haven't even created it!!
		BannerActions::$editTextID = -1;
		
		//Default to today
		JRequest::setVar('banner_start_date', strftime(DATEFORMAT_INPUT));
		JRequest::setVar('banner_end_date',   strftime(DATEFORMAT_INPUT));
		
		return EDIT_BANNER;
	}
	
	public static function saveAddBannerText($banner)
	{
		//Mainly check the dates
		BannerActions::validateBannerSpanText();
		if (BannerActions::hasErrors())
		{
			BannerActions::$editTextID = JRequest::getInt('text_id');
			return EDIT_BANNER;
		}
		
		//$start_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_start_date'));
		//$sdate = mktime(0, 0, 0, $start_date_arr->month, $start_date_arr->day, $start_date_arr->year);
		$sdate = strtotime(JRequest::getString('banner_start_date'));
		$edate = 0; //"Blank" by default
		
		//The end date is optional!
		//But will be checked if it is not empty
		if (strlen(JRequest::getString('banner_end_date')))
		{
			//$end_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_end_date'));
			//$edate = mktime(0, 0, 0, $end_date_arr->month, $end_date_arr->day, $end_date_arr->year);
			$edate = strtotime(JRequest::getString('banner_end_date'));
		}
		
		//else
		BannerDatabase::addBannerText($banner->id, JRequest::getString('main_text'), JRequest::getString('sub_text'), $sdate, $edate);
		
		//This is to make it easier for the banner to show up nicely!
		//$date_text = $banner->date_text . " " . strftime($banner->date_format, $sdate); 
		
		return EDIT_BANNER;
	}
		
	// ---------------- EDITING ----------------------
	//If they have come this far, they are already validated and allowed to edit the banner

	public static function editBannerSettings($banner)
	{
		//Set the appropriate JRequest values to fill the table
		//$settings = BannerDatabase::getBanner($bannerID);
		
		//List updatable settings
		//`id`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
		JRequest::setVar('id', $banner->id);
		JRequest::setVar('site_url', $banner->site_url);
		JRequest::setVar('background_image', $banner->background_image);
		JRequest::setVar('price_text', $banner->price_text);				
		JRequest::setVar('time_text', $banner->time_text);
		
		JRequest::setVar('date_text', $banner->date_text);				
		JRequest::setVar('date_format', $banner->date_format);

		//Instead of retrieving a date, display the default text
		JRequest::setVar('main_text', SAMPLE_MAIN_TEXT);
		JRequest::setVar('sub_text', SAMPLE_SUB_TEXT);
		
		return EDIT_BANNER_SETTINGS;
	}
	
	public static function editBannerSingleText($banner)
	{
		//Actually, just use the single "editBannerText" function
		//It handles both cases! :)
		return BannerActions::editBannerText($banner);
	}

	public static function editBannerSpanText($banner)
	{
		//Actually, just use the single "editBannerText" function
		//It handles both cases! :)
		return BannerActions::editBannerText($banner);
	}
	
	public static function editBannerText($banner)//, $day, $month, $year)
	{
		$textID = JRequest::getInt('text_id', 0);
		
		if ($textID == 0)
		{
			//If not passed in manually,
			//Calculate the ID based on date values
			
			//This is mainly used for SINGLE and also used to create new banner text
			
			$day = JRequest::getInt('d');
			$month = JRequest::getInt('m');
			$year = JRequest::getInt('y');
			
			$phpDate = mktime(0, 0, 0, $month, $day, $year);
			//$mysqlDate = date( 'Y-m-d', $phpDate );
			
			//Will create a new id with that date if not already existing
			$textID = BannerDatabase::getIdByDate($banner->id, $phpDate);
		}

		
		//Make sure all values are set
		if ($textID)
		{
	
			//For security (and efficiency), use this rather than JRequest
			BannerActions::$editTextID = $textID;
			
			$text = BannerDatabase::getBannerTextByID($textID); //, $mysqlDate);
			
			//This one is okay to pass via JRequest, and saves calculating time
			//JRequest::setVar('mysqlDate', $mysqlDate);
			JRequest::setVar('main_text', $text->main_text);
			JRequest::setVar('sub_text', $text->sub_text);
			JRequest::setVar('text_id', $textID); //Yes, duplicate, but this is okay
			
			
			//Set both SINGLE and SPAN values, doesn't matter
			
			//Used for the SINGLE preview 
			$date_text = $banner->date_text . " " . strftime($banner->date_format, $phpDate);
			JRequest::setVar('date_text', $date_text);
			
			//Convert int dates to strings
			$start_date = strftime(DATEFORMAT_INPUT, $text->banner_start_date);
			$end_date =   strftime(DATEFORMAT_INPUT, $text->banner_end_date);
			
			//Used for SPAN
			JRequest::setVar('banner_start_date', $start_date);
			JRequest::setVar('banner_end_date', $end_date);
			
			//Setting the "editTextID" property will automatically make the extra form appear
			return EDIT_BANNER;
		}
		
		//else
		return EDIT_BANNER;
	}
	
	// ---------------- VALIDATING ----------------------
	// 	Returns "true" upon errors, and updates the error log
	// EDIT: MAYBE NOT
	
	public static function validateBannerSettings()
	{
		if (!strlen(JRequest::getString('site_url')))
			{ BannerActions::addError('site_url', E_URL_TOO_SHORT); }
		if (!strlen(JRequest::getString('background_image')))
			{ BannerActions::addError('background_image', E_NO_BACKGROUND_IMAGE); }
			
		//No checking needed for "price_text" or "time_text". Empty is okay!
	}
	
	public static function validateBannerText()
	{
		//Requires no validation
		//return;
	}
	
	public static function validateBannerSpanText()
	{
		//$start_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_start_date'));
		//if ($start_date_arr->error_count)
		if (!strtotime(JRequest::getString('banner_start_date')))
		{
			BannerActions::addError('banner_start_date', E_BAD_DATEFORMAT);
		}
		
		//The end date is optional!
		//But will be checked if it is not empty
		if (strlen(JRequest::getString('banner_end_date')))
		{
			//$end_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_end_date'));
			//if ($end_date_arr->error_count)
			if (!strtotime(JRequest::getString('banner_end_date')))
			{
				BannerActions::addError('banner_end_date', E_BAD_DATEFORMAT);
			}
		}
	}
	
	
	// ---------------- SAVING ----------------------
	
	public static function saveBannerSettings($banner)
	{
		BannerActions::validateBannerSettings();
		if (BannerActions::hasErrors())
		{
			return EDIT_BANNER_SETTINGS;
		}
		//else continue
		
		//The database will automatically clean the strings. phew...
		BannerDatabase::editBannerSettings($banner->id,
			 JRequest::getString('site_url'),
			 JRequest::getString('background_image'),
			 JRequest::getString('date_text'),
			 JRequest::getString('price_text'),
			 JRequest::getString('time_text'));
			 
		return EDIT_BANNER;
	}
	
	public static function saveBannerSingleText($banner)
	{
		/*
		BannerActions::validateBannerText();
		if (BannerActions::hasErrors())
		{
			BannerActions::$editTextID = JRequest::getInt('text_id');
			return EDIT_BANNER;
		}
		*/
		//else continue
		
		//The database will automatically clean the strings.
		BannerDatabase::setBannerTextByID(JRequest::getInt('text_id'), $banner->id, JRequest::getString('main_text'), JRequest::getString('sub_text'));
		
		$date_text = $banner->date_text . " " . strftime($banner->date_format, mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y')));
		
		//Now make the preview thingy look nice
		JRequest::setVar('date_text', $date_text);
		
		return EDIT_BANNER;
	}
	
	public static function saveBannerSpanText($banner)
	{
		BannerActions::validateBannerSpanText();
		if (BannerActions::hasErrors())
		{
			BannerActions::$editTextID = JRequest::getInt('text_id');
			return EDIT_BANNER;
		}
		
		//else continue
		
		//$start_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_start_date'));
		//$sdate = mktime(0, 0, 0, $start_date_arr->month, $start_date_arr->day, $start_date_arr->year);
		$sdate = strtotime(JRequest::getString('banner_start_date'));
		$edate = 0; //"Blank" by default
		
		//The end date is optional!
		//But will be checked if it is not empty
		if (strlen(JRequest::getString('banner_end_date')))
		{
			//$end_date_arr = strptime(DATEFORMAT_INPUT, JRequest::getString('banner_end_date'));
			//$edate = mktime(0, 0, 0, $end_date_arr->month, $end_date_arr->day, $end_date_arr->year);
			$edate = strtotime(JRequest::getString('banner_end_date'));
		}
		
		//The database will automatically clean the strings.
		BannerDatabase::setBannerTextByID(JRequest::getInt('text_id'), $banner->id, JRequest::getString('main_text'), JRequest::getString('sub_text'), $sdate, $edate);
		
		//This is to make it easier for the preview of the banner to show up nicely!
		$date_text = $banner->date_text . " " . strftime($banner->date_format, $sdate); 
		JRequest::setVar('date_text', $date_text);
		
		return EDIT_BANNER;
	}
	
	
	// ---------------- DISPLAY ----------------------
	
	//This variable is a sortof hackish way of doing things.
	//It is difficult keeping track of it in the database otherwise.
	//Maybe it's better to use the date and bannerID as the unique identifiers?
	private static $editTextID = 0;
	public static function editingText()
	{
		return (BannerActions::$editTextID) ? true : false;
	}
	
	
	public static function showEditBannerText($categoryType = CTYPE_SPAN)
	{
		if (BannerActions::editingText())
		{
			/*
			//Convert the date to a from that is openable by MySQL
			//$mysqlDate = date( 'Y-m-d H:i:s', $phpdate );		
			$mysqlDate = date( 'Y-m-d', $phpdate );
			$text_id = BannerDatabase::getIdByDate();
			*/
			//include(EDIT_BANNER_TEXT);
			
			if ($categoryType == CTYPE_SINGLE)
			{
				require(EDIT_BANNER_TEXT_SINGLE); 
			}
			elseif ($categoryType == CTYPE_SPAN)
			{ 
				require(EDIT_BANNER_TEXT_SPAN); 
			}
			else
			{
				//By default, list used banners
				require(EDIT_BANNER_TEXT_SPAN); 
			}
		}
	}
	

	//$baseURL = http://stadsaktuellt.nu/index.php?option=com_eventlist&task=edit_banner&id=1
	public static function showMonthSelector($baseURL, $month, $year)
	{
		echo '<div class="elbanner_monthselector">';

			//Previous month - DISABLED
			//echo '<span class="elbanner_month">'.BannerActions::monthText($baseURL, $month-1, $year).' &nbsp; </span>';
			
			$currentMonth = intval(date('m'));
			$currentYear = intval(date('Y'));
			if (($currentMonth != $month) || ($currentYear != $year))
			{
				//Current month
				echo '<span class="elbanner_month">'.BannerActions::monthText($baseURL, $currentMonth, $currentYear).' &nbsp; </span>';
			}
		
			//Current month
			echo '<span class="elbanner_current_month">'.BannerActions::monthText($baseURL, $month, $year).' &nbsp; </span>';
			
			//Next two months
			echo '<span class="elbanner_month">'.BannerActions::monthText($baseURL, $month+1, $year).' &nbsp; </span>';
			echo '<span class="elbanner_month">'.BannerActions::monthText($baseURL, $month+2, $year).'</span>';
		
		echo '</div>';
	}

	private static function monthText($baseURL, $month, $year)
	{
		$url = $baseURL . "&m=".$month . "&y=".$year;
		return '<a href="'.$url.'">'.strftime(DATEFORMAT_MONTH_TEXT, mktime(0,0,0, $month, 1, $year)).'</a>';
	}

	
	public static function showBannerTextTableByMonth($bannerID, $month, $year, $baseURL = "")
	{
		$editable = ($baseURL) ? true : false;
		$databaseTextArray = BannerDatabase::getBannerTextByMonth($bannerID, $month, $year);
		
		$currentDay = 	intval(date("j"));
		$currentMonth = intval(date("n"));
		$currentYear = 	intval(date("Y"));
		$dullOldText = (($currentMonth == $month) && ($currentYear == $year)) ? true : false;
		
		if ($editable)
		{
			$baseURL .= "&m=".$month . "&y=".$year . "&action=single_text";
		}
		
		//The number of days in the specified month
		$numDays = date("t", mktime(0,0,0, $month, 1, $year));
		$mainTextArray = array();
		$subTextArray = array();
		
		for ($i = 1; $i <= $numDays; $i++)
		{
			//Fill the array with blank values
			$mainTextArray[$i] = NO_MAIN_TEXT;
			$subTextArray[$i] = NO_SUB_TEXT;
		}
		
		
		//Only loop through if the query actually returned data
		//This prevents error messages
		if ($databaseTextArray)
		{
			//Prepopulate the day array
			foreach($databaseTextArray as $bannerText)
			{
				$mainTextArray[$bannerText->day_of_month] = Banner::makeHTML($bannerText->main_text);
				$subTextArray[$bannerText->day_of_month] = Banner::makeHTML($bannerText->sub_text);
			}
		}
		
		echo '<br />';
		echo '<table>';
		for ($i = 1; $i <= $numDays; $i++)
		{
			$class = ($i % 2) ? "odd" : "even";

			if ($dullOldText && ($i < $currentDay))
				{ $class .= " old"; }
			
			echo '<tr class="'.$class.'">';
			
			//Only allowed to edit banners which days have not passed yet!
			if ($editable)
			{ 
				if ($dullOldText && ($i < $currentDay))
				{
					//Old date - not editable
					echo '<td valign="top"></td>';
				}
				else
				{ 
					echo '<td valign="top"><a href="'.$baseURL."&d=".$i.'"><img src="'.EDIT_IMAGE.'" /></a></td>'; 
				}
			}
			
			echo '<td valign="top">'.$i.".".'</td>';	
			echo '<td valign="top">'.$mainTextArray[$i].'</td>';
			echo '<td valign="top">'.$subTextArray[$i].'</td>';
			
			echo '</tr>';
			
			echo '<tr height="5">';
			echo '<td colspan="4"> </td>';
			
			echo '</tr>';
		}
		echo '</table>';
	}

	
	// "AllBannerText" is same as "span text"
	public static function showAllBannerTextTable($bannerID, $baseURL = "")
	{
		$editable = ($baseURL) ? true : false;
		$databaseTextArray = BannerDatabase::getSpanBannerAllText($bannerID);
		//print_r($databaseTextArray);
		
		//Only loop through if the query actually returned data
		//This prevents error messages
		if ($databaseTextArray)
		{
			echo '<br />';
			echo '<table>';
			
			//Create the header
			echo '<tr>';
			echo '<th></th>'; //The first column houses the edit button, and is therefore blank
			echo '<th>'.HEADER_BEGIN_DATE.'</th>';
			echo '<th>'.HEADER_END_DATE.'</th>';
			echo '<th>'.HEADER_MAIN_TEXT.'</th>';
			echo '<th>'.HEADER_SUB_TEXT.'</th>';
			echo '</tr>';
			
			//Determines the odd or even numbering of rows
			$i = 0;
			
			//Prepopulate the day array
			foreach($databaseTextArray as $bannerText)
			{
				//$mainTextArray[$bannerText->day_of_month] = $bannerText->main_text;
				//$subTextArray[$bannerText->day_of_month] = $bannerText->sub_text;
				
				$i++;
				$class = ($i % 2) ? "odd" : "even";
	
				if ($bannerText->old)
					{ $class .= " old"; }
				elseif ($bannerText->running)
					{ $class .= " running"; }
									
				echo '<tr class="'.$class.'">';
				
				//Only allowed to edit banners which days have not passed yet!
				if ($editable)
				{ 
					if ($bannerText->old)
					{
						//Old date - not editable
						echo '<td valign="top"></td>';
					}
					else
					{ 
						echo '<td valign="top"><a href="'.$baseURL."&action=span_text&text_id=".$bannerText->id.'"><img src="'.EDIT_IMAGE.'" /></a></td>'; 
					}
				}
				
				$start_date = 	strftime(DATEFORMAT_TEXT_LIST, $bannerText->banner_start_date);
				$end_date = 	strftime(DATEFORMAT_TEXT_LIST, $bannerText->banner_end_date);
				echo '<td valign="top">'.Banner::makeHTML($start_date).'</td>';	
				echo '<td valign="top">'.Banner::makeHTML($end_date).'</td>';
					
				echo '<td valign="top">'.Banner::makeHTML($bannerText->main_text).'</td>';
				echo '<td valign="top">'.Banner::makeHTML($bannerText->sub_text).'</td>';
				
				echo '</tr>';
				
				echo '<tr height="5">';
				echo '<td colspan="4"> </td>';
				
				echo '</tr>';
			}
			
			echo '</table>';
		}
		


	}
	
	
	// ---------------- MISC ----------------------
	public static function getBannerCategoryType($banner_id)
	{
		
		$category_id = BannerDatabase::getBannerCategory($banner_id);
		return BannerActions::getCTypeByCategoryID($category_id);

	}
	
	public static function getCTypeByCategoryID($categoryID)
	{
		switch ($categoryID)
		{
			case CAT_LUNCHGUIDEN:
				return CTYPE_SINGLE;
				
			case CAT_EVANGEMANG:
				return CTYPE_SPAN;
			
			default:
				return CTYPE_SPAN;
		}
	}
	

}

?>
