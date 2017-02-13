SoclozMonitoringBundle
======================

[![Build Status](https://secure.travis-ci.org/SoCloz/SoclozMonitoringBundle.png?branch=master)](http://travis-ci.org/SoCloz/SoclozMonitoringBundle)

Warning
-------

**First stable version was released and tagged, if you don't want to change anything, you should use version 1.0.0, otherwise see [CHANGELOG.md](CHANGELOG.md) to see what has changed**

Introduction
------------

A monitoring Symfony2 bundle for production servers that :

* sends emails on unhandled exceptions,
* profiles PHP code and sends profiling information to statsd,
* logs request profiling information,
* *new* adds Request IDs/pid to log files/HTTP headers to troubleshoot bugs.

Dependencies
------------

Exceptions catching : none.

Profiling : 

* *(optional)* [xhprof](http://pecl.php.net/package/xhprof)
* *(optional)* [StatsD](https://github.com/etsy/statsd) (+ any graphing backend, e.g. [Graphite](http://graphite.wikidot.com))

Profiled data
--------------

* *request* : number of HTTP requests (handled by Symfony) / duration of requests
* *mongodb* : number of mongodb calls (insert/batchInsert/update/save/remove/count/find/findOne) / total duration (requires xhprof)
* *redis* : number of redis calls / total duration (only phpredis calls are tracked - http://github.com/nicolasff/phpredis) (requires xhprof)
* *sphinx* : number of sphinx calls (query/runQueries/updateAttributes/buildExcerpts/buildKeywords) / total duration (requires xhprof)
* *curl* : number or curl calls (curl_exec/curl_multi_exec) / total duration (requires xhprof)

For each piece of data, you get :

* a global graph,
* a per route graph.

What do I get ?
---------------

Thanks to graphite, you can get some nice graphs : [TODO]

You also get the following log messages :

```
[2013-06-10 10:38:31] app.INFO: /page/page_slug : 2041 ms mongodb : 6 calls/108 ms redis : 9 calls/2 ms [] {"request_id":"785317517bccd92b4da08d88b4c09fe5","pid":5503}
```

Debugging using Request IDs
---------------------------

For each HTTP request (or script), a Request ID is computed. It enabled you to correlate logs from various services (webservices, database requests, ...).

This Request ID is forwarded to external webservices called using Guzzle using the X-RequestID HTTP header.

The Request ID is automatically :

* added to the HTTP response as a X-RequestID header,
* added to all log lines (as long as the pid),
* added to all Guzzle outgoing requests as a X-RequestID header.

If a X-RequestID HTTP header is found, its value will be used for the Request ID. If you call a SoclozMonitoringBundle powered webservice using Guzzle, the logs for the webservice will use the same request_id as the master request.

You should forward the Request ID to all the external services used. For example, you can add the Request ID to all your database queries as a SQL comment :

```php
$requestId = $this->get("socloz_monitoring.request_id")->getRequestId();
$sql = "SELECT /* {"request_id":"$requestId"} */ * FROM my_table WHERE col = ?";
```

FAQ
---

1. *Is it helpful ?*

    If you are not convinced that profiling on production servers is helpful, this module is not for you.

2. *What can I do to limit the overhead ?*

    On large sites, the recommended setup is :

* activate xhprof only on a couple servers (or on none if the overhead is really too important). The module will disable profiling if xhprof is absent.
* enable sampling. By default, all requests are profiled; you can lower the number of requests profiled by setting `socloz_monitoring.profiler.sampling` (a value 50 will profile 50% of requests).

3. *Can it profile database calls ? memcached calls ?*

    Yes (as long as the calls can be identified in xprof data). Contributions are welcomed for new parsers (mysql, pgsql, ...). See `Resources/config/profiler.xml` for examples.

4. *I don't like receiving emails on errors. Can I use rollbar/airbrake/sentry instead ?*

    Yes. Monolog already has a decent support for those tools. Please refer to the [monolog doc](https://github.com/Seldaek/monolog#log-specific-servers-and-networked-logging).

5. *Are yo hiring ?*

    Yes. If you are looking for a job in Paris, France, send a mail to techjobs AT socloz DOT com

Setup
-----

* Install `xhprof`, `statsd` and a graphing backend (i.e. `graphite`)

* Install & activate the module

* Configure the module : `socloz_monitoring.mailer.from` (source email of the exception alert mails), `socloz_monitoring.mailer.to` (destination email of the exception alert mails),
`socloz_monitoring.statsd.host` (IP address/hostname of the statsd server), `socloz_monitoring.statsd.port` (port ot the statsd server), `socloz_monitoring.statsd.prefix` (prefix of the statsd keys)

* Decide what you want to profile : `socloz_monitoring.profiling.request` (HTTP requests), `socloz_monitoring.profiling.mongodb` (MongoDB calls)

If you are using statsd version 0.4 or later :
* set `socloz_monitoring.statsd.merge_packets` to `true`
* if the statsd server isn't on the same LAN as your server, set `socloz_monitoring.statsd.packet_size` to `512`, otherwise keep the default value

The default configuration is :

```yaml
socloz_monitoring:
    exceptions:
        enable: true
        ignore: ['Symfony\Component\HttpKernel\Exception\NotFoundHttpException','Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException']
    profiler:
        enable: true
        sampling: 100
        mongodb: false
        request: false
        redis: false
        sphinx: false
    mailer:
        enable: true
        message_factory: socloz_monitoring.default_message_factory
        from: 
        to: 
    statsd:
        enable: false
        host:
        port:
        prefix: socloz_monitoring
        always_flush: false
        merge_packets: false
        packet_size: 1432
    request_id:
        enable: true
        add_pid: true
    logger:
        enable: false
```        

Graphing application data
-------------------------

It is possible to send some application data to statsd :

Counters :

```php
$container->get('socloz_monitoring.statsd')->updateStats("counter_name", $number);
```

Gauges :

```php
$container->get('socloz_monitoring.statsd')->gauge("gauge_name", $number);
```

Timers :

```php
$start = microtime(true);
// do some stuff
$end = microtime(true);
$container->get('socloz_monitoring.statsd')->timing("timer_name", ($end-$start)*1000);
```

Just make sure you don't have any name collisions between your counters/timers and the standard ones.

Stats are buffered and sent at the end of the script/request. In long running scripts, you should flush statsd regularly :

```php
$container->get('socloz_monitoring.statsd')->flush();
```

You can also configure statsd to always send data immediately :

```yaml
socloz_monitoring:
    statsd:
        always_flush: true
```
Finding data in graphite
------------------------

The statsd data is (on unmodified configs) :

* `stats.timers.prefix.{mongodb,request, ...}.{lower,mean_90,upper,upper_90}` - timing information
* `stats.prefix.{mongodb,request, ...} / stats_counts.prefix.{mongodb,request, ...}` - counters
* `stats.timers.prefix.per_route.{mongodb,request, ...}.{route}.{lower,mean_90,upper,upper_90}` - per route timing information
* `stats.prefix.per_route.{mongodb,request, ...}.{route}` - per route counters

Hints
-----

Graphite hints

* *number of mongodb calls per request* : `divideSeries(stats_counts.socloz_monitoring.mongodb, stats_counts.socloz_monitoring.request)`

Roadmap
-------

* Parsers : mysql, memcached
* Integrated graphite templates
* Tracking of events (code releases, ...)
* Rate limiting of mails

Thanks
------

Exception handling inspired by [RoxWayErrorNotifyBundle](https://github.com/szymek/RoxWayErrorNotifyBundle)

StatsD client taken from Etsy [StatsD](https://github.com/etsy/statsd)

Xhprof listener inpired by [JnsXhprofBundle](https://github.com/jonaswouters/XhprofBundle)

License
-------

This bundle is released under the MIT license (see LICENSE).
