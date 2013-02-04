<?php

//Define the constant that allows other pages to be viewed
define('BANNER', 		JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner');
define('BANNER_ADMIN', 	JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'admin');

//Define all redirect pages
define('LIST_BANNERS',		BANNER_ADMIN.DS.'list_banners.html.php');
define('EDIT_BANNER_ADMIN',	BANNER_ADMIN.DS.'edit_banner_admin.html.php');
//define('EDIT_BANNER_SETTINGS',  BANNER.DS.'edit_banner_settings.html.php');

//NOTE: This page is made to be embedded INSIDE "EDIT_BANNER",
//not as standalone!
//define('EDIT_BANNER_TEXT',  BANNER.DS.'edit_banner_text.html.php');


//Import the databse directly - it will be used in every function anyway
//NOTE: This is a different database than the regular banner uses!
require_once(BANNER_ADMIN.DS.'banner_admin.db.php');

require_once(BANNER.DS.'messages.php');


class BannerAdmin
{

	static $errorLog = array();
	public static function getErrors($part)
	{
		  return BannerAdmin::$errorLog[$part]; 
	}
	public static function addError($part, $msg)
	{
		  BannerAdmin::$errorLog[$part] .= $msg . "<br/>";
	}
	public static function hasErrors()
	{
		  if (count(BannerAdmin::$errorLog) > 0)
				{ return true; }
				
		  //else 
		  return false;
	}

	// ---------------- EDITING REDIRECTION ----------------------

	static function editBanners()
	{
			//Check if the owner is even allowed to edit the banner
			//$banner = BannerDatabase::getBanner(JRequest::getInt('id'));
			
			if (BannerAdmin::isAdministrator())
			{
		  	
		  		if (JRequest::getCmd('add'))
		  		{
		  			return BannerAdmin::addBanner();
		  		}
				elseif (JRequest::getCmd('save'))
				{
					return BannerAdmin::saveBanner();
				}
				elseif (JRequest::getCmd('preview'))
				{
					//Here is something nifty for ya!
					//Since the JRequest data already contains the neccessary items
					//all you have to do is navigate to the page and the POST data
					//will do the rest of the work. Glorious, eh?
					
					BannerAdmin::validateBanner();
					return EDIT_BANNER_ADMIN;
				}
				elseif (JRequest::getCmd('enable'))
				{
					return BannerAdmin::enableBanner();
				}
		  		elseif (JRequest::getCmd('disable'))
				{
					return BannerAdmin::disableBanner();
				}
				elseif (JRequest::getCmd('remove'))
				{
					return BannerAdmin::removeBanner();
				}
				elseif (JRequest::getCmd('edit'))
				{
					//Prepare for the actual editing. Goes hand in hand with
					//the "save" Cmd.
					return BannerAdmin::editBanner();	
				}
				else
				{ 
					if (JRequest::getCmd('action') == "add")
					{
						return EDIT_BANNER_ADMIN;
					}
					
					//If nothing else, display all banners as normal
					return LIST_BANNERS;
				}
		  }
		  else
		  {
				//Regular users are not allowed to edit banners
				//Just return to the default eventlist page
				return "";
		  }
	}

	static function isAdministrator()
	{
		  $user =& JFactory::getUser();

		  if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
				{ return true; }

		  //else
		  return false;
	}
	
	
	// ---------------- EDITING ----------------------
	//If they have come this far, they are already validated and allowed to edit the banner

	public static function editBanner()
	{
		//Set the appropriate JRequest values to fill the table
		$banner = BannerAdminDatabase::getBanner(JRequest::getInt('id'));
		
		if ($banner)
		{
			//List updatable settings
			//`id`, `owner`, `enabled`, `category`, `background_image`, `site_url`, `price_text`, `time_text` ";
			JRequest::setVar('id', $banner->id);
			JRequest::setVar('banner_name', $banner->name);				
			JRequest::setVar('banner_owner', $banner->owner);
			JRequest::setVar('banner_category', $banner->category);
			
			//Now go to the edit page
			return EDIT_BANNER_ADMIN;
		}
		
		//else - banner does not exist. Return to default eventlist page
		return "";
	}
	
	public static function removeBanner()
	{
		BannerAdminDatabase::removeBanner(JRequest::getInt('id'));
		return LIST_BANNERS;
	}
	
	// ---------------- VALIDATING ----------------------
	// 	Returns "true" upon errors, and updates the error log
	// EDIT: MAYBE NOT
	
	public static function validateBanner()
	{
		/*
		  if (!strlen(JRequest::getString('site_url')))
		  	{ BannerAdmin::addError('site_url', E_URL_TOO_SHORT); }
		  if (!strlen(JRequest::getString('background_image')))
		  	{ BannerAdmin::addError('background_image', E_NO_BACKGROUND_IMAGE); }
		*/
		
		//No checking needed at all. There are no required fields.
	}
	
	// ---------------- SAVING ----------------------
	
	public static function saveBanner()
	{
		BannerAdmin::validateBanner();
		if (BannerAdmin::hasErrors())
		{
			//Return and display the proper error messages
			return EDIT_BANNER_ADMIN;
		}
		//else continue
		
		//The database will automatically clean the strings. phew...
		BannerAdminDatabase::editBanner(JRequest::getInt('id'),
			 JRequest::getString('banner_name'),
			 JRequest::getInt('banner_owner'),
			 JRequest::getInt('banner_category'));
			 
   		return LIST_BANNERS;
	}
	
	public static function addBanner()
	{
		BannerAdmin::validateBanner();
		if (BannerAdmin::hasErrors())
		{
			//Return and display the proper error messages
			return EDIT_BANNER_ADMIN;
		}
		//else continue

		//Set the default header text (aka date text)
		$date_text = JRequest::getString('banner_name');
		
		if (JRequest::getInt('banner_category') == CAT_LUNCHGUIDEN)
			{ $date_text = LUNCH_DEFAULT_HEADER_TEXT; }
			
		
		//The database will automatically clean the strings. phew...
		BannerAdminDatabase::addBanner(
				JRequest::getString('banner_name'),
				JRequest::getInt('banner_owner'),
				JRequest::getInt('banner_category'),
				$date_text);
		
		return LIST_BANNERS;
	}
	
	public static function enableBanner()
	{
		BannerAdminDatabase::enableBanner(JRequest::getInt('id'));
		return LIST_BANNERS;
	}
	
	public static function disableBanner()
	{
		BannerAdminDatabase::disableBanner(JRequest::getInt('id'));
		return LIST_BANNERS;
	}
		
	// ---------------- DISPLAY ----------------------
	
	/*
	//This variable is a sortof hackish way of doing things.
	//It is difficult keeping track of it in the database otherwise.
	//Maybe it's better to use the date and bannerID as the unique identifiers?
	private static $editTextID = 0;
	public static function showEditBannerText()
	{
		if (BannerAdmin::$editTextID)
		{
			
			//Convert the date to a from that is openable by MySQL
			//$mysqlDate = date( 'Y-m-d H:i:s', $phpdate );		
			$mysqlDate = date( 'Y-m-d', $phpdate );
			$text_id = BannerDatabase::getIdByDate();
			
			include(EDIT_BANNER_TEXT);
		}
	}
	

	//$baseURL = http://stadsaktuellt.nu/index.php?option=com_eventlist&task=edit_banner&id=1
	public static function showMonthSelector($baseURL, $month, $year)
	{
		  echo '<div class="elbanner_monthselector">';

			  //Previous month
			  echo '<span class="elbanner_month">'.BannerAdmin::monthText($baseURL, $month-1, $year).'</span>';
			  
			  //Current month
			  echo '<span class="elbanner_current_month">'.BannerAdmin::monthText($baseURL, $month, $year).'</span>';
			  
			  //Next two months
			  echo '<span class="elbanner_month">'.BannerAdmin::monthText($baseURL, $month+1, $year).'</span>';
			  echo '<span class="elbanner_month">'.BannerAdmin::monthText($baseURL, $month+2, $year).'</span>';
		
		  echo '</div>';
	}

	private static function monthText($baseURL, $month, $year)
	{
		$url = $baseURL . "&m=".$month . "&y=".$year;
		return '<a href="'.$url.'">'.date("F Y", mktime(0,0,0, $month, 1, $year)).'</a>';
	}*/

	
	public static function listAllBanners($baseURL = "")
	{
		
		$bannersArray = BannerAdminDatabase::getBanners();
		
		$i = 0;
		echo '<table>';
		foreach($bannersArray as $banner)
		{
			$class = ($i++ % 2) ? "odd" : "even";

			if (!$banner->enabled)
				{ $class .= " old"; }
			
			echo '<tr class="'.$class.'">';
							
			echo '<td>'.$i.".".'</td>';	
			
			echo '<td><a href="'.$baseURL."&edit=1&id=".$banner->id.'">'.EDIT_REMOVE_BANNER.'</a></td>';
			echo '<td>'.htmlspecialchars($banner->name).'</td>';
			
			if ($banner->enabled)
			{
				echo '<td><a href="'.$baseURL."&disable=1&id=".$banner->id.'">'.CLICK_TO_DISABLE.'</a></td>';
			}
			else
			{
				echo '<td><a href="'.$baseURL."&enable=1&id=".$banner->id.'">'.CLICK_TO_ENABLE.'</a></td>';
			}
			
			echo '</tr>';
		}
		echo '</table>';
	}

	
	public static function showUsersDropdown($selectedUser = 0, $name = 'banner_owner')
	{
		$usersArray = BannerAdminDatabase::getUsers();

		echo "<select name='".$name."'>";
		
		//$selected = ($item->category_id == JRequest::getInt('fs_category', 0)) ? " selected" : "";
		//echo "<option value='0' ".$selected.">Alla kategorier</option>";

		//List all users (limit to only authors? 
		foreach($usersArray as $user)
		{
		   $selected = ($user->id == $selectedUser) ? " selected" : "";
		   echo "<option value='".$user->id."' ".$selected.">".$user->name."</option>";
		}
		
		echo "</select>";
	}

	public static function showCategoriesDropdown($selectedCategory = 0, $name = 'banner_category')
	{
		$categoriesArray = BannerAdminDatabase::getCategories();

		echo "<select name='".$name."'>";
		
		//$selected = ($item->category_id == JRequest::getInt('fs_category', 0)) ? " selected" : "";
		//echo "<option value='0' ".$selected.">Alla kategorier</option>";

		//List all users (limit to only authors? 
		foreach($categoriesArray as $category)
		{
		   $selected = ($category->id == $selectedCategory) ? " selected" : "";
		   echo "<option value='".$category->id."' ".$selected.">".$category->catname."</option>";
		}
		
		echo "</select>";
	}
	

}

?>
