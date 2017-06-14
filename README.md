Park Street
===========

PHP traffic meter.


Quick Start
-----------

    $ git clone git@github.com:adamelso/parkstreet.git 
    $ cd parkstreet
    $ make install
    $ make test

This will install Composer packages, create the database named `park_street`
and import the feed data into the database.


Commands
--------

### `import [--live]`  ###

Runs the data importer, using a local/offline feed in `testdata.json`.
To use the remote feed in `http://tech-test.sandbox.samknows.com/php-2.0/testdata.json`, run with the `--live` switch.

Does nothing if a unit is already imported.

```
 $ bin/parkstreet import --live
 >

 1659 [------->--------------------] | 1 secs | 20 MiB | Imported Unit 1 with 1659 metrics.
 2116 [---------------->-----------] | 1 secs | 20 MiB | Imported Unit 2 with 457 metrics.
 2640 [-------->-------------------] | 1 secs | 20 MiB | Imported Unit 3 with 524 metrics.
 4275 [------------------->--------] | 2 secs | 20 MiB | Imported Unit 4 with 1635 metrics.
 5908 [>---------------------------] | 3 secs | 20 MiB | Imported Unit 5 with 1633 metrics.
 7434 [-------------->-------------] | 4 secs | 20 MiB | Imported Unit 6 with 1526 metrics.
 9106 [------>---------------------] | 6 secs | 20 MiB | Imported Unit 7 with 1672 metrics.
 10702 [------>---------------------] | 7 secs | 20 MiB | Imported Unit 8 with 1596 metrics.
 12286 [---------------------->-----] | 8 secs | 20 MiB | Imported Unit 9 with 1584 metrics.
 12286 [============================] Imported 9 items.


```

If you want to re-run the import this, just run:

    $ make reload


### `aggregate <metric> <hour> [ --unit=<id> ]`  ###

Aggregates metric data of a particular type for a given hour.


#### Examples ####

##### Aggregate Latency on Unit 4 at midnight #####

```
$ bin/parkstreet aggregate latency 0 --unit=4
>
 ------------ ------------ ------------ ------------ -------- 
  Aggregated Latency Metrics for Unit 4 at 00:00              
 ------------ ------------ ------------ ------------ -------- 
  Unit Id      Minimum      Maximum      Mean         Median  
 ------------ ------------ ------------ ------------ -------- 
  4            41352        47442        42210        41394   
 ------------ ------------ ------------ ------------ -------- 


```

##### Aggregate Download Speeds for any units recorded at 6pm #####


```

$ bin/parkstreet aggregate download 18
>
 ---------- ---------- ---------- ---------- ---------- 
  Aggregated Download Metrics at 18:00                  
 ---------- ---------- ---------- ---------- ---------- 
  Unit Id    Minimum    Maximum    Mean       Median    
 ---------- ---------- ---------- ---------- ---------- 
  1          4654760    4670720    4664695    4666650   
  2          72011      489941     226338     172556    
  3          16924200   26594500   23049175   24339000  
  7          6588280    6868650    6844542    6863410   
  8          641827     766261     739571     746603    
  9          2002630    4671830    4326884    4615580   
 ---------- ---------- ---------- ---------- ---------- 


```


API
---

### Clients ###

Interface: `ParkStreet\Client`

Clients connect to the resource and return the appropriate PSR-7 stream.

 * __`ParkStreet\Client\OfflineClient`__


      $client = new OfflineClient($filePath);

      echo $client->connect()->getContents();

 * __`ParkStreet\Client\LiveClient`__


      $client = new LiveClient($uri);

      echo $client->connect()->getContents();


### Feeds ###

Interface: `ParkStreet\Feed`

Feeds will return the metric data in an array format from a PSR-7 stream.

 * __`ParkStreet\Feed\JsonFeed`__

  Decodes JSON from the body of the stream feed.


    $feed = new JsonFeed();
    $units = $feed->process($stream);

    echo $units[0]['metrics']['download'][0]['value'];

### Aggregations ###

Interface: `ParkStreet\Aggregation`

Calculates the minimum, maximum, median and mean from a list of data values.


 * __`ParkStreet\Aggregation\MathPhpAggregation`__

  Uses the MathPHP library to calculate values.

    $aggr = new MathPhpAggregation([10, 20, 30, 40);

    echo $aggr->mean();    // 25
    echo $aggr->maximum(); // 40

### Reports ###

Interface: `ParkStreet\Report`

Represents a report table that can be rendered later to the appropriate format.


 * __`ParkStreet\Report\MetricsReport`__

  Uses the MathPHP library to calculate values.

    $report = MetricsReport::forSingleUnit(3, 23, [...], Metric::DOWNLOAD);
    $report = MetricsReport::forMultipleUnits(23, [...], Metric::DOWNLOAD);

    return new Response($this-twig->render('report.html.twig', ['report' => $report]));

This format could be console output, or HTML, or something else.

```
<h1>{{ report.title }}</h1>
<table>
<tr>
      {%- for header in report.tableHeaders %}
          <th>{{ header }}
      {% endfor -%}
</tr>
{% for row in report.tableRows %}
    <tr>
      {%- for cell in row %}
          <td>{{ cell }}
      {% endfor -%}
    </tr>
{% endfor %}
</table>
```
