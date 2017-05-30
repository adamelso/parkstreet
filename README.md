Park Street
===========

PHP traffic meter.


Quick Start
-----------

    $ composer install
    $ bin/parkstreet


Commands
--------

### `info` (default) ###

Brings up information about running the command.


API
-------

### Feeds ###

Interface: `ParkStreet\Feed`

Feeds will return the metric data in an array format from any source.

 * __`ParkStreet\Feed\Psr7Feed`__

  Decodes JSON from the body of any PSR-7 compatible feed.
