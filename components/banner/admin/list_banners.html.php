<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
defined('BANNER_ADMIN') or die('Restricted access');

//Predefine commonly used variables
//$banner_id = JRequest::getInt('id');
$returnURL = "index.php?option=com_eventlist&task=banner_admin";
?>

<p>Detta är alla tillgängliga annonsbanners. 

Inaktivering av en banner kommer inte att ta bort den. Dess inställningar är kvar, men annonsen kommer inte vara publicerad.<br /><br />

Du kan ta bort en annonsbanner genom att klicka Ändra/Ta bort, och sedan välja "Ta bort"-knappen. VARNING! Annonsen raderas då permanent!</p><br />

<a href="<?php echo $returnURL . "&action=add"; ?>"><?php echo NEW_BANNER; ?></a> <br /><br />

<?php 
	BannerAdmin::listAllBanners($returnURL);
?>