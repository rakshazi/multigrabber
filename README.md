# Multigrabber

> Special combination of [PicoFeed parser](https://github.com/fguillot/picoFeed) and [MCurl](https://github.com/KhristenkoYura/mcurl)
> These libraries allow Multigrabber download content from multiple urls in parallel requests and parse it with PicoFeed parser (best html parser ver).

Test results (100 urls, multiple sites): 64 sec and 0.36MB RAM for download and parse all content.

### Installation 

```
composer require rakshazi/multigrabber
```

### Usage

```php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$config = new \PicoFeed\Config\Config;
$config->setGrabberRulesFolder(__DIR__ . '/rules'); //PicoFeed grabber rules, @link https://github.com/fguillot/picoFeed/blob/master/docs/feed-parsing.markdown#custom-regex-filters
$config->setClientUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36');
$grabber = new \Rakshazi\Multigrabber($config);
$urls = ['http://example.site/1', 'https://example.site/post2', '...'];
$data = $grabber->run($urls);

var_dump($data);
```

Output:

```
array(2) {
  ["http://example.site/1"]=>
  string(978) "</p>Parsed content from nat-geo.ru (text was removed in this example) <a href="http://www.nat-geo.ru/go.php?url=http%3A%2F%2Fvk.com%2Fstudio_vd" rel="noreferrer" target="_blank">Vert Dider</a>.</p>
<p><iframe src="https://vk.com/video_ext.php?oid=-55155418&amp;id=171249157&amp;hash=7715a02bf81f02f4&amp;hd=2" width="853" height="480" frameborder="0"></iframe></p>"
  ["https://example.site/post2"]=>
  string(3675) "Parsed <strong>html</strong>"
}

```
