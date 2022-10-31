<?php
return [
    'resources' => [
        'authors' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at',
            ],
            'allowedIncludes' => [
                'books',
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string',
                ]
            ],
            'relationships' => [
                [
                    'type' => 'books',
                    'method' => 'books',
                    'id' => 'author',
                ]
            ]
        ],
        'books' => [
            'allowedSorts' => [
                'title',
                'publication_year',
                'created_at',
                'updated_at',
            ],
            'allowedIncludes' => [
                'authors',
                'comments',
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.title' => 'required|string',
                    'data.attributes.description' => 'required|string',
                    'data.attributes.publication_year' => 'required|string',
                ],
                'update' => [
                    'data.attributes.title' => 'sometimes|required|string',
                    'data.attributes.description' => 'sometimes|required|string',
                    'data.attributes.publication_year' => 'sometimes|required|string',
                ]
            ],
            'relationships' => [
                [
                    'type' => 'authors',
                    'method' => 'authors',
                    'id' => 'book',
                ],
                [
                    'type' => 'comments',
                    'method' => 'comments',
                    'id' => 'book',
                ],
            ]
        ],
        'users' => [
            'allowedSorts' => [
                'name',
                'email',
            ],
            'allowedIncludes' => [
                'comments',
            ],
            'allowedFilters' => [
                Spatie\QueryBuilder\AllowedFilter::exact('role'),
            ],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string',
                    'data.attributes.email' => 'required|email',
                    'data.attributes.password' => 'required|string',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string',
                    'data.attributes.email' => 'sometimes|required|email',
                    'data.attributes.password' => 'sometimes|required|string',
                ],
            ],
            'relationships' => [
                [
                    'type' => 'comments',
                    'method' => 'comments',
                    'id' => 'user',
                ],
            ]
        ],
        'comments' => [
            'allowedSorts' => [
                'created_at'
            ],
            'allowedIncludes' => [
                'books',
                'users',
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.message' => 'required|string',
                ],
                'update' => [
                    'data.attributes.message' => 'sometimes|required|string',
                ]
            ],
            'relationships' => [
                [
                    'type' => 'books',
                    'method' => 'books',
                    'id' => 'comment',
                ],
                [
                    'type' => 'users',
                    'method' => 'users',
                    'id' => 'comment',
                ],
            ]
        ]
    ]
];
