<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//Avoids typing out "mysql_real_escape_string" 20 times in a row. ;)
function clean($str)
	{ return mysql_real_escape_string($str); }
	
	
class BannerAdminDatabase
{ 


    public static function getBanners()
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
        $query .= "FROM `#__eventlist_banners`; ";
        
        $db->setQuery($query);
        return $db->loadObjectList(); 
    }
    /*
    public static function getBannersByCategory($categoryID)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `name`, `owner`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
        $query .= "FROM `#__eventlist_banners` ";
        $query .= "WHERE (category='".intval($categoryID)."'); ";
        
        $db->setQuery($query);
        return $db->loadObjectList();  
    }
    */
/*
    public static function getActiveBanners()
    {
        $db =& JFactory::getDBO();

        $query = "SELECT * ";//`id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
        $query .= "FROM `#__eventlist_banners` ";
        $query .= "WHERE (`enabled`='1'); ";
        
        $db->setQuery($query);
        return $db->loadObjectList(); 
    }


    public static function getActiveBannersByCategory($categoryID)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `name`, `owner`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
        $query .= "FROM `#__eventlist_banners` ";
        $query .= "WHERE (category='".intval($categoryID)."') ";
        $query .= "AND (enabled='1'); ";
        
        $db->setQuery($query);
        return $db->loadObjectList();  
    }

*/
    public static function getBanner($bannerID)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `name`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
        $query .= "FROM `#__eventlist_banners` ";
        $query .= "WHERE (id='".intval($bannerID)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        return $db->loadObject();  
    }
    
    //Set the `price_text` and `time_text` default values separately
    public static function addBanner($name, $owner, $category, $background_image = "", $site_url = "", $enabled = 1)
    {
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__eventlist_banners` ";
        $query .= "(`name`, `owner`, `enabled`, `category`, `background_image`, `site_url`) ";
        $query .= "VALUES ('".clean($name)."', '".intval($owner)."', '".intval($enabled)."' , '".intval($category)."' , '".clean($background_image)."', '".clean($site_url)."'); ";

        $db->setQuery($query);
        $db->query();
    }


    //It's actually easier just to remove it and start over - usually
    public static function editBanner($bannerID, $name, $owner, $category)
    {
        $db =& JFactory::getDBO();

        $query = "UPDATE `#__eventlist_banners` ";
        $query .= "SET `name` = '".clean($name)."', `owner` = '".intval($owner)."', `category` = '".intval($category)."' ";
        $query .= "WHERE (id='".intval($bannerID)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
    }
