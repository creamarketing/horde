<?php
/**
 * Defines the AJAX interface for Ingo.
 *
 * Copyright 2012-2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Ajax_Application extends Horde_Core_Ajax_Application
{
    /**
     */
    protected function _init()
    {
        global $registry;

        switch ($registry->getView()) {
        case $registry::VIEW_SMARTMOBILE:
            $this->addHandler('Ingo_Ajax_Application_Smartmobile');
            break;
        }
    }

}
