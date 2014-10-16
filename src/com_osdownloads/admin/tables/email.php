<?php
/**
 * @version 1.0.0
 * @author Open Source Training (www.ostraining.com)
 * @copyright (C) 2014 Open Source Training
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OsDownloadsTableEmail extends JTable
{
    public $id;
    public $email;
    public $document_id;
    public $downloaded_date;

    public function __construct(&$_db)
    {
        parent::__construct('#__osdownloads_emails', 'id', $_db);
    }

    public function store($updateNulls = false)
    {
        // Trigger events to osdownloads plugins
        JPluginHelper::importPlugin('osdownloads');
        $dispatcher = JEventDispatcher::getInstance();
        $pluginResults = $dispatcher->trigger('onOSDownloadsBeforeSaveEmail', array(&$this));

        $result = false;
        if ($pluginResults !== false) {
            $result = parent::store($updateNulls);

            $dispatcher->trigger('onOSDownloadsAfterSaveEmail', array($result, &$this));
        }

        return $result;
    }

    public function delete($pk = null)
    {
        $id = $this->id;

        // Trigger events to osdownloads plugins
        JPluginHelper::importPlugin('osdownloads');
        $dispatcher = JEventDispatcher::getInstance();
        $pluginResults = $dispatcher->trigger('onOSDownloadsBeforeDeleteEmail', array(&$this, $pk));

        $result = false;
        if ($pluginResults !== false) {
            $result = parent::delete($pk);

            $dispatcher->trigger('onOSDownloadsAfterDeleteEmail', array($result, $this->id, $pk));
        }

        return $result;
    }
}
