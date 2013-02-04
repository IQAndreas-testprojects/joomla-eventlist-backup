<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER_ADMIN') or die('Restricted access');

//Predefine commonly used variables
//$banner_id = JRequest::getInt('id');
$returnURL = "index.php?option=com_eventlist&task=banner_admin";
?>

These are all currently avaiable banners.

Disabling a banner will not delete it, but will stop it from showing up, while still keeping the settings in memory.

You can also remove the banner by clicking the edit button, and then choosing the "remove banner" button. WARNING! This cannot be undone!

<a href="<?php echo $returnURL . "&action=add"; ?>"><?php echo NEW_BANNER; ?></a> <br/>

<?php 
	BannerAdmin::listAllBanners($returnURL);
?>
