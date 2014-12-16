#Changelog

## 2014-09-18 :

- Profiler does not require anymore xhprof. request data is now profiled without xhprof

## 2014-07-16 :

- Add request_id service

## 2013-06-12 :

- statsd data can now be sent in a single UDP packet (if you are using statsd 0.4)
- better testing

## 2013-06-10 :

- new logger module,
- xhprof parser is much faster,
- more precise mongodb timing measurement,
- per route timings/calls tracking.