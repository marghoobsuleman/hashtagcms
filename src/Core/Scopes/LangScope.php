<?php
namespace MarghoobSuleman\HashtagCms\Core\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class LangScope implements Scope {

    /**
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model) {
        $lang_id = htcms_get_language_id_for_admin();
        $builder->where("lang_id", "=", $lang_id);
    }


}
