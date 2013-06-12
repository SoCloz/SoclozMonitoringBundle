SoclozMonitoringBundle
======================

[![Build Status](https://secure.travis-ci.org/SoCloz/SoclozMonitoringBundle.png?branch=master)](http://travis-ci.org/SoCloz/SoclozMonitoringBundle)

A monitoring Symfony2 bundle for production servers that :

* sends emails on unhandled exceptions,
* profiles PHP code and sends profiling information to statsd,
* *(new)* logs request profiling information.

Dependencies
------------

Exceptions catching : none.

Profiling : 

* [xhprof](http://pecl.php.net/package/xhprof)
* *(optional)* [StatsD](https://github.com/etsy/statsd) (+ any graphing backend, e.g. [Graphite](http://graphite.wikidot.com))

What's new ?
------------

Updated 2013/06/12 :
* statsd data can now be sent in a single UDP packet (if you are using statsd 0.4)
* better tests

Updated 2013/06/10 :
* new logger module,
* xhprof parser is much faster,
* more precise mongodb timing measurement,
* per route timings/calls tracking.

Profiled data
--------------

* *request* : number of HTTP requests (handled by Symfony) / duration of requests
* *mongodb* : number of mongodb calls (insert/batchInsert/update/save/remove/count/find/findOne) / total duration
* *redis* : number of redis calls / total duration (only phpredis calls are tracked - http://github.com/nicolasff/phpredis)
* *sphinx* : number of sphinx calls (query/runQueries/updateAttributes/buildExcerpts/buildKeywords) / total duration
* *curl* : number or curl calls (curl_exec/curl_multi_exec) / total duration

For each piece of data, you get :

* a global graph,
* a per route graph.

What do I get ?
---------------

Thanks to graphite, you can get some nice graphs : [TODO]

You also get the following log messages :

    [2013-06-10 10:38:31] app.INFO: /page/page_slug : 2041 ms mongodb : 6 calls/108 ms redis : 9 calls/2 ms [] []

FAQ
---

1. *Is it helpful ?*

    If you are not convinced that profiling on production servers is helpful, this module is not for you.

2. *What is the overhead ?*

    On large sites, the recommended setup is :

* activate xhprof only on a couple servers. The module will disable profiling if xhprof is absent.
* enable sampling. By default, all requests are profiled; you can lower the number of requests profiled by setting socloz_monitoring.profiler.sampling` (a value 50 will profile 50% of requests).

3. *Can it profile database calls ? memcached calls ?*

    Yes (as long as the calls can be identified in xprof data). Contributions are welcomed for new parsers (mysql, pgsql, ...). See `Resources/config/profiler.xml` for examples.

4. *Are yo hiring ?*

    Yes. If you are looking for a job in Paris, France, send a mail to techjobs AT closetome DOT fr

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
        logger:
            enable: false

Graphing application data
-------------------------

It is possible to send some application data to statsd :

Counters :

    $container->get('socloz_monitoring.statsd')->updateStats("counter_name", $number);

Gauges :

    $container->get('socloz_monitoring.statsd')->gauge("gauge_name", $number);

Timers :

    $start = microtime(true);
    // do some stuff
    $end = microtime(true);
    $container->get('socloz_monitoring.statsd')->timing("timer_name", ($end-$start)*1000);

Just make sure you don't have any name collisions between your counters/timers and the standard ones.

Stats are buffered and sent at the end of the script/request. In long running scripts, you should flush statsd regularly :

    $container->get('socloz_monitoring.statsd')->flush();

You can also configure statsd to always send data immediately :

    socloz_monitoring:
        statsd:
            always_flush: true

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

* *number of mongo calls per request* : `divideSeries(stats_counts.socloz_monitoring.mongodb, stats_counts.socloz_monitoring.request)`

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