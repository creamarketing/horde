<?php
/**
 * Handling distribution lists.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Server
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Server
 */

/**
 *  We need the base class
 */
require_once 'Horde/Kolab/Test/Server.php';

require_once 'Horde/Kolab/Server.php';

/**
 * Handling distribution lists.
 *
 * Copyright 2008-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category Kolab
 * @package  Kolab_Server
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Server
 */
class Horde_Kolab_Server_DistListHandlingTest extends Horde_Kolab_Test_Server
{

    /**
     * Test adding a distribution list.
     *
     * @scenario
     *
     * @return NULL
     */
    public function creatingDistributionList()
    {
        $this->given('an empty Kolab server')
            ->when('adding a distribution list')
            ->then('the result should be an object of type',
                   'Horde_Kolab_Server_Object_distlist');
    }

}
