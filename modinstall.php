<?php
/**
 * @version    CVS: 0.1.0
 * @package    Com_Ravepayments
 * @author     Olatunbosun Olanrewaju <bosunolanrewaju@gmail.com>
 * @copyright  Olatunbosun Olanrewaju
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Script file of HelloWorld component
 */
class Com_RavePaymentsInstallerScript
{

  /**
   * method to run before an install/update/uninstall method
   *
   * @return void
   */
  function preflight($type, $parent)
  {
    // Initialize variables
    $this->parentInstaller = $parent->getParent();
    $this->manifest = $this->parentInstaller->getManifest();
    $this->app = JFactory::getApplication();

    // Check if installed Joomla version is compatible
    $joomlaVersion = new JVersion;
    $canInstall   = substr($joomlaVersion->RELEASE, 0, 1) == substr((string) $this->manifest['version'], 0, 1) ? true : false;

    if (!$canInstall) {
      $this->app->enqueueMessage(sprintf('Cannot install "%s" because the installation package is not compatible with your installed Joomla version', (string) $this->manifest->name), 'error');
    }

    if (isset($this->manifest->modinstall) AND $this->manifest->modinstall instanceOf SimpleXMLElement) {
      $extNode = $this->manifest->modinstall->children()[0];

      // Re-create serializable dependency object
      $extension = (array) $extNode;
      $extension = (object) $extension['@attributes'];
      $extension->title = trim((string) $extNode != '' ? (string) $extNode : ($extNode['title'] ? (string) $extNode['title'] : (string) $extNode['name']));

      // Check if dependency has local installation package
      if (isset($extension->dir) AND is_dir($source = $this->parentInstaller->getPath('source') . '/' . $extension->dir)) {
        $extension->source = $source;
      }

      $path = ($extension->client == 'site' ? JPATH_SITE : JPATH_ADMINISTRATOR) . "/{$extension->type}s";

      if (!is_dir($path) OR !is_writable($path)) {
        $canInstallExtension = false;
        $this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $path), 'error');
      }

      if ($canInstall AND $canInstallExtension AND isset($extension->source)) {
        // Backup dependency parameters
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $q->select('params');
        $q->from('#__extensions');
        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);

