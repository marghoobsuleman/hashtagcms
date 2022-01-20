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
  > php artisan make:seed CmsModuleTableSeeder
    open CmsModuleTableSeeder and paste below content in run()
     
        $table_name = 'cms_modules';
        $date = date('Y-m-d H:i:s');
        $cms_modules = array(
            array('name' => 'Module Properties','controller_name' => 'moduleproperty','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage Module Propeties','icon_css' => 'fa fa-cog','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '30','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        $res = DB::selectOne("select * from $table_name where controller_name='moduleproperty'");
        if (!$res) {
            DB::table($table_name)->insert($cms_modules);
        }
    
  > php artisan db:seed CmsModuleTableSeeder
```  
- Frontend: Separate assets path support for different domain. you can configure that in config/hashtagcms.php
- Backend: Added Module Props and Module Props langs with view and controller
- Backend: Added support for module props copy in site config and in site clone too.
- Backend: Fixed in create frontend module. 
- Backend: added 'headers' column in module table. Will use this field to send header with each module. (implement in next version)
- API: Module props added in api results
- API: Site props key value is similar to module props

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
