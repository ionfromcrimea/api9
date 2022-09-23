<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;

class BooksCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'data' => $this->collection,
            'included' => $this->mergeIncludedRelations($request),
        ];
    }

    private function mergeIncludedRelations($request)
    {
//        return $includes;

//        $includes = $this->collection->flatMap(function ($resource) use ($request) {
//            return $resource->included($request);
//        })->unique()->values();
        $includes = $this->collection->flatMap->included($request)->unique()->values();

        return $includes->isNotEmpty() ? $includes : new MissingValue();
    }
}
