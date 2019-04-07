<?php
/**
 * @version    CVS: 0.0.1
 * @package    Com_Ravepayments
 * @author     Olatunbosun Olanrewaju <bosunolanrewaju@gmail.com>
 * @copyright  Olatunbosun Olanrewaju
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Ravepayments', JPATH_COMPONENT);
JLoader::register('RavepaymentsController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Ravepayments');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
