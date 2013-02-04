<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER') or die('Restricted access');

//Predefine commonly used variables
$banner_id = JRequest::getInt('id');
$bannerEditURL = "index.php?option=com_eventlist&task=edit_banner&id=".$banner_id;

$categoryType = BannerActions::getBannerCategoryType($banner_id);

?>

<?php 
    //Show the banner
    //The problem is, now I am retrieving the banner information TWICE.
    //Overkill, but oh well...
    require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner.php');
    Banner::displayBannerByID($banner_id, JRequest::getString('main_text', SAMPLE_MAIN_TEXT), JRequest::getString('sub_text', SAMPLE_SUB_TEXT), JRequest::getString('date_text', ""), JRequest::getString('date_format')); //Show with sample text
?>
<!-- EDIT BANNER SETTINGS -->
<br />
<form action="<?php echo $bannerEditURL; ?>" method="post">
    <p><input type="submit" value="<?php echo EDIT_BANNER_DESIGN; ?>" /></p>
    <p><input type="hidden" name="action" value="settings" /></p>
</form>
<br />


<!-- EDIT DAILY TEXT SETTINGS -->
<?php 
	BannerActions::showEditBannerText($categoryType);
?>

<?php 
	//Show a different banner list depending on category!
	
	if ($categoryType == CTYPE_SINGLE)
	{
		require(BANNER.DS.'edit_banner_textlist_single.html.php'); 
	}
	elseif ($categoryType == CTYPE_SPAN)
	{
		require(BANNER.DS.'edit_banner_textlist_span.html.php'); 
	}
	else
	{
		//By default, list used banners
		require(BANNER.DS.'edit_banner_textlist_span.html.php'); 
	}
?>