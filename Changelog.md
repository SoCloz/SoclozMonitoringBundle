#Changelog

## 1.1.0: Cleaning

- PHPDoc
- Coding style
- Type hinting
- Create StatsdInterface
- Change namespace of StatsD service (from Socloz\MonitoringBundle\Notify\StatsD to Socloz\MonitoringBundle\Notify\StatsD\StatsD)

## 1.0.0: First stable release

- Profiler does not require anymore xhprof. request data is now profiled without xhprof
- Add request_id service
- statsd data can now be sent in a single UDP packet (if you are using statsd 0.4)
- better testing
- new logger module,
- xhprof parser is much faster,
- more precise mongodb timing measurement,
- per route timings/calls tracking.