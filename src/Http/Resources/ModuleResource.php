<?php

namespace MarghoobSuleman\HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isLocal = env('APP_ENV') === 'local';


        return [
            'id'=>$this->id,
            'siteId'=>$this->site_id,
            'name'=>$this->name,
            'alias'=>$this->alias,
            'placeholder'=>"%{cms.module.{$this->alias}}%",
            'linkedModule'=>$this->linked_module,
            'viewName'=>$this->view_name,
            'dataType'=>$this->data_type,
            'queryStatement'=>$this->when($isLocal, $this->query_statement),
            'queryAs'=>$this->when($isLocal, $this->query_as),
            'dataHandler'=>$this->when($isLocal, $this->data_handler),
            'dataKeyMap'=>$this->when($isLocal, $this->data_key_map),
            'description'=>$this->description,
            'isMandatory'=>$this->is_mandatory,
            'methodType'=>$this->method_type,
            'serviceParams'=>$this->service_params,
            'headers'=>$this->headers,
            'individualCache'=>$this->individual_cache,
            'cacheGroup'=>$this->cache_group,
            'isSeoModule'=>$this->is_seo_module,
            'liveEdit'=>$this->live_edit,
            'shared'=>$this->shared,
            'data'=>$this->data,
            'queryData'=>$this->when(isset($this->queryData), $this->queryData),
            'moduleProps'=>$this->moduleProps
        ];
    }
}
