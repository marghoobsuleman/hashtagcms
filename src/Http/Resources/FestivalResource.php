<?php

namespace MarghoobSuleman\HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FestivalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            "id"=>$this->id,
            "image"=>$this->image,
            "bodyCss"=>$this->body_css,
            "lottie"=>$this->lottie,
            "headerCss"=>$this->header_css,
            "footerCss"=>$this->footer_css,
            "startDate"=>$this->start_date,
            "endDate"=>$this->end_date,
            "publishStatus"=>$this->publish_status
        ];
    }
}
