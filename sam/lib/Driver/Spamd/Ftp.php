<?php
/**
 * Sam storage implementation for FTP access to the users' user_prefs files.
 *
 * Copyright 2003-2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author  Chris Bowlby <cbowlby@tenthpowertech.com>
 * @author  Max Kalika <max@horde.org>
 * @author  Ben Chavet <ben@horde.org>
 * @author  Jan Schneider <jan@horde.org>
 * @package Sam
 */
class Sam_Driver_Spamd_Ftp extends Sam_Driver_Spamd_Base
{
    /**
     * VFS instance.
     *
     * @var Horde_Vfs_Base
     */
    protected $_vfs;

    /**
     * Constructor.
     *
     * @param string $user   A user name.
     * @param array $params  Class parameters:
     *                       - vfs: (Horde_Vfs_Base) A VFS handle pointing to
     *                         the FTP server.
     *                       - user_prefs: (string) The file with the
     *                         user preferences, relative to the home directory.
     */
    public function __construct($user, $params = array())
    {
        foreach (array('vfs', 'user_prefs') as $param) {
            if (!isset($params[$param])) {
                throw new InvalidArgumentException(
                    sprintf('"%s" parameter is missing', $param));
            }
        }

        $this->_vfs = $params['vfs'];
        unset($params['vfs']);

        parent::__construct($user, $params);
    }

    /**
     * Retrieves user preferences from the backend.
     *
     * @throws Sam_Exception
     */
    public function retrieve()
    {
        /* Get preference file(s). */
        try {
            $content = $this->_vfs->read(
                dirname($this->_params['system_prefs']),
                basename($this->_params['system_prefs']));
            $conf = $this->_parse($content);
            $content = $this->_vfs->read(
                dirname($this->_params['user_prefs']),
                basename($this->_params['user_prefs']));
            $conf = array_merge($conf, $this->_parse($content));
        } catch (Horde_Vfs_Exception $e) {
            throw new Sam_Exception($e);
        }

        foreach ($conf as $option => $value) {
            $this->_options[$this->_mapOptionToAttribute($option)] = $value;
        }
    }

    /**
     * Stores user preferences in the backend.
     *
     * @param boolean $defaults  Whether to store the global defaults instead
     *                           of user options. Unused.
     *
     * @throws Sam_Exception
     */
    public function store($defaults = false)
    {
        /* Generate preference file. */
        $output = '# ' . _("SpamAssassin preference file generated by Sam")
            . ' (' . date('F j, Y, g:i a') . ")\n";

        foreach ($this->_options as $attribute => $value) {
            if (is_array($value)) {
                $output .= $this->_mapAttributeToOption($attribute) . ' '
                    . trim(implode(' ', $value)) . "\n";
            } else {
                $output .= $this->_mapAttributeToOption($attribute) . ' '
                    . trim($value) . "\n";
            }
        }

        /* Write preference file. */
        try {
            $this->_vfs->writeData(dirname($this->_params['user_prefs']),
                                   basename($this->_params['user_prefs']),
                                   $output,
                                   true);
        } catch (Horde_Vfs_Exception $e) {
            throw new Sam_Exception($e);
        }
    }

    /**
     * Parses the file into an option-value-hash.
     *
     * @param string $config  The configuration file contents.
     *
     * @return array  Hash with options and values.
     */
    protected function _parse($config)
    {
        $config = explode("\n", $config);
        $parsed = array();

        foreach ($config as $line) {
            // Ignore comments and whitespace.
            $line = trim(substr($line, 0, strcspn($line, '#')));
            if (!empty($line)) {
                $split = preg_split('/\s+/', $line, 2);
                if (count($split) == 2) {
                    $parsed[$split[0]] = $split[1];
                }
            }
        }

        return $parsed;
    }
}
