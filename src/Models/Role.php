<?php

namespace MarghoobSuleman\HashtagCms\Models;

use MarghoobSuleman\HashtagCms\Core\Traits\Admin\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends AdminBaseModel
{
    use Common;

    protected $guarded = array();


    /**
     * Get Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Give Permission
     * @param Permission $permission
     * @return Model
     */
    public function givePermissionTo(Permission $permission) {

        return $this->permissions()->save($permission);

    }

    /**
     * Assign All permissions
     *
     * @param array $allPermissions
     * @return array
     */

    public function giveAllPermissionTo($allPermissions = array()) {
        $saved = array();
        foreach ($allPermissions as $permission) {
            $saved[] = $this->givePermissionTo($permission);
        }
        return $saved;
    }

    /**
     * Delete old permission
     * @param $permission
     * @return mixed
     */
    public function detachAllPermissions() {
        return DB::table('permission_role')->where('role_id', $this->id)->delete();
    }


    /**
     * @override
     * Find By Id
     *
     * @param int $id
     * @param string $with
     * @return array
     */
    public static function getById($id=0, $with='') {

        $data = parent::getById($id, 'permissions');
        $data["permissions"] = self::pivotToArray($data["permissions"], "permission_id");;

        return $data;
    }



}
