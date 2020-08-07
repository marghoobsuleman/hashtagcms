<?php
namespace MarghoobSuleman\HashtagCms\Core\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class SiteScope implements Scope {


    /**
     * @param Builder $builder
     * @param Model $model\
     */
    public function apply(Builder $builder, Model $model) {

        $site_id = htcms_get_siteId_for_admin();
        $builder->where("site_id", "=", $site_id);

    }
}
