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

Feeds will return the metric data in an array format from a PSR-7 stream.

 * __`ParkStreet\Feed\JsonFeed`__

  Decodes JSON from the body of the stream feed.
