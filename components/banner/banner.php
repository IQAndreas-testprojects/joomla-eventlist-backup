<?php

require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'messages.php');


//The Banner class should be as minimalist as possible!!
//Instead use the BannerActions class for actual editing
//and redirection.
class Banner
{
	public static function displayAdminLink()
	{
		echo '<a href="http://stadsaktuellt.nu/index.php?option=com_eventlist&task=banner_admin">';
		echo '<img src="'.EDIT_IMAGE.'" />'.ADD_REMOVE_BANNERS;
		echo '</a>';
	}
	
	
	public static function displayTodayBanners($categoryID = 0)
	{
		//Import the databse
		require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.db.php');
	
		$user =& JFactory::getUser();
		$admin = false;

		if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
			{ $admin = true; }

		/* For now, it is better to REQUIRE there to be a category!
		$bannersArray;

		if ($categoryID)
			{ $bannersArray = BannerDatabase::getActiveBannersByCategory($categoryID); }
		else
			{ $bannersArray = BannerDatabase::getActiveBanners(); }			
	   	*/
		
		
		$bannersArray = BannerDatabase::getActiveBannersByCategory($categoryID);
			

		// Import CSS
		echo '<link rel="stylesheet" type="text/css" href="'.BANNER_CSS.'" />';
		echo '<div id="dagens_container">';
		foreach($bannersArray as $banner)
		{
			//$text = BannerDatabase::getBannerTodayText($banner->id);
			$text = BannerDatabase::getSpanBannerTodayText($banner->id);
			
			if ($text && strlen($text->main_text))
			{
				//$date_text = strftime(DATEFORMAT_TIME_TEXT); //NOW by default
				$date_text = $banner->date_text . " " . strftime($banner->date_format);
				
				//Figure out the "lhead" text based on category
				$lhead_text = "";
				if ($banner->category == CAT_LUNCHGUIDEN)
					{ $lhead_text = SAMPLE_LHEAD_TEXT; }
				
				Banner::showBanner($text->main_text, $text->sub_text, $banner->price_text, $banner->time_text, $date_text, $lhead_text, $banner->site_url, $banner->background_image, $banner->name);

				//Show edit button if current owner or administrator
				if ($admin || ($user->id == $banner->owner))
				{
					echo '<a href="http://stadsaktuellt.nu/index.php?option=com_eventlist&task=edit_banner&id='.$banner->id.'">';
					echo '<img src="'.EDIT_IMAGE.'" />';
					echo '</a>';
				}
			}
			else
			{
				//If there is no text for the day, but the current user is still the banner owner,
				//Show the option to edit the banner anyway.
				if ($admin || ($user->id == $banner->owner))
				{
					echo '<a href="http://stadsaktuellt.nu/index.php?option=com_eventlist&task=edit_banner&id='.$banner->id.'">';
					echo '<img src="'.EDIT_IMAGE.'" />';
					echo '&nbsp;' . Banner::makeHTML($banner->name) . '&nbsp; - &nbsp;' . NO_BANNER_TEXT_TODAY;
					echo '</a>';
				}
			}
		}
		echo '</div>';
	}

	static function displayBannerByID($bannerID, $main_text = "", $sub_text = "", $date_text = "", $date_format = "")
	{
		//Import the databse
		require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.db.php');

		$banner = BannerDatabase::getBanner($bannerID);

		if ($banner)
		{
			if ((!strlen($main_text)) && (!strlen($sub_text)))
			{
				$main_text = SAMPLE_MAIN_TEXT;
				$sub_text =  SAMPLE_SUB_TEXT;
			}
			
			if (!strlen($date_text))
			{
				if (!strlen($date_format))
					{ $date_format = $banner->date_format; }
					
				//DO NOT DEFAULT TO THE 'messages.php' DEFAULT DATE FORMAT!!
				//Sometimes, dates will not show up, therefore, the date should not
				//be dispalyed!
					
				//$date_text = strftime(SAMPLE_DATE_TEXT_DATEFORMAT); 
				$date_text = $banner->date_text . " " . strftime($date_format); 
			} 
			
			//Figure out the "lhead" text based on category
			$lhead_text = "";
			if ($banner->category == CAT_LUNCHGUIDEN)
				{ $lhead_text = SAMPLE_LHEAD_TEXT; }
			
	   		Banner::displayCustomBanner($main_text, $sub_text, $banner->price_text, $banner->time_text, $date_text, $lhead_text, $banner->site_url, $banner->background_image, $banner->name);
		}
	}
	
	
	//Excludes the edit button ETC or any database access
	public static function displayCustomBanner($main_text, $sub_text, $price_text, $time_text, $date_text, $lhead_text, $site_url, $background_image, $image_alt = "")
	{
		// Import CSS
		echo '<link rel="stylesheet" type="text/css" href="'.BANNER_CSS.'" />';
		echo '<div id="dagens_container">';
		Banner::showBanner($main_text, $sub_text, $price_text, $time_text, $date_text, $lhead_text, $site_url, $background_image, $image_alt);
		echo '</div>';
	}
	

	
	//Displays the RAW banner, with no div or edit buttons
	static function showBanner($main_text, $sub_text, $price_text, $time_text, $date_text, $lhead_text, $site_url, $background_image, $image_alt = "")
	{
		//Now, make it into a giant link!!
		echo '<a target="_blank" href="'.$site_url.'">';
		
		echo '<table class="dagens" cellpadding="0" cellspacing="0">';
		echo '<tr class="head">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td class="left_head">'.Banner::makeHTML($lhead_text).'</td>';
			echo '<td class="right_head">'.Banner::makeHTML($date_text).'</td>';
			echo '<td class="spacer">&nbsp;</td>';
		echo '</tr>';
		echo '<tr class="annons">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td><img src="'.BANNER_IMAGE_ROOT_URL.$background_image.'" alt="'.Banner::makeHTML($image_alt).'" class="dagens" /></td>';
			echo '<td>';
				echo '<h1 class="dagens">'.Banner::makeHTML($main_text).'</h1>';
				echo '<h6 class="dagens">'.Banner::makeHTML($sub_text).'</h2>';
			echo '</td>';
			echo '<td class="spacer">&nbsp;</td>';
		echo '</tr>';
		echo '<tr class="footer">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td class="left_footer">'.Banner::makeHTML($time_text).'</td>';
			echo '<td class="right_footer">'.Banner::makeHTML($price_text).'</td>';
			echo '<td class="spacer">&nbsp;</td>';
		echo '</tr>';
		echo '</table>';
		
		echo '</a>';
	}
	
	public static function makeHTML($string)
	{
		return str_replace("\n", '<br/>', htmlspecialchars($string));
	}

}
?>
