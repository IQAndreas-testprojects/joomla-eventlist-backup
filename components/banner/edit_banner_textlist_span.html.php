<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;
?>

<?php 

//Display the link for adding banners
$addTextURL = $bannerEditURL . "&action=add_text";
echo '<a href="'.$addTextURL.'">'.ADD_BANNER_TEXT.'</a><br/>';

//Going to try something risky here and display ALL banners!
BannerActions::showAllBannerTextTable($banner_id, $bannerEditURL);

?>