        $extension->params = $db->loadResult();
      }

      $this->mod_rave_payments_forms_module = $extension;
    }
  }

  /**
   * method to run after an install/update/uninstall method
   *
   * @return void
   */
  function postflight($type, $parent)
  {
    isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();
    $extension = $this->mod_rave_payments_forms_module;

    if (isset($extension->remove) AND (int) $extension->remove > 0) {
      $this->removeExtension($extension);
    } else {
      // Install only dependency that has local installation package
      if (isset($extension->source)) {
        // Install and update dependency status
        $this->installExtension($extension);
      } elseif (!isset($this->missingDependency)) {
        $this->missingDependency = true;
      }
    }
  }

  /**
   * method to install the component
   *
   * @return void
   */
  function installExtension($extension)
  {
    // Get application object
    isset($this->app) OR $this->app = JFactory::getApplication();

    // Get database object
    $db = JFactory::getDbo();
    $q = $db->getQuery(true);

    // Build query to get dependency installation status
    $q->select('manifest_cache, custom_data');
    $q->from('#__extensions');
    $q->where("element = '{$extension->name}'");
    $q->where("type = '{$extension->type}'");
    $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

    // Execute query
    $db->setQuery($q);

    if ($status = $db->loadObject())
    {
      // Initialize variables
      $JVersion = new JVersion;
      $manifest = json_decode($status->manifest_cache);

      // Get information about the dependency to be installed
      $xml = JPATH::clean($extension->source . '/' . $extension->name . '.xml');

      if (is_file($xml) AND ($xml = simplexml_load_file($xml)))
      {
        if ($JVersion->RELEASE == (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<'))
        {
          // The dependency to be installed is newer than the existing one, mark for update
          $doInstall = true;
        }

        if ($JVersion->RELEASE != (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<='))
        {
          // The dependency to be installed might not newer than the existing one but Joomla version is difference, mark for update
          $doInstall = true;
        }
      }
    }
    elseif (isset($extension->source))
    {
      // The dependency to be installed not exist, mark for install
      $doInstall = true;
    }

    if (isset($doInstall) AND $doInstall)
    {
      // Install dependency
      $installer = new JInstaller;

      if (!$installer->install($extension->source))
      {
        $this->app->enqueueMessage(sprintf('Error installing "%s" %s', $extension->name, $extension->type), 'error');
      }
      else
      {
        $this->app->enqueueMessage(sprintf('Install "%s" %s was successfull', $extension->name, $extension->type));

        // Update dependency status
        $this->updateExtension($extension);

        // Build query to get dependency installation status
        $q = $db->getQuery(true);

        $q->select('manifest_cache, custom_data');
        $q->from('#__extensions');
        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);

        // Load dependency installation status
        $status = $db->loadObject();
      }
    }

    // Update dependency tracking
    if (isset($status))
    {
      $ext = isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
      $dep = !empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

      // Backward compatible: move all dependency data from params to custom_data column
      if (is_array($params = (isset($extension->params) AND $extension->params != '{}') ? (array) json_decode($extension->params) : null))
      {
        foreach (array('imageshow', 'poweradmin', 'sample') AS $com)
        {
          if ($com != $ext AND isset($params[$com]))
          {
            $dep[] = $com;
          }
        }
      }

      // Update dependency list
      in_array($ext, $dep) OR $dep[] = $ext;
      $status->custom_data = array_unique($dep);

      // Build query to update dependency data
      $q = $db->getQuery(true);

      $q->update('#__extensions');
      $q->set("custom_data = '" . json_encode($status->custom_data) . "'");

      // Backward compatible: keep data in this column for older product to recognize
      $manifestCache = json_decode($status->manifest_cache);
      $manifestCache->dependency = $status->custom_data;

      $q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

      // Backward compatible: keep data in this column also for another old product to recognize
      $params = is_array($params)
        ? array_merge($params, array_combine($status->custom_data, $status->custom_data))
        : array_combine($status->custom_data, $status->custom_data);

      $q->set("params = '" . json_encode($params) . "'");

      $q->where("element = '{$extension->name}'");
      $q->where("type = '{$extension->type}'");
      $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

      $db->setQuery($q);
      $db->execute();
    }
  }

  /**
   * Update dependency status.
   *
   * @param   object  $extension  Extension to update.
   *
   * @return  object  Return itself for method chaining.
   */
  protected function updateExtension($extension)
  {
    // Get object to working with extensions table
    $table = JTable::getInstance('Extension');

    // Load extension record
    $condition = array(
      'type' => $extension->type,
      'element' => $extension->name
    );

    $table->load($condition);

    // Update extension record
    $table->enabled = (isset($extension->publish)    AND (int) $extension->publish > 0) ? 1 : 0;
    $table->protected = (isset($extension->lock)        AND (int) $extension->lock > 0) ? 1 : 0;
    $table->client_id = (isset($extension->client)    AND $extension->client == 'site') ? 0 : 1;

    // Store updated extension record
    $table->store();

    // Update module instance
    if ($extension->name != 'mod_rave_payments_forms')
    {
      // Get object to working with modules table
      $module = JTable::getInstance('module');

      // Load module instance
      $module->load(array('module' => $extension->name));

      // Update module instance
      $module->title = $extension->title;
      $module->ordering = isset($extension->ordering) ? $extension->ordering : 0;
      $module->published = (isset($extension->publish) AND (int) $extension->publish > 0) ? 1 : 0;

      if ($hasPosition = (isset($extension->position) AND (string) $extension->position != ''))
      {
        $module->position = (string) $extension->position;
      }

      // Store updated module instance
      $module->store();

      // Set module instance to show in all page
      if ($hasPosition AND (int) $module->id > 0)
      {
        // Get database object
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        try
        {
          // Remove all menu assignment records associated with this module instance
          $q->delete('#__modules_menu');
          $q->where("moduleid = {$module->id}");

          // Execute query
          $db->setQuery($q);
          $db->execute();

          // Build query to show this module instance in all page
          $q->insert('#__modules_menu');
          $q->columns('moduleid, menuid');
          $q->values("{$module->id}, 0");

          // Execute query
          $db->setQuery($q);
          $db->execute();
        } catch (Exception $e)
        {
          throw $e;
        }
      }
    }

    return $this;
  }

  /**
   * Remove a dependency.
   *
   * @param   object  $extension  Extension to update.
   *
   * @return  object  Return itself for method chaining.
   */
  protected function removeExtension($extension)
  {
    // Get application object
    isset($this->app) OR $this->app = JFactory::getApplication();

    // Preset dependency status
    $extension->removable = true;

    // Get database object
    $db = JFactory::getDbo();
    $q = $db->getQuery(true);

    // Build query to get dependency installation status
    $q->select('extension_id, manifest_cache, custom_data, params');
    $q->from('#__extensions');
    $q->where("element = '{$extension->name}'");
    $q->where("type = '{$extension->type}'");
    $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

    // Execute query
    $db->setQuery($q);

    if ($status = $db->loadObject())
    {
      // Initialize variables
      $id = $status->extension_id;
      $ext = isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
      $deps = !empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

      // Update dependency tracking
      $status->custom_data = array();

      foreach ($deps AS $dep)
      {
        // Backward compatible: ensure that product is not removed
        // if ($dep != $ext)
        if ($dep != $ext AND is_dir(JPATH_BASE . '/components/com_' . $dep))
        {
          $status->custom_data[] = $dep;
        }
      }

      if (count($status->custom_data))
      {
        // Build query to update dependency data
        $q = $db->getQuery(true);

        $q->update('#__extensions');
        $q->set("custom_data = '" . json_encode($status->custom_data) . "'");

        // Backward compatible: keep data in this column for older product to recognize
        $manifestCache = json_decode($status->manifest_cache);
        $manifestCache->dependency = $status->custom_data;

        $q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

        // Backward compatible: keep data in this column also for another old product to recognize
        $params = is_array($params = (isset($status->params) AND $status->params != '{}') ? (array) json_decode($status->params) : null)
          ? array_merge($params, array_combine($status->custom_data, $status->custom_data))
          : array_combine($status->custom_data, $status->custom_data);

        $q->set("params = '" . json_encode($params) . "'");

        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);
        $db->execute();

        // Indicate that extension is not removable
        $extension->removable = false;
      }
    }
    else
    {
      // Extension was already removed
      $extension->removable = false;
    }

    if ($extension->removable)
    {
      // Disable and unlock dependency
      $this->disableExtension($extension);
      $this->unlockExtension($extension);

      // Remove dependency
      $installer = new JInstaller;

      if ($installer->uninstall($extension->type, $id))
      {
        $this->app->enqueueMessage(sprintf('"%s" %s has been uninstalled', $extension->name, $extension->type));
      }
      else
      {
        $this->app->enqueueMessage(sprintf('Cannot uninstall "%s" %s', $extension->name, $extension->type));
      }
    }

    return $this;
  }

  /**
   * Disable a dependency.
   *
   * @param   object  $extension  Extension to update.
   *
   * @return  object  Return itself for method chaining.
   */
  protected function disableExtension($extension)
  {
    // Get database object
    $db = JFactory::getDbo();
    $q = $db->getQuery(true);

    // Build query
    $q->update('#__extensions');
    $q->set('enabled = 0');
    $q->where("element = '{$extension->name}'");
    $q->where("type = '{$extension->type}'");
    $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

    // Execute query
    $db->setQuery($q);
    $db->execute();

    return $this;
  }

  /**
   * Unlock a dependency.
   *
   * @param   object  $extension  Extension to update.
   *
   * @return  object  Return itself for method chaining.
   */
  protected function unlockExtension($extension)
  {
    // Get database object
    $db = JFactory::getDbo();
    $q = $db->getQuery(true);

    // Build query
    $q->update('#__extensions');
    $q->set('protected = 0');
    $q->where("element = '{$extension->name}'");
    $q->where("type = '{$extension->type}'");
    $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

    // Execute query
    $db->setQuery($q);
    $db->execute();

    return $this;
  }

}
