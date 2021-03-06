<?php
/**
 * Copyright 2014-2015 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Http
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */

/**
 * Unit tests for version 1.x of the PECL http extension.
 *
 * @category   Horde
 * @package    Http
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Http_Peclhttp2Test extends Horde_Test_Case
{
    private $_server;

    public function setUp()
    {
        if (!class_exists('\http\Client', false)) {
            $this->markTestSkipped('Missing PHP extension "http" or wrong version!');
        }

        $config = self::getConfig('HTTP_TEST_CONFIG');
        if ($config && !empty($config['http']['server'])) {
            $this->_server = $config['http']['server'];
        }
    }

    /**
     * @expectedException Horde_Http_Exception
     */
    public function testThrowsOnBadUri()
    {
        $client = new Horde_Http_Client(array('request' => new Horde_Http_Request_Peclhttp2()));
        $client->get('http://doesntexist/');
    }

    /**
     * @expectedException Horde_Http_Exception
     */
    public function testThrowsOnInvalidProxyType()
    {
        $client = new Horde_Http_Client(
            array(
                'request' => new Horde_Http_Request_Peclhttp2(
                    array(
                        'proxyServer' => 'localhost',
                        'proxyType' => Horde_Http::PROXY_SOCKS4
                    )
                )
            )
        );
        $client->get('http://www.example.com/');
    }

    public function testReturnsResponseInsteadOfExceptionOn404()
    {
        $this->_skipMissingConfig();
        $client = new Horde_Http_Client(array('request' => new Horde_Http_Request_Peclhttp2()));
        $response = $client->get('http://' . $this->_server . '/doesntexist');
        $this->assertEquals(404, $response->code);
    }

    public function testGetBodyAfter404()
    {
        $this->_skipMissingConfig();
        $client = new Horde_Http_Client(array('request' => new Horde_Http_Request_Peclhttp2()));
        $response = $client->get('http://' . $this->_server . '/doesntexist');
        $content = $response->getBody();
        $this->assertTrue(!empty($content));
    }

    private function _skipMissingConfig()
    {
        if (empty($this->_server)) {
            $this->markTestSkipped('Missing configuration!');
        }
    }
}
