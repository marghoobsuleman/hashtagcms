# HashtagCMS

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]


Headless CMS, Use it as Headless CMS or Bundled, API enabled, 
Admin Panel, multisite/multi-tenant, multiplatform, multilingual with endless possibilities.
The Most powerful, fast, user-friendly and secured platform. Made with PHP Laravel Framework.

## Installation

Via Composer

``` bash
composer create-project laravel/laravel mysite
cd mysite
composer require marghoobsuleman/hashtagcms
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
- Open `app/Models/User.php` and remove or comment below lines.

``` bash
// use Illuminate\Foundation\Auth\User as Authenticatable
```
- Add below lines in the same file (`app/Models/User.php`)
``` bash
use MarghoobSuleman\HashtagCms\User as Authenticatable;
```
- You might want to comment/remove below route in `routes/web.php`

```bash 
/*Route::get('/', function () {
    return view('welcome');
});*/
```

###Install Command
``` bash
php artisan cms:install
```

### Configure site on browser. Open it with the appUrl prefix
```bash 
http://{APP_URL}/install
ie:
http://www.hashtagcms.org/install
```

- You are done :)

## Test
```bash 
php artisan test vendor/marghoobsuleman/hashtagcms
```

## Change log
#v1.4.2 Changes
- Backend/Frontend/API: Added festival support. You can add lottie and schedule it.
- API: Added ServiceLater Support. Service url will be passed to view too.
- Core: Added module data modifier
- See how it works:
- Create class/method `App\Parser\ModuleDataModifier` -> `moduleAliasNameMethod($data, $moduleInfo)`
  and manipulate your data and return it. Works for API and views.
- Do not forget to run `php artisan migrate` if you are upgrading.

#v1.4.1 Changes
- Backend: Export the correct folder (neo) while installing the package
- General: Code cleanup
- General: Added webpack.mix.js and package.json while exporting assets in resource/support

### More logs [changelog](changelog.md) for more information on what has changed recently.


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
