# Changelog
## Version 1.3.0
- Optimization
- Frontend: Major Code Refactoring
- Frontend: Api Code refactor
- Frontend: ModuleParser Hook. If you are adding a new module type. You need to add parser for that. Create a class Parser\ModuleParser in app folder and need to create a method to parse module. Say module type is "MenuService"; you need to create a method called "getMenuServiceModule(mixed $module):?array"
- Backend: Frontend Module type can be pass through config
- Backend: Menu Sorter changes, change id and name field


## Version 1.2.9
- Backend: Fixed content auto update url issue

## Version 1.2.8
- Backend: ModifyHooks Migration table issue fixed

## Version 1.2.7
- Backend: ModifyCategory Migration table issue fixed

## Version 1.2.6
- Frontend: moduleInfo is now passed with each view template.
- Frontend: hooks table has now 'direction' column. It can be useful for mobile layout handling.  

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
