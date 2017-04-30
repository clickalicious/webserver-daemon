<?php

/**
 * (The MIT license)
 * Copyright 2017 clickalicious, Benjamin Carl
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Clickalicious\Webserver\Daemon;

use Clickalicious\Rng\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class ScriptHandlerTest
 *
 * @package Webserverdaemon
 * @author  Benjamin Carl <opensource@clickalicious.de>
 */
class DemonizeTest extends TestCase
{
    /**
     * UID of daemon process for testing.
     *
     * @var string
     */
    protected $uid;

    /**
     * Port used for test daemon instance.
     *
     * @var int
     */
    protected $port;

    /**
     * Contains list of processed UIDs to be cleaned up in case of error while testing.
     *
     * @var array
     */
    protected static $listOfUids = [];

    /**
     * Lowest possible port for randomizer.
     *
     * @var int
     */
    const PORT_MIN = 60000;

    /**
     * Highest possible port for randomizer.
     *
     * @var int
     */
    const PORT_MAX = 65534;

    /**
     * Whether to print result or nor.
     *
     * @var bool
     */
    const PRINT_RESULT = false;

    /**
     * setUp.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     */
    protected function setUp()
    {
        $randomizer = new Generator(
            Generator::MODE_OPEN_SSL,
            (int)str_replace('.', '', microtime(true))
        );

        $this->uid  = sha1($randomizer->getRandomBytes(64));
        $this->port = $randomizer->generate(self::PORT_MIN, self::PORT_MAX);

        self::$listOfUids[] = $this->uid;
    }

    /**
     * Tidy-up after unit tests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        foreach (self::$listOfUids as $uid) {
            $logFile = sprintf('%s%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, $uid, Demonize::LOG_FILE_SUFFIX);
            $pidFile = sprintf('%s%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, $uid, Demonize::PID_FILE_SUFFIX);

            // Check if exists and if is file ...
            if (true === file_exists($logFile) && true === is_file($logFile)) {
                unlink($logFile);
            }

            // Check if exists and if is file ...
            if (true === file_exists($pidFile) && true === is_file($pidFile)) {
                unlink($pidFile);
            }
        }
    }

    /**
     * testCreatingInstanceWithDefaults.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     */
    public function testCreatingInstanceWithDefaults()
    {
        $instance = new Demonize();
        self::assertInstanceOf('\\Clickalicious\\Webserver\\Daemon\\Demonize', $instance);
    }

    /**
     * testCreatingInstanceWithCustomValidArgument.
     *
     * @param array $arguments Arguments for creating instance.
     *
     * @dataProvider provideValidArguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     */
    public function testCreatingInstanceWithCustomValidArgument(array $arguments)
    {
        // Enrich with mock data
        $arguments = $this->injectRandomizedMockData($arguments);

        $reflection = new \ReflectionClass('\\Clickalicious\\Webserver\\Daemon\\Demonize');
        /* @var $instance Demonize*/
        $instance   = $reflection->newInstanceArgs($arguments);

        self::assertInstanceOf('\\Clickalicious\\Webserver\\Daemon\\Demonize', $instance);
    }

    /**
     * Test: Start, Status, Restart, Stop Daemon with valid Arguments.
     *
     * @param array $arguments Arguments for creating instance.
     *
     * @dataProvider provideValidArguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     */
    public function testStartStatusRestartStopDaemonWithCustomValidArgument(array $arguments)
    {
        // Enrich with mock data
        $arguments = $this->injectRandomizedMockData($arguments);

        $reflection = new \ReflectionClass('\\Clickalicious\\Webserver\\Daemon\\Demonize');
        /* @var $instance Demonize*/
        $instance   = $reflection->newInstanceArgs($arguments);

        $result = $instance->start(self::PRINT_RESULT);
        self::assertTrue($result);

        $result = $instance->status(self::PRINT_RESULT);
        self::assertTrue($result);

        $result = $instance->start(self::PRINT_RESULT);
        self::assertFalse($result);

        $result = $instance->restart(self::PRINT_RESULT);
        self::assertTrue($result);

        $result = $instance->stop(self::PRINT_RESULT);
        self::assertTrue($result);
    }

