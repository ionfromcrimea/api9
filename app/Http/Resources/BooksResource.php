<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BooksResource extends JsonResource
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
            'type' => 'books',
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'publication_year' => $this->publication_year,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => [
                'authors' => [
                    'links' => [
                        'self' => route(
                            'books.relationships.authors',
                            ['book' => $this->id]
                        ),
                        'related' => route(
                            'books.authors',
                            ['book' => $this->id]
                        ),
                    ],
//                    'ss' => $this->authors->get(0)->id,
//                    'ss' => $this->id,
//                    'data' => $this->authors->map(function ($author) {
//                        return [
//                            'id' => $author->id,
//                            'type' => 'authors',
//                        ];
//                    })
                    'data' => AuthorsIdentifierResource::collection(
                        $this->whenLoaded('authors')),
                ],
            ]
        ];
    }

    private function relations()
    {
        return [
            AuthorsResource::collection($this->whenLoaded('authors')),
        ];
    }

    public function included($request)
    {
        return collect($this->relations())
            ->filter(function ($resource) {
                return $resource->collection !== null;
            })->flatMap->toArray($request);
    }

    public function with($request)
    {
//        return [
//            'included' => $this->relations(),
//        ];
        $with = [];
        if ($this->included($request)->isNotEmpty()) {
            $with['included'] = $this->included($request);
        }
        return $with;
    }
}
