<?php

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

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
            'allowedFilters' => [
                // Пример скоупа берём из "блогерам", работает, как и предполагалось, см. метод в модели
                AllowedFilter::scope('intervalIDs'),
                // Запрос: api9/api/v1/authors?filter[intervalIDs]=55,56
            ],
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
            'allowedFilters' => [
//  Применение колбэков - через жопу. В запросе: ?filter[имя_колбэка]=<чему_угодно_хоть_пустой_строке>
            // (хотя, наверное, если чему-то равняется, то это, по аналогии, передаётся вторым параметром в замыкание)
                AllowedFilter::callback('has_comments', fn(Builder $query) => $query->whereDoesntHave('comments')),
//                AllowedFilter::callback('has_comments', fn (Builder $query) => $query->where('id', 2)),
                // custom - универсальный фильтр
                AllowedFilter::custom('myfilter', new \App\Http\Filters\MyFilter),
                // 'myfilter' - это название фильтра для командной строки
                // MyFilter() - это класс-обработчик, в него третьим параметром $property приходит это же название
                // запрос: api9/api/v1/books?filter[myfilter]=2020
                // 2020 - это параметр $value, принимаемый в MyFilter()
                // можно передавать несколько параметров через запятую, тогда в MyFilter()
                // обращаться к $value, как к массиву: $value[0], $value[1], ...
            ],
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
// exact() и partial() работают отлично в обоих вариантах применения, без проблем
//                Spatie\QueryBuilder\AllowedFilter::exact('role'),
                Spatie\QueryBuilder\AllowedFilter::partial('role'),
                // Вызов в обоих случаях: api9/api/v1/users?filter[role]=admin
//                'role',
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
