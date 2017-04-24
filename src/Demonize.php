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

use Assert\Assertion;

/**
 * Class Demonize
 *
 * @package Webserverdaemon
 */
class Demonize implements DemonizeInterface
{
    /**
     * Directory separator.
     *
     * @var string
     */
    protected $separator = DIRECTORY_SEPARATOR;

    /**
     * UID identifier for daemon process.
     *
     * @var string
     */
    protected $uid;

    /**
     * Document root to serve from.
     *
     * @var string
     */
    protected $documentRoot;

    /**
     * PHP binary used for executing daemon.
     *
     * @var string
     */
    protected $phpBinary;

    /**
     * Interface to listen on (IP or name).
     *
     * @var string
     */
    protected $interface;

    /**
     * Port to listen on.
     *
     * @var int
     */
    protected $port;

    /**
     * Temporary directory used for log + PID file.
     *
     * @var string
     */
    protected $tempDir;

    /**
     * Filename + path to log-file.
     *
     * @var string
     */
    protected $logFile;

    /**
     * Filename + path to PID file.
     *
     * @var string
     */
    protected $pidFile;

    /**
     * Commandline used to start daemon process.
     *
     * @var string
     */
    protected $commandline;

    /**
     * PID of the active daemon process.
     *
     * @var int
     */
    protected $pid;

    /**
     * Pipes used for STDOUT, STDIN, STDERR
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * Suffix for Log-file.
     *
     * @var string
     */
    const LOG_FILE_SUFFIX = '.log';

    /**
     * Suffix for PID-file.
     *
     * @var string
     */
    const PID_FILE_SUFFIX = '.pid';

    /**
     * Demonize constructor.
     *
     * @param string $interface    Interface to make daemon listen on.
     * @param int    $port         Port to make daemon listen on.
     * @param string $documentRoot Document root to serve content from.
     * @param string $uid          Unique Identifier used to enable multiple daemons.
     * @param string $phpBinary    PHP binary to execute.
     * @param null   $tempDir      Temporary directory to run process in and keep log and pid file.
     * @param string $commandline  Commandline being executed on daemon launch.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws \InvalidArgumentException On any invalid arguments passed to the function.
     *
     */
    public function __construct(
        $interface = '127.0.0.1',
        $port = 8000,
        $documentRoot = '.',
        $uid = 'webserver',
        $phpBinary = PHP_BINARY,
        $tempDir = null,
        $commandline = '%s -S %s:%s -t %s > %s 2>&1 & echo $!'
    )
    {
        $tempDir = ($tempDir) ? $tempDir : sys_get_temp_dir();

        // Validate passed arguments first
        if (true !== $result = $this->validateArguments(
                [
                    $interface,
                    $port,
                    $documentRoot,
                    $uid,
                    $phpBinary,
                    $tempDir,
                    $commandline,
                ]
            )
        ) {
            throw new \InvalidArgumentException(
                $result
            );
        }

        $this->interface    = $interface;
        $this->port         = $port;
        $this->documentRoot = $documentRoot;
        $this->uid          = $uid;
        $this->phpBinary    = $phpBinary;
        $this->tempDir      = $tempDir;
        $this->logFile      = sprintf('%s%s%s%s', $this->tempDir, $this->separator, $this->uid, self::LOG_FILE_SUFFIX);
        $this->pidFile      = sprintf('%s%s%s%s', $this->tempDir, $this->separator, $this->uid, self::PID_FILE_SUFFIX);

        $this->commandline = sprintf(
            $commandline,
            $this->phpBinary,
            $this->interface,
            $this->port,
            $this->documentRoot,
            $this->logFile
        );
    }

    /**
     * Validates passed arguments.
     *
     * @param array $arguments Arguments to validate against simple assertion matrix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|string TRUE on success, otherwise error as string.
     */
    protected function validateArguments(array $arguments = [])
    {
        // 1st Interface
        if (false === is_string($arguments[0]) || false === Assertion::ip($arguments[0])) {
            return sprintf(
                'Please pass a valid hostname OR an valid IPv4|IPv6 address as 1st argument. You passed "%s".',
                $arguments[0]
            );
        }

        // 2nd Port
        try {
            Assertion::numeric($arguments[1]);

        } catch (\Exception $exception) {
            return sprintf(
                'Please pass a valid port as 2nd argument. You passed "%s".',
                $arguments[1]
            );
        }

        // 3rd Document Root
        try {
            Assertion::directory($arguments[2]);

        } catch (\Exception $exception) {
            return sprintf(
                'Please pass a valid directory as 3rd argument. You passed "%s".',
                $arguments[2]
            );
        }

        // 4th UID
        try {
            Assertion::string($arguments[3]);

        } catch (\Exception $exception) {
            return sprintf(
                'Please pass a valid UID as 4th argument. You passed "%s".',
                $arguments[3]
            );
        }

        // 5th PHP Binary
        try {
            if ('php' !== $arguments[4]) {
                Assertion::file($arguments[4]);
            }

        } catch (\Exception $exception) {
            return sprintf(
                'Please pass a valid PHP binary as 5th argument. You passed "%s".',
                $arguments[4]
            );
        }

        // 6th Temp dir
        try {
            Assertion::directory($arguments[5]);

        } catch (\Exception $exception) {
            return sprintf(
                'Please pass a valid temporary directory as 6th argument. You passed "%s".',
                $arguments[5]
            );
        }

        return true;
    }

