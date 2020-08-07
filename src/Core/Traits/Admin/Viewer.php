<?php
namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use MarghoobSuleman\HashtagCms\Models\CmsModule;
use MarghoobSuleman\HashtagCms\Models\CmsPermission;


trait Viewer {

    /**
     * @param $data
     * @param $viewName
     * @param bool $checkPolicy
     * @return mixed
     */
    public function viewNow($viewName, $data, $checkPolicy=TRUE) {

        if($checkPolicy==TRUE) {

            if(!$this->checkPolicy('read')) {

                return htcms_admin_view("common.error");

            }
        }

        return htcms_admin_view($viewName, $data);


    }


    /**
     * @param null $module_name
     * @return array
     */
    public function getModuleInfo($module_name=NULL) {

        //info("module_name: ".$module_name. " request module id ".request()->module_info->id);
        $id = ($module_name==NULL) ? request()->module_info->id : CmsModule::getInfoByName($module_name)->id;

        $isSuperAdmin = Auth::user()->isSuperAdmin();

        $permission = CmsPermission::has($id, Auth::user()->id);

        //info("permission ".json_encode($permission));
        //info("compact: ".json_encode(compact('isSuperAdmin', 'permission')));
        return compact('isSuperAdmin', 'permission');
    }

    /**
     * @param $rights - 'read'|'write' etc
     * @return bool
     */
    protected function checkPolicy($rights='', $module=NULL) {

        //return false;

        $moduleInfo = $this->getModuleInfo($module);

        info("moduleInfo: ". json_encode($moduleInfo));

        if(!$moduleInfo['isSuperAdmin']) {

            //handle special case. User has rights but readonly for a module
            switch($rights) {
                case "edit":
                    //User can edit but we want to give readonly on a module.
                    if($this->isReadOnly($moduleInfo['permission'])==TRUE) {
                        return FALSE;
                    }
                    break;

            }

            //info("rights: $rights ".$moduleInfo['permission']." id: ".Auth::user()->id);
            //$this->authorize($rights);
            if(Gate::denies($rights, $moduleInfo['permission']) || $moduleInfo['permission'] == FALSE) {
                return FALSE;

            }

        }

        return TRUE;

    }

    /**
     * check if it has readonly access for a module
     * @param $moduleInfo
     * @return bool
     */
    private function isReadOnly($moduleInfo) {
        return ($moduleInfo == FALSE) ? TRUE : (($moduleInfo->readonly==1) ? TRUE : FALSE);
    }


}
