### Introduction

Simple solution to get description of countries from Wikipedia and top 10 youtube videos for them (only description and thumbnail url)

### Setup

To run the solution, just use the php self-hosted local server

```bash
cd top_videos_summary
composer install
php -S localhost:8080 -t public/
```

The url to get the json response is:

> http://localhost:8080/videos/top

To paginate the results:

> http://localhost:8080/videos/top?countriesPerPage=2&page=6

To use requests throttling, you need to have memcached installed locally and uncomment the middleware part in `routes/web.php`.


_Note:_ If you are not allowed to use the Google api, the youtube requests will fail

