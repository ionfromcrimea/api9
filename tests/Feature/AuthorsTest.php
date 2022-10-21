<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthorsTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_an_author_as_a_resource_object()
    {
        $author = Author::factory()->create();
//        dd($author);
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
//        Passport::actingAs($user);

//        $this->getJson('/api/v1/authors/1')
        $this->getJson('/api/v1/authors/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $author->name,
                        'created_at' => $author->created_at->toJSON(),
                        'updated_at' => $author->updated_at->toJSON(),
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_all_authors_as_a_collection_of_resource_objects()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = Author::factory()
            ->count(3)
            ->create();
        $this->get('/api/v1/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => '1',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[0]->name,
                            'created_at' => $authors[0]->created_at->toJSON
                            (),
                            'updated_at' => $authors[0]->updated_at->toJSON
                            (),
                        ]
                    ],
                    [
                        "id" => '2',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[1]->name,
                            'created_at' => $authors[1]->created_at->toJSON
                            (),
                            'updated_at' => $authors[1]->updated_at->toJSON
                            (),
                        ]
                    ],
                    [
                        "id" => '3',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[2]->name,
                            'created_at' => $authors[2]->created_at->toJSON
                            (),
                            'updated_at' => $authors[2]->updated_at->toJSON
                            (),
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_create_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => 'John Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'John Doe',
//                        'created_at' => now()->setMilliseconds(0)->toJSON(),
//                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ])
            ->assertHeader('Location', url('/api/v1/authors/1'));
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_is_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => '',
                'attributes' => [
                    'name' => 'John Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.type field is required.',
                        'details' => 'Поле data.type обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_has_the_value_of_authors_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'author',
                'attributes' => [
                    'name' => 'John Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The selected data.type is invalid.',
                        'details' => 'Выбранное значение для data.type некорректно.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes field is required.',
                        'details' => 'Поле data.attributes обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => 'not an object',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes must be an array.',
                        'details' => 'Значение поля data.attributes должно быть массивом.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_a_name_attribute_is_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => '',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes.name field is required.',
                        'details' => 'Поле data.attributes.name обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_a_name_attribute_is_a_string_when_creating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => 47,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes.name must be a string.',
                        'details' => 'Значение поля data.attributes.name должно быть строкой.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_update_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();

        $creationTimestamp = now();
        sleep(1);

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'authors',
                    'attributes' => [
                        'name' => 'Jane Doe',
//                        'created_at' => $creationTimestamp->setMilliseconds(0)->toJSON(),
//                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ],
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => 'Jane Doe',
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_an_id_member_is_given_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.id field is required.',
                        'details' => 'Поле data.id обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_an_id_member_is_a_string_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => 1,
                'type' => 'authors',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.id must be a string.',
                        'details' => 'Значение поля data.id должно быть строкой.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_is_given_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => '',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.type field is required.',
                        'details' => 'Поле data.type обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_has_the_value_of_authors_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'author',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The selected data.type is invalid.',
                        'details' => 'Выбранное значение для data.type некорректно.',
                        'source' => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes field is required.',
                        'details' => 'Поле data.attributes обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => 'not an object',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes must be an array.',
                        'details' => 'Значение поля data.attributes должно быть массивом.',
                        'source' => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_a_name_attribute_is_a_string_when_updating_an_author()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'name' => 47,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.attributes.name must be a string.',
                        'details' => 'Значение поля data.attributes.name должно быть строкой.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_delete_an_author_through_a_delete_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $author = Author::factory()->create();
        $this->delete('/api/v1/authors/1', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])
            ->assertStatus(204);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => $author->name,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_authors_by_name_through_a_sort_query_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = collect([
            'Bertram',
            'Claus',
            'Anna',
        ])->map(function ($name) {
            return Author::factory()->create([
                'name' => $name
            ]);
        });
        $this->get('/api/v1/authors?sort=name', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Anna',
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Bertram',
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Claus',
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_authors_by_name_in_descending_order_through_a_sort_query_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = collect([
            'Bertram',
            'Claus',
            'Anna',
        ])->map(function ($name) {
            return Author::factory()->create([
                'name' => $name
            ]);
        });
        $this->get('/api/v1/authors?sort=-name', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Claus',
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Bertram',
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Anna',
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_authors_by_multiple_attributes_through_a_sort_query_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = collect([
            'Bertram',
            'Claus',
            'Anna',
        ])->map(function ($name) {
            if ($name === 'Bertram') {
                return Author::factory()->create([
                    'name' => $name,
                    'created_at' => now()->addSeconds(3),
                ]);
            }
            return Author::factory()->create([
                'name' => $name,
            ]);
        });
        $this->get('/api/v1/authors?sort=created_at,name', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Anna',
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Claus',
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Bertram',
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_authors_by_multiple_attributes_in_descending_order_through_a_sort_query_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = collect([
            'Bertram',
            'Claus',
            'Anna',
        ])->map(function ($name) {
            if ($name === 'Bertram') {
                return Author::factory()->create([
                    'name' => $name,
                    'created_at' => now()->addSeconds(3),
                ]);
            }
            return Author::factory()->create([
                'name' => $name,
            ]);
        });
        $this->get('/api/v1/authors?sort=-created_at,name', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Bertram',
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Anna',
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'Claus',
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_paginate_authors_through_a_page_query_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $authors = Author::factory()->count(10)->create();
        $this->get('/api/v1/authors?page[size]=5&page[number]=1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '4',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[3]->name,
                        'created_at' => $authors[3]->created_at->toJSON(),
                        'updated_at' => $authors[3]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '5',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[4]->name,
                        'created_at' => $authors[4]->created_at->toJSON(),
                        'updated_at' => $authors[4]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('authors.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('authors.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('authors.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
    }
}
