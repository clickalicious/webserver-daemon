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

/**
 * Interface DemonizeInterface
 *
 * @package Webserverdaemon
 *
 * @codeCoverageIgnore
 */
interface DemonizeInterface
{
    /**
     * Starts a daemon process.
     *
     * @param bool $printStatus TRUE to print status, FALSE to do not.
     * @param bool $restarted   TRUE to signalize that call was received from @see restart()
     *
     * @throws \RuntimeException on any exceptional behavior
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function start($printStatus = true, $restarted = false);

    /**
     * Stops a daemon process.
     *
     * @param bool $printStatus TRUE to print status, FALSE to do not.
     *
     * @throws \RuntimeException on any exceptional behavior
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function stop($printStatus = true);

    /**
     * Restarts (stop & starts) a daemon process.
     *
     * @param bool $printStatus TRUE to print status, FALSE to do not.
     *
     * @throws \RuntimeException on any exceptional behavior
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function restart($printStatus = true);

    /**
     * Returns the current status of a daemon process.
     *
     * @param bool $printStatus TRUE to print status, FALSE to do not.
     *
     * @throws \RuntimeException on any exceptional behavior
     *
     * @return string Status message
     */
    public function status($printStatus = true);

    /**
     * Getter for PID.
     *
     * @return int PID of active daemon instance.
     */
    public function getPid();
}