    /**
     * Cleanup filesystem (PID- and log-file).
     *
     * @param bool $pidFile TRUE (default) to delete PID-file, FALSE to do not.
     * @param bool $logFile TRUE to delete log-file, FALSE (default) to do not.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success
     */
    protected function cleanup($pidFile = true, $logFile = false)
    {
        if (true === $pidFile && true === file_exists($this->pidFile)) {
            @unlink($this->pidFile);
        }

        if (true === $logFile && true === file_exists($this->logFile)) {
            @unlink($this->logFile);
        }
    }

    /**
     * Fetches PID of configured UID from filesystem stored PID-file if exist.
     */
    protected function fetchPid()
    {
        // Try to fetch PID from file if not set || created in this instance
        if (null === $this->pid && true === file_exists($this->pidFile)) {
            if ('' !== $pid = trim(file_get_contents($this->pidFile))) {
                $this->pid = $pid;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function start($printStatus = true, $restarted = false)
    {
        // Check first if already running with UID!
        if (true === $this->status(false)) {
            $status     = false;
            $statusText = sprintf(
                'webserver-daemon (UID: %s) process (PID: %s) already running!',
                $this->uid,
                $this->pid
            );

        } else {
            $descriptorSpecification = [
                0 => ['pipe', 'r'],                     // STDIN
                1 => ['pipe', 'w'],                     // STDOUT
                2 => ['file', $this->logFile, 'a']      // STDERR
            ];

            $processHandle = proc_open($this->commandline, $descriptorSpecification, $this->pipes, $this->tempDir);

            // Check for successful creation of process
            if (true === is_resource($processHandle)) {
                $this->pid = trim(stream_get_contents($this->pipes[1]));
                file_put_contents($this->pidFile, $this->pid);
            }

            if (null === $this->pid) {
                throw new \RuntimeException(
                    sprintf('Error while starting webserver. See "%s" for details.', $this->logFile)
                );
            }

            $status     = true;
            $statusText = sprintf(
                'webserver-daemon (UID: %s) process (PID: %s) %s.',
                $this->uid,
                $this->pid,
                (true === $restarted) ? 'restarted' : 'started'
            );
        }

        if (true === $printStatus) {
            printf($statusText.PHP_EOL);
        }

        return $status;
    }

    /**
     * @inheritdoc
     */
    public function stop($printStatus = true)
    {
        $this->fetchPid();

        // Assume not running so we cannot stop it
        $status     = false;
        $statusText = sprintf(
            'webserver-daemon (UID: %s) not running so it could not be stopped.',
            $this->uid
        );

        if (null !== $this->pid) {
            $commandline = sprintf('kill %s &> /dev/null', $this->pid);
            system($commandline, $result);

            if (0 !== $result) {
                // Check for "ps" command
                exec('kill', $killCheckOutput, $killCheckResult);

                if (0 !== $killCheckResult) {
                    throw new \RuntimeException(
                        sprintf(
                            'Error killing webserver-daemon (UID: %s) process (PID %s) via "kill" (commandline: "%s")!',
                            $this->uid,
                            $this->pid,
                            $commandline
                        )
                    );
                }
            }

            $status     = true;
            $statusText = sprintf(
                'webserver-daemon (UID: %s) process (PID: %s) stopped.',
                $this->uid,
                $this->pid
            );

            $this->cleanup();
        }

        if (true === $printStatus) {
            printf($statusText.PHP_EOL);
        }

        return $status;
    }

    /**
     * @inheritdoc
     */
    public function restart($printStatus = true)
    {
        $log       = null;
        $restarted = true;

        try {
            $restarted = $restarted && $this->stop($printStatus);

        } catch (\RuntimeException $exception) {
            $restarted = false;
            $log       = $exception->getMessage();
        }

        $result = $this->start($printStatus, $restarted);

        if (null !== $log) {
            file_put_contents($this->logFile, $log, FILE_APPEND);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function status($printStatus = true)
    {
        $this->fetchPid();

        // Assume not running
        $status     = false;
        $statusText = sprintf('webserver-daemon (UID: %s) not running.', $this->uid);

        if (null !== $this->pid) {
            $commandline = sprintf('ps -p %s 2>&1', $this->pid);
            exec($commandline, $output, $result);

            if (0 !== $result) {
                $this->cleanup();

                // Check for "ps" command
                exec('ps', $psCheckOutput, $psCheckResult);

                if (0 !== $psCheckResult) {
                    throw new \RuntimeException(
                        sprintf(
                            'Error retrieving webserver-daemon (UID: %s) process status via "ps" (commandline: "%s")!',
                            $this->uid,
                            $commandline
                        )
                    );
                }
            }

            $statusText = sprintf(
                'webserver-daemon (UID: %s) process (PID: %s) not found!',
                $this->uid,
                $this->pid
            );

            foreach ($output as $responseLine) {
                if (false !== strpos($responseLine, $this->pid)) {
                    $status     = true;
                    $statusText = sprintf(
                        'webserver-daemon (UID: %s) process (PID: %s) running ...%s%s',
                        $this->uid,
                        $this->pid,
                        PHP_EOL,
                        $responseLine
                    );
                }
            }
        }

        if (true === $printStatus) {
            printf($statusText.PHP_EOL);
        }

        return $status;
    }

    /**
     * @inheritdoc
     */
    public function getPid()
    {
        $this->fetchPid();

        return $this->pid;
    }
}