    /**
     * Test: Start, Status, Restart, Stop Daemon with valid Arguments.
     *
     * @param array $arguments Arguments for creating instance.
     *
     * @dataProvider provideInvalidArguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @expectedException \InvalidArgumentException
     */
    public function testStartStatusRestartStopDaemonWithCustomInvalidArgument(array $arguments)
    {
        $reflection = new \ReflectionClass('\\Clickalicious\\Webserver\\Daemon\\Demonize');
        /* @var $instance Demonize*/
        $instance   = $reflection->newInstanceArgs($arguments);

        $result = $instance->start(self::PRINT_RESULT);
        self::assertFalse($result);

        /*
            $result = $instance->status(self::PRINT_RESULT);
            self::assertTrue($result);

            $result = $instance->restart(self::PRINT_RESULT);
            self::assertTrue($result);

            $result = $instance->stop(self::PRINT_RESULT);
            self::assertTrue($result);
        */
    }

    /**
     * Injects generated mock data for each test before test is executed.
     *
     * @param array $dataSet Set of data to enrich with randomized mock data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Data set enriched with mock data.
     */
    protected function injectRandomizedMockData(array $dataSet = [])
    {
        switch (count($dataSet)) {
            case 2:
                $dataSet[1] = $this->port;
                break;
            case 3:
                $dataSet[1] = $this->port;
                break;
            case 4:
                $dataSet[1] = $this->port;
                $dataSet[3] = $this->uid;
                break;
            case 5:
                $dataSet[1] = $this->port;
                $dataSet[3] = $this->uid;
                break;
            case 6:
                $dataSet[1] = $this->port;
                $dataSet[3] = $this->uid;
                break;
            case 7:
                $dataSet[1] = $this->port;
                $dataSet[3] = $this->uid;
                break;
            default:
                break;
        }

        return $dataSet;
    }

    /**
     * Data provider for valid test data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Valid test data.
     *
     * @codeCoverageIgnore
     */
    public function provideValidArguments()
    {

        return [
            'interface' => [
                [
                    '127.0.0.1',
                ]
            ],
            'interface, port' => [
                [
                    '127.0.0.1',
                    null
                ]
            ],
            'interface, port, documentRoot' => [
                [
                    '127.0.0.1',
                    null,
                    sys_get_temp_dir(),
                ]
            ],
            'interface, port, documentRoot, uid' => [
                [
                    '127.0.0.1',
                    null,
                    sys_get_temp_dir(),
                    null,
                ]
            ],
            'interface, port, documentRoot, uid, phpBinary' => [
                [
                    '127.0.0.1',
                    null,
                    sys_get_temp_dir(),
                    null,
                    PHP_BINARY,
                ]
            ],
            'interface, port, documentRoot, uid, phpBinary, tempDir' => [
                [
                    '127.0.0.1',
                    null,
                    sys_get_temp_dir(),
                    null,
                    PHP_BINARY,
                    sys_get_temp_dir(),
                ]
            ],
            'interface, port, documentRoot, uid, phpBinary, tempDir, commandLine' => [
                [
                    '127.0.0.1',
                    null,
                    sys_get_temp_dir(),
                    null,
                    PHP_BINARY,
                    sys_get_temp_dir(),
                    '%s -S %s:%s -t %s > %s 2>&1 & echo $!',
                ]
            ],
        ];
    }

    /**
     * Data provider for invalid test data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Valid test data.
     *
     * @codeCoverageIgnore
     */
    public function provideInvalidArguments()
    {

        return [
            'interface' => [
                [
                    1,
                ]
            ],
            'interface, port' => [
                [
                    '127.0.0.1',
                    'three'
                ]
            ],
            'interface, port, documentRoot' => [
                [
                    '127.0.0.1',
                    9999,
                    'invaliddirectory',
                ]
            ],
            'interface, port, documentRoot, uid' => [
                [
                    '127.0.0.1',
                    9999,
                    sys_get_temp_dir(),
                    null,
                ]
            ],
            'interface, port, documentRoot, uid, phpBinary' => [
                [
                    '127.0.0.1',
                    9999,
                    sys_get_temp_dir(),
                    'foobar',
                    'nophp',
                ]
            ],
            'interface, port, documentRoot, uid, phpBinary, tempDir' => [
                [
                    '127.0.0.1',
                    9999,
                    sys_get_temp_dir(),
                    'foobar',
                    PHP_BINARY,
                    'invaliddirectory',
                ]
            ],
        ];
    }
}
