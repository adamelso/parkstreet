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
---

### Clients ###

Interface: `ParkStreet\Client`

Clients connect to the resource and return the appropriate PSR-7 stream.

 * __`ParkStreet\Client\OfflineClient`__


      $client = new OfflineClient();

      echo $client->connect()->getContents();


### Feeds ###

Interface: `ParkStreet\Feed`

Feeds will return the metric data in an array format from a PSR-7 stream.

 * __`ParkStreet\Feed\JsonFeed`__

  Decodes JSON from the body of the stream feed.


    $feed = new JsonFeed();
    $units = $feed->process($stream);

    echo $units[0]['metrics']['download'][0]['value'];
