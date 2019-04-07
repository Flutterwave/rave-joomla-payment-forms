<?php

/**
 * @version    CVS: 0.0.1
 * @package    Com_Ravepayments
 * @author     Olatunbosun Olanrewaju <bosunolanrewaju@gmail.com>
 * @copyright  Olatunbosun Olanrewaju
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Ravepayments.
 *
 * @since  1.6
 */
class RavepaymentsViewTransactionlist extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		RavepaymentsHelpersRavepayments::addSubmenu('transactionlist');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = RavepaymentsHelpersRavepayments::getActions();

		JToolBarHelper::title(JText::_('COM_RAVEPAYMENTS_TITLE_TRANSACTIONLIST'), 'transactionlist.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/records';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('records.add', 'JTOOLBAR_NEW');
				JToolbarHelper::custom('transactionlist.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('records.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('transactionlist.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('transactionlist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'transactionlist.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('transactionlist.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('transactionlist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'transactionlist.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('transactionlist.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_ravepayments');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_ravepayments&view=transactionlist');

		$this->extra_sidebar = '';
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`tx_ref`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_TX_REF'),
			'a.`customer_email`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_CUSTOMER_EMAIL'),
			'a.`amount`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_AMOUNT'),
			'a.`status`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_STATUS'),
			'a.`module`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_MODULE'),
			'a.`date`' => JText::_('COM_RAVEPAYMENTS_TRANSACTIONLIST_DATE'),
		);
	}
}
