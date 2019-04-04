<?php

namespace Adobe\Tests;

use Adobe\AppMeasurement;
use Adobe\AppMeasurement\Error;
use PHPUnit\Framework\TestCase;
use phpmock\phpunit\PHPMock;

class AppMeasurementTest extends TestCase
{
    use PHPMock;

    /**
     * int
     */
    const RAND_VALUE = 58909084;

    /**
     *  string
     */
    const TIME_MOMENT = '2019-01-01T12:00:00.000000Z';

    /** @var AppMeasurement */
    private $instance;

    public static $ch;

    public static $visitorCookieValue;

    public static $header = '';

    /**
     * @var int
     */
    public static $logCalls = 0;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->instance = AppMeasurement::getInstance();
        $this->instance->account = 'testAccount';
        $this->instance->visitorID = 'visitorId';
    }

    public function testInstantiation()
    {
        $this->assertFalse($this->instance->mobile);
    }

    public function testTrackWithException()
    {
        $this->instance->account = null;
        $this->expectException(Error::class);
        $this->instance->track();
    }

    public function testTrackWithImage()
    {
        ob_start();
        $this->instance->imageDimensions = 'wrongSizexwrongSize';
        $this->instance->track(['prop1' => 'testValue']);
        $output = ob_get_clean();

        $query = [
            'AQB' => 1,
            'ndh' => 1,
            't' => '%s',
            'ce' => 'UTF-8',
            'gn' => '%s',
            'c1' => 'testValue',
            'AQE' => 1,
        ];
        $tag = $this->prepareImageTag($query);

        $this->assertStringMatchesFormat(
            $tag,
            $output,
            'Image is not matching with pattern'
        );
    }

    public function testTrackLinkWithImage()
    {
        ob_start();
        $this->instance->trackLink('testlink.local', 'testType', 'test');
        $output = ob_get_clean();

        $query = [
            'AQB' => 1,
            'ndh' => 1,
            't' => '%s',
            'ce' => 'UTF-8',
            'gn' => '%s',
            'pe' => 'lnk_o',
            'pev1' => 'testlink.local',
            'pev2' => 'test',
            'AQE' => 1,
        ];
        $tag = $this->prepareImageTag($query);

        $this->assertStringMatchesFormat(
            $tag,
            $output,
            'Image is not matching with pattern for link test'
        );
    }

    public function testTrackMobile()
    {
        $_SERVER['HTTP_RANDOM_HEADER'] = 'CLIENT_ID';
        $_SERVER['SERVER_NAME'] = 'test.domain.com';
        $_COOKIE['s_vid'] = null;
        $_REQUEST['s_vid'] = null;
        $this->instance->sendFromServer = true;
        $this->instance->mobile = true;
        $this->instance->visitorID = null;
        $this->instance->manageVisitorID();
        $this->assertNotEmpty(self::$visitorCookieValue);
    }

    public function testTrackServerWithDebug()
    {
        $this->instance->sendFromServer = true;
        $this->instance->debugTracking = true;
        $this->instance->debugFilename = 'test.log';
        $this->instance->track();
        $this->assertNotEmpty(self::$ch);
        $this->assertEquals(2, self::$logCalls);
    }

    public function testClearVars()
    {
        $this->instance->channel = 'someChannel';
        $this->instance->events = 'some.event';

        $this->instance->clearVars();
        $this->assertNull($this->instance->channel);
        $this->assertNull($this->instance->events);
    }

    public function testManageVisitorID()
    {
        $_SERVER['SERVER_NAME'] = 'testServer.com';
        $_COOKIE['s_vid'] = null;
        $_REQUEST['s_vid'] = null;
        $this->instance->cookieLifetime = 'session';
        $this->instance->visitorID = null;
        $this->instance->manageVisitorID();
        $this->assertNotEmpty(self::$visitorCookieValue);
    }

    public function testTrackWithOverriding()
    {
        $this->instance->sendFromServer = true;
        $this->instance->track([
            'dynamicVariablePrefix' => 'dynamicVariablePrefix',
            'visitorID' => 'visitorID',
            'pageURL' => 'pageURL',
            'referrer' => 'referrer',
            'vmk' => 'vmk',
            'visitorMigrationKey' => 'visitorMigrationKey',
            'visitorMigrationServer' => 'visitorMigrationServer',
            'visitorMigrationServerSecure' => 'visitorMigrationServerSecure',
            'timestamp' => 'timestamp',
            'pageName' => 'pageName',
            'pageType' => 'pageType',
            'products' => 'products',
            'purchaseID' => 'purchaseID',
            'server' => 'server',
            'charSet' => 'charSet',
            'visitorNamespace' => 'visitorNamespace',
            'cookieDomainPeriods' => 'cookieDomainPeriods',
            'cookieLifetime' => 'cookieLifetime',
            'currencyCode' => 'currencyCode',
            'channel' => 'channel',
            'transactionID' => 'transactionID',
            'campaign' => 'campaign',
            'events' => 'events',
        ]);

        $this->assertNotEmpty(self::$ch);
    }

    private function prepareImageTag(array $parameters)
    {
        $url = $this->prepareLink($parameters);

        return "<img width='%d' height='%d' border='%d' alt='' src='{$url}' />";
    }

    private function prepareLink(array $parameters)
    {
        $baseUrl = 'http://testAccount.112.2o7.net/b/ss/testAccount/1/PHP-1.2.2/s58909084?';
        $queryString = urldecode(http_build_query($parameters));

        return $baseUrl . $queryString;
    }
}

/**
 * Override system calls
 */
namespace Adobe;

use Adobe\Tests\AppMeasurementTest;

function rand($min = 0, $max = null)
{
    return AppMeasurementTest::RAND_VALUE;
}

function time()
{
    return (new \DateTime(AppMeasurementTest::TIME_MOMENT))->format('U');
}

function curl_exec($ch)
{
    AppMeasurementTest::$ch = $ch;
}

function file_put_contents($filename, $data, $flags = 0, $context = null)
{
    AppMeasurementTest::$logCalls++;
}

function setcookie($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false)
{
    AppMeasurementTest::$visitorCookieValue = $value;
}
