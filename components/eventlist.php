<?php
/**
 * @version 1.0 $Id: eventlist.php 958 2009-02-02 17:23:05Z julienv $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

//Require helperfile
require_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'helper.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'user.class.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'image.class.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'output.class.php');

//perform cleanup if it wasn't done today (archive, delete, recurrence)
ELHelper::cleanup();


// ANDREAS RENBERG May 2010
// If the script calls for the banner to be modified,
// Hijack this code, and go do the banner modifications instead.
// Modified all the code until the end


$task = JRequest::getCmd("task");
$next_page = "";

//As a speed optimization, don't switch the task
//or even import unless the task is actually set
if ($task)
{

    switch ($task)
    {
        case "edit_banner" :
            require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner_actions.php');
            $next_page = BannerActions::editBanner();
            break;
            
        case "banner_admin" :
            require_once(JPATH_BASE.DS.'components'.DS.'com_eventlist'.DS.'banner'.DS.'banner_admin.php');
            $next_page = BannerAdmin::editBanners();
            break;

        default :
            //NADA
            break;
    }
}

if (strlen($next_page))
{
    //Load whatever page is required next
    require_once($next_page);
}
else    //RESUME NORMAL ACTIVITY
{

    // Set the table directory
    JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

    // Require the controller
    require_once (JPATH_COMPONENT.DS.'controller.php');

    // Create the controller
    $classname  = 'EventListController';
    $controller = new $classname( );

    // Perform the Request task
    $controller->execute( JRequest::getVar('task', null, 'default', 'cmd') );

    // Redirect if set by the controller
    $controller->redirect();

}
?>
