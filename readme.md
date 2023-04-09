# HashtagCMS

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]


Headless CMS, Use it as Headless CMS or Bundled, API enabled, 
Admin Panel, multisite/multi-tenant, multiplatform, multilingual with endless possibilities.
The Most powerful, fast, user-friendly and secured platform. Made with PHP Laravel Framework.

## Installation

Via Composer

``` bash
laravel new mysite
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
###Install Command
``` bash
php artisan cms:install
```
- Open `app/Models/User.php` and remove or comment below lines.

``` bash
//use Illuminate\Foundation\Auth\User as Authenticatable
```

- Add below lines in the same file (`app/Models/User.php`)
``` bash
use MarghoobSuleman\HashtagCms\User as Authenticatable;
```
- You might want to comment/remove below route in `routes/web.php`

```bash 
Route::get('/', function () {
    return view('welcome');
});
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

#v1.3.9 Changes
- Backend: assets files are in lowercase
- Backend: Showing roles in user listing
- Backend: Fixed some bugs and cleanup 


#v1.3.8 Changes
#### Major Update: Please note: This updates needs fresh installation.
- General: Compatible with laravel/framework": "^10.0"
- Backend: Moved from Vue 2 to Vue 3
- Backend: Added gallery and festival modules. You can now upload files/images etc.
- Backend: Added gallery module support with content editor
- Backend: Moved to bootstrap 5.0 for backend
- Backend: Improved Smart Copy/Paste
- Backend: `direction` column in `hooks` table has default Null value now
- Backend: linked module and live edit bug while adding or editing frontend module
- Backend: Sitewise permission added
- Backend: Dashboard sub-modules data fetching issue on sitewise
- API: Refactored API. Used Resource to make key camelCase.
- API: modules props are returns as key/value pairs
- General: Move Passport to Sanctum for authentication
- Frontend: Frontend can partially work with API. Useful for microservices architecture.
- Frontend: Fixed some bugs
- Frontend: Moved to bootstrap 5.0

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
