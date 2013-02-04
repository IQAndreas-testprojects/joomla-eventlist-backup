<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;
?>

<?php 
    //Show the banner
    //The problem is, now I am retrieving the banner information TWICE.
    //Overkill, but oh well...
    require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.php');
    Banner::displayBannerByID($banner_id, JRequest::getString('lunch_text', SAMPLE_LUNCH_TEXT), JRequest::getString('altlunch_text', SAMPLE_ALTLUNCH_TEXT), JRequest::getString('date_text', "")); //Show with sample text
?>
<!-- EDIT BANNER SETTINGS -->
<form action="<?php echo $bannerEditURL; ?>" method="post">
    <input type="submit" value="<?php echo EDIT_BANNER_DESIGN; ?>" />
    <input type="hidden" name="action" value="settings" />
</form>


<!-- EDIT DAILY TEXT SETTINGS -->
<?php 
	BannerActions::showEditBannerText();
?>

<?php 
$currentMonth = date("n");
$displayedMonth = JRequest::getInt("m", $currentMonth);

$currentYear = date("Y");
$displayedYear = JRequest::getInt("Y", $currentYear);

BannerActions::showMonthSelector($bannerEditURL, $displayedMonth, $displayedYear);
BannerActions::showBannerTextTableByMonth($banner_id, $displayedMonth, $displayedYear, $bannerEditURL);
?>

