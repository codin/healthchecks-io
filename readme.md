# Healthchecks-io Client

Usage

```php
$pinger = new Codin\Healthchecks\Ping('0d338dcc-a054-11ec-b909-0242ac120002');
$pinger->start();
try {
    // do some stuff
    $pinger->success();
} catch (Exception $e) {
    $pinger->fail();
}
```
