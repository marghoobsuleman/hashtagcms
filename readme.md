# HashtagCMS

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]


Headless CMS, Use it as Headless CMS or Bundled, API enabled, 
Admin Panel, Multisite, Multitenant, Multilingual, and oAuth 2.0.
The Most powerful, fast, user-friendly and secured platform. Made with PHP Laravel Framework.

## Installation

Via Composer

``` bash
$ composer require marghoobsuleman/hashtagcms
```

## Hashtag CMS installation guidelines

- Open `config/app.php` and add below line in `providers` array.
``` bash
MarghoobSuleman\HashtagCms\HashtagCmsServiceProvider::class
```
- Open `.env` file and update `APP_URL`, and make sure database information is correct.

``` bash 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
###Install Command
``` bash
$ php artisan cms:install
```
- Open `app/User.php` and remove or comment below line.

``` bash
use Illuminate\Foundation\Auth\User as Authenticatable
```

- Add below line in the same file (`app/Models/User.php` or `app/User.php`)
``` bash
use MarghoobSuleman\HashtagCms\User as Authenticatable;
```
- You might want to comment/remove below route in `routes/web.php`

```bash 
Route::get('/', function () {
    return view('welcome');
});
```
- You are done :)

## Change log
- v1.3.3 Changes
- How to update to v1.3.2 to v1.3.3:
```bash 
  > composer upgrade 
  > php artisan migrate
```  
- done - Frontend: Separate asset path support for different domain. you can configure that in config/hashtagcms.php
- done - Backend: Added Module Props and Module Props langs
- done - Backend: Adde support for module props copy and in clone site too.
- done - API: Module props added in api results


- later: headers in modules table

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Marghoob Suleman][https://www.marghoobsuleman.com]


## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/marghoobsuleman/hashtagcms.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/marghoobsuleman/hashtagcms.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/marghoobsuleman/hashtagcms/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/marghoobsuleman/hashtagcms
[link-downloads]: https://packagist.org/packages/marghoobsuleman/hashtagcms
[link-travis]: https://travis-ci.org/marghoobsuleman/hashtagcms
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/marghoobsuleman
[link-contributors]: ../../contributors
