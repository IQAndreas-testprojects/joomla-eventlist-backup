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

        $bannersArray;

        if ($categoryID)
        	{ $bannersArray = BannerDatabase::getActiveBannersByCategory($categoryID); }
        else
        	{ $bannersArray = BannerDatabase::getActiveBanners(); }        	
        	

        // Import CSS
        echo '<link rel="stylesheet" type="text/css" href="'.BANNER_CSS.'" />';
        echo '<div id="dagens_container">';
        foreach($bannersArray as $banner)
        {
            $text = BannerDatabase::getBannerTodayText($banner->id);

            if ($text && strlen($text->lunch_text))
            {
            	$date_text = strftime(DATEFORMAT_TIME_TEXT); //NOW by default
                Banner::showBanner($text->lunch_text, $text->altlunch_text, $banner->price_text, $banner->time_text, $date_text, $banner->site_url, $banner->background_image, $banner->name);

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

    static function displayBannerByID($bannerID, $lunch_text = "", $altlunch_text = "", $date_text = "")
    {
        //Import the databse
        require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.db.php');

        $banner = BannerDatabase::getBanner($bannerID);

        if ($banner)
        {
        	if ((!strlen($lunch_text)) && (!strlen($altlunch_text)))
        	{
        		$lunch_text = SAMPLE_LUNCH_TEXT;
        		$altlunch_text = SAMPLE_ALTLUNCH_TEXT;
        	}
        	
        	if (!strlen($date_text))
        		{ $date_text = strftime(DATEFORMAT_TIME_TEXT); } //NOW by default
        	
       		Banner::displayCustomBanner($lunch_text, $altlunch_text, $banner->price_text, $banner->time_text, $date_text, $banner->site_url, $banner->background_image, $banner->name);
        }
    }
    
    
    //Excludes the edit button ETC or any database access
    public static function displayCustomBanner($lunch_text, $altlunch_text, $price_text, $time_text, $date_text, $site_url, $background_image, $image_alt = "")
    {
    	// Import CSS
        echo '<link rel="stylesheet" type="text/css" href="'.BANNER_CSS.'" />';
    	echo '<div id="dagens_container">';
		Banner::showBanner($lunch_text, $altlunch_text, $price_text, $time_text, $date_text, $site_url, $background_image, $image_alt);
		echo '</div>';
    }
    


    static function showBanner($lunch_text, $altlunch_text, $price_text, $time_text, $date_text, $site_url, $background_image, $image_alt = "")
    {
    	//Now, make it into a giant link!!
    	echo '<a href="'.$site_url.'">';
    	
    	echo '<table class="dagens" cellpadding="0" cellspacing="0">';
		echo '<tr class="head">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td class="left_head">till restaurangens hemsida &raquo;</td>';
			echo '<td class="right_head">'.Banner::makeHTML($date_text).'</td>';
			echo '<td class="spacer">&nbsp;</td>';
		echo '</tr>';
		echo '<tr class="annons">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td><img src="'.BANNER_IMAGE_ROOT_URL.$background_image.'" alt="'.Banner::makeHTML($image_alt).'" class="dagens" /></td>';
			echo '<td>';
				echo '<h1>'.Banner::makeHTML($lunch_text).'</h1>';
				echo '<h2>'.Banner::makeHTML($altlunch_text).'</h2>';
			echo '</td>';
			echo '<td class="spacer">&nbsp;</td>';
		echo '</tr>';
		echo '<tr class="footer">';
			echo '<td class="spacer">&nbsp;</td>';
			echo '<td class="left_footer">'.Banner::makeHTML(PREFIX_TIME_TEXT.$time_text).'</td>';
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
