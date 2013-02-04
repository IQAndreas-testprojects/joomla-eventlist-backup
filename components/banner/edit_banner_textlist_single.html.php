<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;
?>

<?php 
$currentMonth = date("n");
$displayedMonth = JRequest::getInt("m", $currentMonth, "&nbsp;");

$currentYear = date("Y");
$displayedYear = JRequest::getInt("Y", $currentYear);

BannerActions::showMonthSelector($bannerEditURL, $displayedMonth, $displayedYear);
BannerActions::showBannerTextTableByMonth($banner_id, $displayedMonth, $displayedYear, $bannerEditURL);
?>
