# Changelog
## Version 1.3.0
- Optimization
- Major Code Refactoring for frontend
- Get site info
  - Get lang info
  - Get tenant info
  - Get category info
- load data

- will work on following
- Admin branding can be changed. Logo height and icon can be pass through module.


## Version 1.2.9
- Fixed content auto update url issue

## Version 1.2.8
- ModifyHooks Migration table issue fixed

## Version 1.2.7
- ModifyCategory Migration table issue fixed

## Version 1.2.6
- moduleInfo is now passed with each view template.
- hooks table has now 'direction' column. It can be useful for mobile layout handling.  

## Version 1.2.5
- Blog read_count is now displayed
- You can define a controller name for a category
- Code refactoring. InfoKeeper is now accessible from app()->HashtagCms->getInfoKeeper();


## Version 1.2.4
- User Model for controller fill-able is now `protected $guarded = array()`;
- Backend: Code refactoring in editor.js


## Version 1.2.3
- Added Rich text editor (tinyMCE)

## Version 1.2.2
- Backend: Added graph for Top categories and Top content in dashboard
- Change TEXT to LONGTEXT of page_content table 
  - (ALTER TABLE `page_langs` MODIFY `page_content` LONGTEXT)
- Change TEXT to LONGTEXT of category_langs table 
  - (ALTER TABLE `category_langs` MODIFY `content` LONGTEXT)
- Showing read count in category and page listing module.  


## Version 1.2.1
- Backend: Add "shared" field in module add/edit
- Blog::getLatestBlog is now generic. Blog can read multiple categories now. look into config/hashtagcms.php 
- page addedit.blade limit fix

## Version 1.2.0
- Added content_source field in page table

## Version 1.1.9
- Added image upload feature in Content/Blog module
- Code refactor in Content/Blog modules
- Added message if unable to connect mysql

## Version 1.1.8
- Fixed Site settings issue

## Version 1.1.7
- Remove VueJs dependency from frontend 
- Update security bugs

## Version 1.1.6
- Fixed Analytic log error
- Font load error fixed 

## Version 1.1.5
- Added Analytical support - read count for each story

## Version 1.0

### Added
- Headless CMS, Use it as Headless CMS or Bundled, API enabled, 
  Admin Panel, Multisite, Multitenant, Multilingual, and oAuth 2.0 enabled.
  The Most powerful, fast, user-friendly and secured platform.
