<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits;

use Illuminate\Support\Facades\DB;

use MarghoobSuleman\HashtagCms\Models\Site;

/**
 * @todo
 * For future use
 * need to work in upcoming version
 * need to create site_user table
 * Desc: To give permission to user's to manages site wise
 */
trait SiteManager {


    public function sites() {

        return $this->belongsToMany(Site::class);

    }

    public function hasSite($site) {

        if(is_string($site)) {

            return $this->sites->contains('context', $site);

        }

        return !! $site->intersect($this->sites)->count();

    }

    /**
     * Save Role
     * @param $role
     */
    public function assignSite($site) {

        $this->sites()->save($site);

    }

    /**
     * Save multiple roles
     * @param $roles
     *
     */
    public function assignMultipleSite($sites) {

        foreach ($sites as $site) {
            $this->sites()->save($site);
        }

    }

    /**
     * Delete all roles
     * @return mixed
     */

    public function detachAllSites() {
        return DB::table('site_user')->where('user_id', $this->id)->delete();
    }


    public function supportedSites() {
        return DB::table('site_user')->where('user_id', $this->id)->get();
    }


}
