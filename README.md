SoclozMonitoringBundle
======================

A monitoring Symfony2 bundle for production servers that :

* sends emails on unhandled exceptions,
* profiles PHP code and sends profiling information to statsd.

Dependencies
------------

Exceptions catching : none.

Profiling :

* [xhprof](http://pecl.php.net/package/xhprof)
* [StatsD](https://github.com/etsy/statsd) (+ any graphing backend, e.g. [Graphite](http://graphite.wikidot.com))

Profiled data
--------------

* *request* : number of HTTP requests (handled by Symfony) / duration of requests
* *mongodb* : number of mongodb calls (insert/batchInsert/update/save/remove/count/find/findOne) / total duration
    Nota bene : mongo cursors are not tracked

FAQ
---

1. *Is it helpful ?*

    If you are not convinced that profiling on production servers is helpful, this module is not for you.

2. *What is the overhead ?*

    For now, xhprof is activated on every request. Sampling is planned in the future. On large sites, the recommended setup is :

* activate xhprof only on a couple servers. The module will disable profiling if xhprof is absent.
* enable sampling when it will be implemented.

3. *Can it profile database calls ? memcached/redis calls ?*

    Yes (as long as the calls can be identified in xprof data). Contributions are welcomed for new parsers (mysql, pgsql, ...). See `Resources/config/profiler.xml` for examples.

Setup
-----

* Install dependencies

* Install & activate the module

* Configure the module : `socloz_monitoring.mailer.from` (source email of the exception alert mails), `socloz_monitoring.mailer.to` (destination email of the exception alert mails),
`socloz_monitoring.statsd.host` (IP address/hostname of the statsd server), `socloz_monitoring.statsd.port` (port ot the statsd server), `socloz_monitoring.statsd.prefix` (prefix of the statsd keys)

* Decide what you want to profile : `socloz_monitoring.profiling.request` (HTTP requests), `socloz_monitoring.profiling.mongodb` (MongoDB calls)
    
The statsd data is (on unmodified configs - type = mongodb or request) : 

* `stats.timers.prefix.type.{lower,mean_90,upper,upper_90}` - timing information
* `stats.prefix.type / stats_counts.prefix.type` - counters

The default configuration is :

    socloz_monitoring:
        exceptions:
            enable: true
            ignore: ['Symfony\Component\HttpKernel\Exception\NotFoundHttpException']
        profiler:
            enable: true
            mongodb: false
            request: false
        mailer:
            enable: true
            from: 
            to: 
        statsd:
            enable: false
            host:
            port:
            prefix: socloz_monitoring

Hints
-----

Graphite hints

* *number of mongo calls per request* : divideSeries(stats_counts.prefix.mongodb, stats_counts.prefix.request)

Roadmap
-------

* Parsers : redis
* Sampling
* Composer config

Thanks
------

Exception handling inspired by [RoxWayErrorNotifyBundle](https://github.com/szymek/RoxWayErrorNotifyBundle)

StatsD client taken frm Etsy [StatsD](https://github.com/etsy/statsd)

Xhprof listener inpired by [JnsXhprofBundle](https://github.com/jonaswouters/XhprofBundle)

License
-------

This bundle is under the MIT license (see LICENSE).