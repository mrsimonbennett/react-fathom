
composer install

Its a bit to fast for php garage collection so needs a fair bit of memory. I used 2GB.
```php -d memory_limit=-1 public/server.php```


test it
```
wrk -t12 -c400 -d30  http://127.0.0.1:8000/
```


Check the Proxy Brower if you want to test posting the data


Check the BulkLogger if you want to change the way requests are processed
