<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//Avoids typing out "mysql_real_escape_string" 20 times in a row. ;)
function clean($str)
	{ return mysql_real_escape_string($str); }
	
	
class BannerDatabase
{ 


	public static function getBanners()
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `date_text`, `date_format`, `price_text`, `time_text` ";
		$query .= "FROM `#__eventlist_banners`; ";
		
		$db->setQuery($query);
		return $db->loadObjectList(); 
	}
	/*
	public static function getBannersByCategory($categoryID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `name`, `owner`, `category`, `background_image`, `site_url`, `date_text`, `date_format`, `price_text`, `time_text` ";
		$query .= "FROM `#__eventlist_banners` ";
		$query .= "WHERE (category='".intval($categoryID)."'); ";
		
		$db->setQuery($query);
		return $db->loadObjectList();  
	}
	*/

	public static function getActiveBanners()
	{
		$db =& JFactory::getDBO();

		$query = "SELECT * ";//`id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `date_text`, `date_format`, `price_text`, `time_text` ";
		$query .= "FROM `#__eventlist_banners` ";
		$query .= "WHERE (`enabled`='1'); ";
		
		$db->setQuery($query);
		return $db->loadObjectList(); 
	}


	public static function getActiveBannersByCategory($categoryID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `name`, `owner`, `category`, `background_image`, `site_url`, `date_text`, `date_format`, `price_text`, `time_text` ";
		$query .= "FROM `#__eventlist_banners` ";
		$query .= "WHERE (category='".intval($categoryID)."') ";
		$query .= "AND (enabled='1'); ";
		
		$db->setQuery($query);
		return $db->loadObjectList();  
	}


	public static function getBanner($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `date_text`, `date_format`, `price_text`, `time_text` ";
		$query .= "FROM `#__eventlist_banners` ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		return $db->loadObject();  
	}
	
	public static function getBannerCategory($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `category` ";
		$query .= "FROM `#__eventlist_banners` ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";
		
		$db->setQuery($query);
		$db->query();
	
		if( $db->getNumRows() ) {
			return intval($db->loadResult());
		}
		
		//else
		return 0; 
	}
	
	//Set the `price_text` and `time_text` default values separately
	public static function addBanner($name, $owner, $category, $background_image, $site_url, $enabled = 1)
	{
		$db =& JFactory::getDBO();

		$query = "INSERT INTO `#__eventlist_banners` ";
		$query .= "(`name`, `owner`, `enabled`, `category`, `background_image`, `site_url`) ";
		$query .= "VALUES ('".clean($name)."', ".intval($owner)."', '".intval($enabled)."' , '".intval($category)."' , '".clean($background_image)."', '".clean($site_url)."'); ";

		$db->setQuery($query);
		$db->query();
	}


	//It's actually easier just to remove it and start over - usually
	public static function editBanner($bannerID, $owner, $category, $background_image, $site_url)
	{
		$db =& JFactory::getDBO();

		$query = "UPDATE `#__eventlist_banners` ";
		$query .= "SET `owner` = '".intval($owner)."', `category` = '".intval($category)."', `background_image` = '".clean($background_image)."', `site_url` = '".clean($site_url)."' ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
	}
	
	//This only edits a small set of settings which are allowed to be set by
	//the banner owners
	public static function editBannerSettings($bannerID, $site_url, $background_image, $date_text, $price_text, $time_text)
	{
		$db =& JFactory::getDBO();

		$query = "UPDATE `#__eventlist_banners` ";
		$query .= "SET `site_url` = '".clean($site_url)."', `background_image` = '".clean($background_image)."', `date_text` = '".clean($date_text)."', `price_text` = '".clean($price_text)."', `time_text` = '".clean($time_text)."' ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
	}


	public static function disableBanner($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "UPDATE `#__eventlist_banners` ";
		$query .= "SET `enabled` = '0' ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
	}

	public static function enableBanner($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "UPDATE `#__eventlist_banners` ";
		$query .= "SET `enabled` = '1' ";
		$query .= "WHERE (id='".intval($bannerID)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
	}


	public static function getBannerTodayText($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT main_text, sub_text ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (banner_start_date = CURDATE()) ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	//Note that the CATEGORY spans!
	//Right now only used for lunchguiden
	//EDIT: REPLACED "getBannerTodayText" as it was able to fit both conditions
	public static function getSpanBannerTodayText($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT main_text, sub_text ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (banner_start_date <= CURDATE()) ";
		$query .= "AND (IF(banner_end_date IS NULL, banner_start_date, banner_end_date) >= CURDATE()) ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$return =  $db->loadObject();
		echo $db->getErrorMsg();
		return $return;
	}
	
	
	public static function getBannerTextByMonth($bannerID, $month, $year)
	{
		$db =& JFactory::getDBO();

		//$query = "SELECT `id`, `banner_start_date`, DAYOFMONTH(banner_start_date) as day_of_month, WEEK(banner_start_date, 3) as week_number, `main_text`, `sub_text` "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		$query = "SELECT `id`, `banner_start_date`, DAYOFMONTH(banner_start_date) as day_of_month, `main_text`, `sub_text` "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "AND (MONTH(banner_start_date) = '".intval($month)."') ";
		$query .= "AND (YEAR(banner_start_date) = '".intval($year)."'); ";
		
		$db->setQuery($query);
		return $db->loadObjectList();  
	}
	
	//Returns all months and years, and the number of banners in that time period
	public static function getBannerCountsByMonth($bannerID)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT COUNT(*) as `count`, YEAR(`banner_start_date`) AS `year`, MONTH(`banner_start_date`) AS `month` ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "GROUP BY YEAR(`banner_start_date`), MONTH(`banner_start_date`) ";
		$query .= "ORDER BY YEAR(`banner_start_date`), MONTH(`banner_start_date`); ";
		
		$db->setQuery($query);
		return $db->loadObjectList();
		//echo ($db->getErrorMsg());
		//return $return;
	}
	
	//Returns all months and years, and the number of banners in that time period
	public static function getBannerCountsByBanner($bannerID)
	{
		$db =& JFactory::getDBO();

		//REMEBER! +1 to the day because DATE_DIFF() does not include the day it was first displayed etc
		
		$query = "SELECT `id`, (DATEDIFF(IF(`banner_end_date` IS NULL, `banner_start_date`, `banner_end_date`), `banner_start_date`) + 1) AS `num_days`, ";
		$query .= "UNIX_TIMESTAMP(`banner_start_date`) as `banner_start_date`, UNIX_TIMESTAMP(IF(banner_end_date IS NULL, banner_start_date, banner_end_date)) AS `banner_end_date`, `main_text`, `sub_text` ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "ORDER BY `banner_start_date`; ";
		
		$db->setQuery($query);
		return $db->loadObjectList();  
	}
	
	
	//IF(banner_end_date IS NULL, banner_start_date, banner_end_date)
	
	//NOTICE!!! WEEK!!!
	//http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_week
	//Experiment with!!
	/*public static function getBannerTextByWeek($bannerID, $week, $year)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `banner_start_date`, DAYOFMONTH(banner_start_date) as day_of_month, WEEK(banner_start_date, 3) as week_number, `main_text`, `sub_text` "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (WEEK(banner_start_date, 3) = '".intval($week)."') ";
		$query .= "AND (YEAR(banner_start_date) = '".intval($year)."'); ";
				
		$db->setQuery($query);
		return $db->loadObjectList();  
	}*/
	
	public static function getSpanBannerAllText($bannerID)
	{
		$db =& JFactory::getDBO();

		//$query = "SELECT `id`, `banner_start_date`, DAYOFMONTH(banner_start_date) as day_of_month, WEEK(banner_start_date, 3) as week_number, `main_text`, `sub_text` "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		//$query = "SELECT `id`, UNIX_TIMESTAMP(`banner_start_date`) as `banner_start_date`, UNIX_TIMESTAMP(`banner_end_date`) as `banner_end_date`, `main_text`, `sub_text`, "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		$query = "SELECT `id`, UNIX_TIMESTAMP(`banner_start_date`) as `banner_start_date`, UNIX_TIMESTAMP(IF(banner_end_date IS NULL, banner_start_date, banner_end_date)) as `banner_end_date`, `main_text`, `sub_text`, "; //GET_FORMAT(banner_start_date,'ISO') AS date_text, 
		
		$query .= "   IF(CURDATE() > IF(banner_end_date IS NULL, banner_start_date, banner_end_date), 1, 0) AS `old`, IF(CURDATE() >= banner_start_date, 1, 0) AS `running` ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (enabled = '1') ";
		$query .= "AND (CHAR_LENGTH(`main_text`) + CHAR_LENGTH(`main_text`) > 4) "; //Skips blank banners
		$query .= "ORDER BY `banner_start_date` DESC; ";
				
		$db->setQuery($query);
		return $db->loadObjectList();  
		//echo $db->getErrorMsg();
		//return $return;
	}
		
	
	//This one is public, the other two are private
	public static function getIdByDate($bannerID, $date)
	{
		//By requiring the extra "bannerID", I am ENSURING that the
		//user won't be allowed to edit foreign banners
		
		$id = intval(BannerDatabase::getID($bannerID, $date));

		if ($id)
		{
			return $id;
		}
		//else
		
		BannerDatabase::addBannerText($bannerID, "", "", $date); //No end date set!
		
		//I'm not sure of a quick way to get the id from a newly created
		//banner, so this will do...
		return BannerDatabase::getID($bannerID, $date);
	}
	
	private static function getID($bannerID, $date)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id` FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (banner_id='".intval($bannerID)."') ";
		$query .= "AND (banner_start_date = FROM_UNIXTIME(".intval($date).")) ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
		
		if( $db->getNumRows() ) {
			return intval($db->loadResult());
		}
		
		//else
		return 0;
	}
	
	
	public static function addBannerText($banner_id, $main_text, $sub_text, $banner_start_date = -1, $banner_end_date = 0)
	{
		$db =& JFactory::getDBO();
		
		if ($banner_start_date == -1)
			{ $banner_start_date = time(); } //Default to now
			
		$cleaned_start_date = "FROM_UNIXTIME(".intval($banner_start_date).")";
		
		$cleaned_end_date = "";
		if ($banner_end_date)
		{
			$cleaned_end_date = "FROM_UNIXTIME(".intval($banner_end_date).")";
		}
		else
		{
			//Set the start and end dates to equal if no other value is set for the end
			$cleaned_end_date = $cleaned_start_date;
		}

		$query = "INSERT INTO `#__eventlist_banner_values` ";
		$query .= "(`banner_id`, `banner_start_date`, `banner_end_date`, `main_text`, `sub_text`) ";
		$query .= "VALUES ('".intval($banner_id)."', ".$cleaned_start_date.", ".$cleaned_end_date.", '".clean($main_text)."', '".clean($sub_text)."'); ";

		$db->setQuery($query);
		$db->query();
	}
	
	
	//NOTE: 'id' is NOT the same as 'banner_id'
	public static function getBannerTextByID($id) //, $banner_start_date)
	{
		$db =& JFactory::getDBO();

		$query = "SELECT `id`, `main_text`, `sub_text`, UNIX_TIMESTAMP(`banner_start_date`) as `banner_start_date`, UNIX_TIMESTAMP(IF(banner_end_date IS NULL, banner_start_date, banner_end_date)) as `banner_end_date` ";
		$query .= "FROM `#__eventlist_banner_values` ";
		$query .= "WHERE (id='".intval($id)."') ";
		//$query .= "AND (banner_start_date = '".clean($banner_start_date)."') ";
		$query .= "LIMIT 1; ";
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	//NOTE: 'id' is NOT the same as 'banner_id'
	//The extra "banner_id" parameter is there for added security
	public static function setBannerTextByID($id, $banner_id, $main_text, $sub_text, $banner_start_date = -1, $banner_end_date = -1)
	{
		$db =& JFactory::getDBO();

		$query = "UPDATE `#__eventlist_banner_values` ";
		$query .= "SET `main_text` = '".clean($main_text)."', `sub_text` = '".clean($sub_text)."' ";
		$query .= "WHERE (id='".intval($id)."') ";
		$query .= "AND (banner_id='".intval($banner_id)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
		
		if ($banner_start_date != -1)
		{
			BannerDatabase::setBannerTextDate($id, $banner_id, $banner_start_date, $banner_end_date);
		}
	}
	
	//NOTE: 'id' is NOT the same as 'banner_id'
	//The extra "banner_id" parameter is there for added security
	public static function setBannerTextDate($id, $banner_id, $banner_start_date = -1, $banner_end_date = -1)
	{
		$db =& JFactory::getDBO();
		
		if ($banner_start_date == -1)
			{ $banner_start_date = time(); } //Default to now
			
		$cleaned_start_date = "FROM_UNIXTIME(".intval($banner_start_date).")";
		
		$cleaned_end_date = "";
		if ($banner_end_date == -1)
		{
			//Set the start and end dates to equal if no other value is set for the end
			//$cleaned_end_date = $cleaned_start_date;
			$cleaned_end_date = "NULL";
		}
		else
		{
			$cleaned_end_date = "FROM_UNIXTIME(".intval($banner_end_date).")";
		}

		$query = "UPDATE `#__eventlist_banner_values` ";
		$query .= "SET `banner_start_date` = ".$cleaned_start_date.", `banner_end_date` = ".$cleaned_end_date." ";
		$query .= "WHERE (id='".intval($id)."') ";
		$query .= "AND (banner_id='".intval($banner_id)."') ";
		$query .= "LIMIT 1; ";

		$db->setQuery($query);
		$db->query();
	}

}



?>
