<img src="https://avatars2.githubusercontent.com/u/514566?v=3&u=4615dfc4970d93dea5d3eaf996b7903ee6e24e20&s=140" align="right" />
---

![Logo of webserverdaemon](docs/logo-large.png)

Small utility to **demonize PHP's internal webserver**.

| [![Travis branch](https://img.shields.io/travis/clickalicious/webserverdaemon/master.svg)](https://travis-ci.org/clickalicious/webserverdaemon) 	| [![Codacy branch grade](https://img.shields.io/codacy/grade/8c129b9effb64446a8d2d30eaf305679/master.svg)](https://www.codacy.com/app/clickalicious/webserverdaemon?utm_source=github.com&utm_medium=referral&utm_content=clickalicious/webserverdaemon&utm_campaign=Badge_Grade) 	| [![Codacy coverage](https://img.shields.io/codacy/coverage/c73c519d18dd4d6ca703271b4d5faccf.svg)](https://www.codacy.com/app/clickalicious/webserverdaemon?utm_source=github.com&utm_medium=referral&utm_content=clickalicious/webserverdaemon&utm_campaign=Badge_Grade) 	| [![clickalicious open source](https://img.shields.io/badge/clickalicious-open--source-green.svg?style=flat)](https://www.clickalicious.de/) 	|
|---	|---	|---	|---	|
| [![GitHub release](https://img.shields.io/github/release/clickalicious/webserverdaemon.svg?style=flat)](https://github.com/clickalicious/webserverdaemon/releases) 	| [![Waffle.io](https://img.shields.io/waffle/label/clickalicious/webserverdaemon/in%20progress.svg)](https://waffle.io/clickalicious/webserverdaemon)  	| [![Issue Stats](https://img.shields.io/issuestats/i/github/clickalicious/webserverdaemon.svg)](https://github.com/clickalicious/webserverdaemon/issues) 	| [![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://opensource.org/licenses/MIT)  	|


## Table of Contents

- [Features](#features)
- [Example](#example)
- [Requirements](#requirements)
- [Philosophy](#philosophy)
- [Versioning](#versioning)
- [Roadmap](#roadmap)
- [Security-Issues](#security-issues)
- [License »](LICENSE)


## Features

 - Demonize PHP's internal webserver by sending process to background
 - Without `nohup` dependency
 - Control webserver with well known `start`, `stop`, `restart` & `status` command
 - Simple PID- and Logfile abstraction
 - Multi process support
 - High-quality & stable codebase (following PSR standards e.g. `PSR-1,2,4`)
 - Built on top of good PHP libraries
 - PHP >= 7.2 ready
 - Clean + well documented code
 - Unit-tested with a good coverage


## Example

We provided some examples in the directory "[demo](demo/)" on How-To use the library and wrapper around PHP's internal webserver: 

### PHP
An example on how to use the library in PHP context:
```php
<?php
 
// Create an instance of PHP's internal webserver
$webserverDaemon = new \Webserverdaemon\Demonize(
    $interface,
    $port,
    $documentRoot,
    $uid,
    $phpBinary,
    $tempDir
);

// Daemon control
$webserverDaemon->start();
$webserverDaemon->restart();
$webserverDaemon->stop();

// Get PID
$webserverDaemon->start();
$webserverDaemon->getPid();

```


### Start
The following simple example shows how the daemon can be `started`:
[Demo START daemon »](demo/start.php)

Use this command for execution of the demo:
```shell
$> php demo/start.php
```

### Stop
The following simple example shows how the daemon can be `stopped`:
[Demo STOP daemon »](demo/stop.php)

Use this command for execution of the demo:
```shell
$> php demo/stop.php
```

### Restart
The following simple example shows how the daemon can be `restarted`:
[Demo RESTART daemon »](demo/restart.php)

Use this command for execution of the demo:
```shell
$> php demo/restart.php
```

### Status
The following simple example shows how the `status` of the daemon can be queried:
[Demo STATUS daemon »](demo/status.php)

Use this command for execution of the demo:
```shell
$> php demo/status.php
```


## Requirements

 - `PHP >= 5.6` (compatible up to version 5.6 as well as >= 7.2)


## Philosophy

This library provides the functionality to daemonize PHP's internal webserver and send the process to background without blocking the `console` or process starting the internal webserver. It uses PHP's pipes to register an I/O stream filter and fetches  


## Versioning

For a consistent versioning i decided to make use of `Semantic Versioning 2.0.0` http://semver.org. Its easy to understand, very common and known from many other software projects.


## Roadmap

- [ ] Target stable release `1.0.0`
- [ ] `>= 90%` test coverage

[![Throughput Graph](https://graphs.waffle.io/clickalicious/webserverdaemon/throughput.svg)](https://waffle.io/clickalicious/webserverdaemon/metrics)


## Security Issues

If you encounter a (potential) security issue don't hesitate to get in contact with us `opensource@clickalicious.de` before releasing it to the public. So i get a chance to prepare and release an update before the issue is getting shared. Thank you!


## Participate & Share

... yeah. If you're a code monkey too - maybe we can build a force ;) If you would like to participate in either **Code**, **Comments**, **Documentation**, **Wiki**, **Bug-Reports**, **Unit-Tests**, **Bug-Fixes**, **Feedback** and/or **Critic** then please let me know as well!
<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=webserverdaemon%20-%20Small%20utility%20to%20demonize%20PHP%27s%20internal%20webserver%20%40phpfluesterer%20%23webserverdaemon%20%23php%20https%3A%2F%2Fgithub.com%2Fclickalicious%2Fwebserverdaemon&tw_p=tweetbutton" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>

## Sponsors

Thanks to our sponsors and supporters:

| JetBrains | Navicat |
|---|---|
| <a href="https://www.jetbrains.com/phpstorm/" title="PHP IDE :: JetBrains PhpStorm" target="_blank"><img src="https://resources.jetbrains.com/assets/media/open-graph/jetbrains_250x250.png" height="55"></img></a> | <a href="http://www.navicat.com/" title="Navicat GUI - DB GUI-Admin-Tool for MySQL, MariaDB, SQL Server, SQLite, Oracle & PostgreSQL" target="_blank"><img src="http://upload.wikimedia.org/wikipedia/en/9/90/PremiumSoft_Navicat_Premium_Logo.png" height="55" /></a>  |


###### Copyright
<div>Icons made by <a href="http://www.flaticon.com/authors/designmodo" title="Designmodo">Designmodo</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
