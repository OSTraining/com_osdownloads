<?php

define('_JEXEC', 1);

define('MOCKUP_PATH', 'tests/_support/mockup');
define('JPATH_ADMINISTRATOR', MOCKUP_PATH . '/administrator');

require MOCKUP_PATH . '/jimport.php';
require MOCKUP_PATH . '/JComponentRouterBase.php';
require MOCKUP_PATH . '/JLog.php';
require MOCKUP_PATH . '/JError.php';
require MOCKUP_PATH . '/JText.php';
require MOCKUP_PATH . '/ArrayHelper.php';
require MOCKUP_PATH . '/RouterBase.php';
require MOCKUP_PATH . '/OSDFreeFactory.php';
require MOCKUP_PATH . '/JFactory.php';
require MOCKUP_PATH . '/Menus.php';
require MOCKUP_PATH . '/AllediaFactory.php';
