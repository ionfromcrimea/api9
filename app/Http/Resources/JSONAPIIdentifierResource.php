<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JSONAPIIdentifierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => (string)$this->id,
            'type' => $this->type(),
        ];
    }
}
