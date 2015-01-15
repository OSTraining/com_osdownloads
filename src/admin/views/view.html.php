<?php
/**
 * @package   OSDownloads
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

jimport('legacy.view.legacy');

class OSDownloadsViewAbstract extends JViewLegacy
{
    public function getLayout()
    {
        $layout = parent::getLayout();

        if (version_compare(JVERSION, '3.0', 'lt')) {
            $layout .= '.j2';
        }

        return $layout;
    }
}