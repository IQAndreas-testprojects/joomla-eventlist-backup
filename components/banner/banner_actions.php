<?php

//Define the constant that allows other pages to be viewed
define('BANNER', JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner');

//Define all redirect pages
define('EDIT_BANNER',           BANNER.DS.'edit_banner.html.php');
define('EDIT_BANNER_SETTINGS',  BANNER.DS.'edit_banner_settings.html.php');

//NOTE: This page is made to be embedded INSIDE "EDIT_BANNER",
//not as standalone!
define('EDIT_BANNER_TEXT',  BANNER.DS.'edit_banner_text.html.php');


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
                    case 'text' :
                        return BannerActions::saveBannerText($banner);
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
                elseif (JRequest::getCmd('action') == 'text')
                {
                	BannerActions::validateBannerText();
                	
                	$phpDate = mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'));
					$mysqlDate = date( 'Y-m-d', $phpDate );
    		
					$textID = BannerDatabase::getIdByDate($banner->id, $mysqlDate);
			
					//For security (and efficiency), use this rather than JRequest
    				BannerActions::$editTextID = $textID;
    				
    				//Set the text of the date - for visible purposes only
					JRequest::setVar('date_text', strftime(DATEFORMAT_TIME_TEXT, $phpDate));
    		
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
                    case 'text' :
                        return BannerActions::editBannerText($banner);
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

    	//Instead of retrieving a date, display the default text
    	JRequest::setVar('lunch_text', SAMPLE_LUNCH_TEXT);
    	JRequest::setVar('altlunch_text', SAMPLE_ALTLUNCH_TEXT);
    	
    	return EDIT_BANNER_SETTINGS;
    }
    
    
    public static function editBannerText($banner)//, $day, $month, $year)
    {
    	$day = JRequest::getInt('d');
    	$month = JRequest::getInt('m');
    	$year = JRequest::getInt('y');
    	
    	//Make sure all values are set
    	if ($day && $month && $year)
    	{
    		$phpDate = mktime(0, 0, 0, $month, $day, $year);
			$mysqlDate = date( 'Y-m-d', $phpDate );
    		
			$textID = BannerDatabase::getIdByDate($banner->id, $mysqlDate);
			
			//For security (and efficiency), use this rather than JRequest
    		BannerActions::$editTextID = $textID;
    		
    		$text = BannerDatabase::getBannerTextByID($textID, $mysqlDate);
    		
    		//This one is okay to pass via JRequest, and saves calculating time
    		//JRequest::setVar('mysqlDate', $mysqlDate);
    		JRequest::setVar('lunch_text', $text->lunch_text);
    		JRequest::setVar('altlunch_text', $text->altlunch_text);
    		JRequest::setVar('text_id', $textID); //Yes, duplicate, but this is okay
    		
    		//Used for the preview
    		JRequest::setVar('date_text', strftime(DATEFORMAT_TIME_TEXT, $phpDate));
    		
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
    	return;
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
    		 JRequest::getString('price_text'),
    		 JRequest::getString('time_text'));
    		 
   		return EDIT_BANNER;
    }
    
    public static function saveBannerText($banner)
    {
    	/*
    	BannerActions::validateBannerText();
		if (BannerActions::hasErrors())
        {
        	return EDIT_BANNER;
        }
        */
    	//else continue
    	
    	//The database will automatically clean the strings.
    	
    	BannerDatabase::setBannerTextByID(JRequest::getInt('text_id'), $banner->id, JRequest::getString('lunch_text'), JRequest::getString('altlunch_text'));
    	
    	//Now make the preview thingy look nice
		JRequest::setVar('date_text', strftime(DATEFORMAT_TIME_TEXT, mktime(0, 0, 0, JRequest::getInt('m'), JRequest::getInt('d'), JRequest::getInt('y'))));
    	
   		return EDIT_BANNER;
    }
    
    
    // ---------------- DISPLAY ----------------------
    
    //This variable is a sortof hackish way of doing things.
    //It is difficult keeping track of it in the database otherwise.
    //Maybe it's better to use the date and bannerID as the unique identifiers?
    private static $editTextID = 0;
    public static function showEditBannerText()
    {
    	if (BannerActions::$editTextID)
    	{
    		/*
			//Convert the date to a from that is openable by MySQL
			//$mysqlDate = date( 'Y-m-d H:i:s', $phpdate );    	
			$mysqlDate = date( 'Y-m-d', $phpdate );
			$text_id = BannerDatabase::getIdByDate();
			*/
    		include(EDIT_BANNER_TEXT);
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
    		$baseURL .= "&m=".$month . "&y=".$year . "&action=text";
    	}
    	
    	//The number of days in the specified month
   		$numDays = date("t", mktime(0,0,0, $month, 1, $year));
    	$lunchTextArray = array();
    	$altlunchTextArray = array();
    	
    	for ($i = 1; $i <= $numDays; $i++)
    	{
    		//Fill the array with blank values
    		$lunchTextArray[$i] = NO_LUNCH_TEXT;
    		$altlunchTextArray[$i] = NO_ALTLUNCH_TEXT;
    	}
    	
    	//Prepopulate the day array
    	foreach($databaseTextArray as $bannerText)
    	{
    		$lunchTextArray[$bannerText->day_of_month] = $bannerText->lunch_text;
    		$altlunchTextArray[$bannerText->day_of_month] = $bannerText->altlunch_text;
    	}
    	
    	echo '<br />';
    	echo '<table>';
        for ($i = 1; $i <= $numDays; $i++)
    	{
    		$class = ($i % 2) ? "odd" : "even";

    		if ($dullOldText && ($i < $currentDay))
    			{ $class .= " old"; }
    		
    		echo '<tr class="'.$class.'">';
    		
    		if ($editable)
    			{ echo '<td valign="top"><a href="'.$baseURL."&d=".$i.'"><img src="'.EDIT_IMAGE.'" /></a></td>'; }
            
    		echo '<td valign="top">'.$i.".".'</td>';	
    		echo '<td valign="top">'.Banner::makeHTML($lunchTextArray[$i]).'</td>';
    		echo '<td valign="top">'.Banner::makeHTML($altlunchTextArray[$i]).'</td>';
    		
    		echo '</tr>';
    		
    		echo '<tr height="5">';
    		echo '<td colspan="4"> </td>';
    		
    		echo '</tr>';
    	}
    	echo '</table>';
    }



}

?>
