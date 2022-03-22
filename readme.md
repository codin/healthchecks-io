# Healthchecks-io Client

![version](https://img.shields.io/github/v/tag/codin/healthchecks-io)
![workflow](https://img.shields.io/github/workflow/status/codin/healthchecks-io/Composer)
![license](https://img.shields.io/github/license/codin/healthchecks-io)

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
