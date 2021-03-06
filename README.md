[![Code Climate](https://codeclimate.com/github/PHPSocialNetwork/phpfastcache-bundle/badges/gpa.svg)](https://codeclimate.com/github/PHPSocialNetwork/phpfastcache-bundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PHPSocialNetwork/phpfastcache-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PHPSocialNetwork/phpfastcache-bundle/?branch=master) [![Build Status](https://travis-ci.org/PHPSocialNetwork/phpfastcache-bundle.svg?branch=master)](https://travis-ci.org/PHPSocialNetwork/phpfastcache-bundle) [![Latest Stable Version](http://img.shields.io/packagist/v/phpfastcache/phpfastcache-bundle.svg)](https://packagist.org/packages/phpfastcache/phpfastcache-bundle) [![Total Downloads](http://img.shields.io/packagist/dt/phpfastcache/phpfastcache-bundle.svg)](https://packagist.org/packages/phpfastcache/phpfastcache-bundle) [![License](https://img.shields.io/packagist/l/phpfastcache/phpfastcache-bundle.svg)](https://packagist.org/packages/phpfastcache/phpfastcache-bundle)
# Symfony Flex PhpFastCache Bundle

#### :warning: Please note that the V3 is a major (BC breaking) update of the PhpFastCache Bundle !
> As of the V3 the bundle is **absolutely** not compatible with previous versions.\
To ensure you the smoothest migration possible, please check the migration guide in the Resources/Docs directory.\
One of the biggest change is the Phpfastcache's dependency which is not set to the v7 which it not backward compatible at all.

#### :thumbsup: Step 1: Include phpFastCache Bundle in your project with composer:

```bash
composer require phpfastcache/phpfastcache-bundle
```

#### :construction: Step 2: Setup your `config/packages/phpfastcache.yaml` to configure your cache(s) instance(s)

```yml
# PhpFastCache configuration
phpfastcache:
    twig_driver: "filecache" # This option must be a valid declared driver, in our example: "filecache"
    twig_block_debug: false # This option will wrap CACHE/ENDCACHE blocks with block debug as HTML comment
    drivers:
        filecache:
            type: Files
            parameters:
                path: "%kernel.cache_dir%/phpfastcache/"
```
* This step can be skipped using [Symfony recipes](https://symfony.com/doc/current/setup/flex.html).

#### :rocket: Step 3: Accelerate your app by making use of PhpFastCache service

Caching data in your controller:
```php
public function indexAction(Request $request, Phpfastcache $phpfastcache)
{
    $cache = $phpfastcache->get('filecache');
    $item = $cache->getItem('myAppData');
    
    if (!$item->isHit() || $item->get() === null) {
        $item->set('Wy app has now superpowers !!')->expiresAfter(3600);//1 hour
        $cache->save($item);
    } 
     
    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', [
        'myAppData' => $item->get(),
        'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
    ]);
}
```
Or in your template:
```twig
<div>
    {#
     * 'myrandom6' Is your cache key identifier, must be unique
     * 300 Is the time to live (TTL) before the cache expires and get regenerated
    #}
    {% cache 'myrandom6' 300 %}
        <textarea>
            <!-- Some heavy stuff like Doctrine Lazy Entities -->
            {% for i in 1..1000 %}{{ random() }}{% endfor %}
        </textarea>
    {% endcache %}
</div>
```

#### :computer: CLI command interactions
As of the V3, some command-line tools were introduced, mostly for CRUD-like operations.

##### GET operation
```bash
php bin/console phpfastcache:get filecache cacheKey
```
This will display the content of a cache item if it eventually exists.


##### SET operation
```bash
php bin/console phpfastcache:get filecache cacheKey '{"a": 14}' 300 -a 1
```
This will set the content of a cache item.\
The TTL (300), in seconds, is optional and take the default value filled in your configuration file.\
The `auto-type-cast` option  _"-a"_ (enabled by default) will let allows you to automatically type cast your variable:
- `false` and `true` will be respectively converted to _boolean_.
- `1337` and `1337.666` will be respectively be converted to _integer_ or _float_.
- `null` will be converted to _null_.
- `{"a": 14}` will be converted to an associative _array_ using a JSON detection
- `a regular string` will remains unchanged and stays a string

You can obviously disable this behavior by turning off the `auto-type-cast` option: `-a 0`

##### DELETE operation
```bash
php bin/console phpfastcache:del filecache cacheKey
```
This will delete the specified cache item. 

##### CLEAR operation
```bash
php bin/console phpfastcache:clear filecache
# OR to clear every caches:
php bin/console phpfastcache:clear
```
This will clear a single cache instance if specified or all the configured cache instances otherwise.

#### :bulb: Introducing Cacheable Responses (V3 only)
As of the V3 there's a new, easier and cleaner way to setup HTTP cache to decrease your server bandwidth along with your CPU load: Cacheable Responses.
And it's pretty easy to implement:
```php
    /**
     * @Route("/cached", name="cached")
     */
    public function cachedAction(Phpfastcache $phpfastcache, Request $request): Response
    {
        return (new CacheableResponse($phpfastcache->get('filecache'), $request))->getResponse('cache_key', 3600, function () {
            return new Response('Random bytes: ' . \random_bytes(255));
        });
    }
``` 
`CacheableResponse` is provided by `\Phpfastcache\Bundle\Response\CacheableResponse`.
This class will handle responses headers (cache-control, etag, etc...) and http status (304 Not modified).

#### :boom: phpFastCache Bundle support
Found an issue or had an idea ? Come here [here](https://github.com/PHPSocialNetwork/phpfastcache-bundle/issues) and let us know !

