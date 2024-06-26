<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Support\Facades\DB;

class CmsModule extends AdminBaseModel
{
    protected $guarded = [];

    /**
     * Get admin modules
     *
     * @param  null  $user_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAdminModules($user_id = null)
    {
        return static::with(['child'])->orderBy('position', 'asc')->get();
    }

    /**
     * Get with child
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child()
    {
        return $this->hasMany(CmsModule::class, 'parent_id')->orderBy('position', 'asc');
    }

    /**
     * Get info by name
     *
     * @param  string  $name
     * @return mixed
     */
    public static function getInfoByName($name = '')
    {

        return static::with('child')->where('controller_name', '=', $name)->get()->first();

    }

    /**
     * Get children
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->child()->with('children');
    }

    /**
     * Get Parents only
     *
     * @return mixed
     */
    public static function parentOnly()
    {
        return self::where('parent_id', '=', 0)->get();
    }

    /************ Create Module **********************/

    /**
     * Get all tables
     */
    public function getAllTables(): array
    {

        $tables = DB::select('SHOW TABLES');

        $arr = [];
        $index = 0;
        foreach ($tables as $key => $table) {
            foreach ($table as $key => $value) {
                $arr[] = ['id' => $index++, 'name' => $value];
            }
        }

        return $arr;
    }

    /**
     * Get Fields name
     *
     * @return mixed
     */
    public function getFieldsName($table)
    {
        $db_columns = DB::select('SHOW COLUMNS FROM '.$table);

        return array_map(function ($column) {
            return $column->Field;
        }, $db_columns);
    }
}