/*    
    //It's actually easier just to remove it and start over - usually
    public static function editBanner($bannerID, $name, $owner, $category, $background_image, $site_url)
    {
        $db =& JFactory::getDBO();

        $query = "UPDATE `#__eventlist_banners` ";
        $query .= "SET `name` = '".clean($name)."', `owner` = '".intval($owner)."', `category` = '".intval($category)."', `background_image` = '".clean($background_image)."', `site_url` = '".clean($site_url)."' ";
        $query .= "WHERE (id='".intval($bannerID)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
    }
    
    //This only edits a small set of settings which are allowed to be set by
    //the banner owners
    public static function editBannerSettings($bannerID, $site_url, $background_image, $price_text, $time_text)
    {
        $db =& JFactory::getDBO();

        $query = "UPDATE `#__eventlist_banners` ";
        $query .= "SET `site_url` = '".clean($site_url)."', `background_image` = '".clean($background_image)."', `price_text` = '".clean($price_text)."', `time_text` = '".clean($time_text)."' ";
        $query .= "WHERE (id='".intval($bannerID)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
    }
*/

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
    
    public static function removeBanner($bannerID)
    {
        $db =& JFactory::getDBO();
		
        //DELETE FROM `jos_eventlist_banners` WHERE `jos_eventlist_banners`.`id` = 4 LIMIT 1
        $query = "DELETE FROM `#__eventlist_banners` ";
        $query .= "WHERE (id='".intval($bannerID)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
    }
        
    // --------------- SPECIAL -------------------------------
    
    public static function getUsers()
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `name` ";
        $query .= "FROM `#__users` ";
        $query .= "WHERE (`block` = 0) ";
        $query .= "ORDER BY `name`; ";
        
        $db->setQuery($query);
        return $db->loadObjectList(); 
    }    
    
    public static function getCategories()
    {
        $db =& JFactory::getDBO();

		$query = "SELECT `id`, `catname` ";
		$query .= "FROM `#__eventlist_categories` ";
		//$query .= "WHERE (`published` = 1) ";
		$query .= "ORDER BY `catname`; ";
        
        $db->setQuery($query);
        return $db->loadObjectList(); 
    }
    

    /*

    public static function getBannerTodayText($bannerID)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT lunch_text, altlunch_text ";
        $query .= "FROM `#__eventlist_banner_values` ";
        $query .= "WHERE (banner_id='".intval($bannerID)."') ";
        $query .= "AND (enabled = '1') ";
        $query .= "AND (banner_date = CURDATE()) ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        return $db->loadObject();
    }
    
    public static function getBannerTextByMonth($bannerID, $month, $year)
    {
        $db =& JFactory::getDBO();

        //$query = "SELECT `id`, `banner_date`, DAYOFMONTH(banner_date) as day_of_month, WEEK(banner_date, 3) as week_number, `lunch_text`, `altlunch_text` "; //GET_FORMAT(banner_date,'ISO') AS date_text, 
        $query = "SELECT `id`, `banner_date`, DAYOFMONTH(banner_date) as day_of_month, `lunch_text`, `altlunch_text` "; //GET_FORMAT(banner_date,'ISO') AS date_text, 
        $query .= "FROM `#__eventlist_banner_values` ";
        $query .= "WHERE (banner_id='".intval($bannerID)."') ";
        $query .= "AND (enabled = '1') ";
        $query .= "AND (MONTH(banner_date) = '".intval($month)."') ";
        $query .= "AND (YEAR(banner_date) = '".intval($year)."'); ";
                
        $db->setQuery($query);
        return $db->loadObjectList();  
    }
    
    //NOTICE!!! WEEK!!!
    //http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_week
    //Experiment with!!
    public static function getBannerTextByWeek($bannerID, $week, $year)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id`, `banner_date`, DAYOFMONTH(banner_date) as day_of_month, WEEK(banner_date, 3) as week_number, `lunch_text`, `altlunch_text` "; //GET_FORMAT(banner_date,'ISO') AS date_text, 
        $query .= "FROM `#__eventlist_banner_values` ";
        $query .= "WHERE (banner_id='".intval($bannerID)."') ";
        $query .= "AND (enabled = '1') ";
        $query .= "AND (WEEK(banner_date, 3) = '".intval($week)."') ";
        $query .= "AND (YEAR(banner_date) = '".intval($year)."'); ";
                
        $db->setQuery($query);
        return $db->loadObjectList();  
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
    	
    	BannerDatabase::addBannerText($bannerID, $date, "", "");
    	
    	//I'm not sure of a quick way to get the id from a newly created
    	//banner, so this will do...
    	return BannerDatabase::getID($bannerID, $date);
    }
    
    private static function getID($bannerID, $date)
    {
        $db =& JFactory::getDBO();

        $query = "SELECT `id` FROM `#__eventlist_banner_values` ";
        $query .= "WHERE (banner_id='".intval($bannerID)."') ";
        $query .= "AND (banner_date = '".clean($date)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
        
        if( $db->getNumRows() ) {
            return intval($db->loadResult());
        }
        
        //else
        return 0;
    }
    
    
    private static function addBannerText($banner_id, $banner_date, $lunch_text, $altlunch_text)
    {
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__eventlist_banner_values` ";
        $query .= "(`banner_id`, `banner_date`, `lunch_text`, `altlunch_text`) ";
        $query .= "VALUES ('".intval($banner_id)."', '".clean($banner_date)."', '".clean($lunch_text)."', '".clean($altlunch_text)."'); ";

        $db->setQuery($query);
        $db->query();
    }
    
    
    //NOTE: 'id' is NOT the same as 'banner_id'
    public static function getBannerTextByID($id, $banner_date)
    {
    	$db =& JFactory::getDBO();

        $query = "SELECT `lunch_text`, `altlunch_text` ";
        $query .= "FROM `#__eventlist_banner_values` ";
        $query .= "WHERE (id='".intval($id)."') ";
        $query .= "AND (banner_date = '".clean($banner_date)."') ";
        $query .= "LIMIT 1; ";
		
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    //NOTE: 'id' is NOT the same as 'banner_id'
    //The extra "banner_id" parameter is there for added security
    public static function setBannerTextByID($id, $banner_id, $lunch_text, $altlunch_text)
    {
        $db =& JFactory::getDBO();

        $query = "UPDATE `#__eventlist_banner_values` ";
        $query .= "SET `lunch_text` = '".clean($lunch_text)."', `altlunch_text` = '".clean($altlunch_text)."' ";
        $query .= "WHERE (id='".intval($id)."') ";
        $query .= "AND (banner_id='".intval($banner_id)."') ";
        $query .= "LIMIT 1; ";

        $db->setQuery($query);
        $db->query();
    }
*/
    
    
    
    
    
}



?>