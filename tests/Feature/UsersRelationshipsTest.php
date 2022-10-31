<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UsersRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_a_relationship_to_comments_adhering_to_json_api_spec()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->getJson("/api/v1/users/{$user->id}?include=comments", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => $user->id,
                    "type" => "users",
                    "attributes" => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at->toJSON(),
                        'updated_at' => $user->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'comments' => [
                            'links' => [
                                'self' => route(
                                    'users.relationships.comments',
                                    ['user' => $user->id]
                                ),
                                'related' => route(
                                    'users.comments',
                                    ['user' => $user->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $comments->get(0)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => $comments->get(1)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => $comments->get(2)->id,
                                    'type' => 'comments'
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        'id' => '1',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(0)->message,
                            'created_at' => $comments->get(0)->created_at->toJson(),
                            'updated_at' => $comments->get(0)->updated_at->toJson(),
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(1)->message,
                            'created_at' => $comments->get(1)->created_at->toJson(),
                            'updated_at' => $comments->get(1)->updated_at->toJson(),
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(2)->message,
                            'created_at' => $comments->get(2)->created_at->toJson(),
                            'updated_at' => $comments->get(2)->updated_at->toJson(),
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_comments_returns_all_related_comments_as_resource_id_objects()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->getJson("/api/v1/users/{$user->id}/relationships/comments", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'comments',
                    ],
                    [
                        'id' => '2',
                        'type' => 'comments',
                    ],
                    [
                        'id' => '3',
                        'type' => 'comments',
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_comments_and_add_new_relationships()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(10)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'comments',
                ],
                [
                    'id' => '6',
                    'type' => 'comments',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        $this->assertDatabaseHas('comments', [
            'id' => 5,
            'user_id' => $user->id,
        ])->assertDatabaseHas('comments', [
            'id' => 6,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_comments_and_remove_relationships()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(5)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'comments',
                ],
                [
                    'id' => '2',
                    'type' => 'comments',
                ],
                [
                    'id' => '5',
                    'type' => 'comments',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'user_id' => $user->id,
        ])->assertDatabaseHas('comments', [
            'id' => 2,
            'user_id' => $user->id,
        ])->assertDatabaseHas('comments', [
            'id' => 5,
            'user_id' => $user->id,
        ])->assertDatabaseMissing('comments', [
            'id' => 3,
            'user_id' => $user->id,
        ])->assertDatabaseMissing('comments', [
            'id' => 4,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_remove_all_relationships_to_comments_with_an_empty_collection()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => []
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'user_id' => null,
        ])->assertDatabaseHas('comments', [
            'id' => 2,
            'user_id' => null,
        ])->assertDatabaseHas('comments', [
            'id' => 3,
            'user_id' => null,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'id' => '3',
                    'type' => 'comments',
                ],
                [
                    'id' => '4',
                    'type' => 'comments',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(404)->assertJson([
            'errors' => [
                [
                    'title' => 'Not Found Http Exception',
                    'details' => 'Resource not found',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_id_member_is_given_when_updating_a_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'type' => 'comments',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.0.id field is required.',
                    'details' => 'Поле data.0.id обязательно для заполнения.',
                    'source' => [
                        'pointer' => '/data/0/id',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_id_member_is_a_string_when_updating_a_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'id' => 1,
                    'type' => 'comments',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.0.id must be a string.',
                    'details' => 'Значение поля data.0.id должно быть строкой.',
                    'source' => [
                        'pointer' => '/data/0/id',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_is_given_when_updating_a_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                [
                    'id' => '1',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.0.type field is required.',
                    'details' => 'Поле data.0.type обязательно для заполнения.',
                    'source' => [
                        'pointer' => '/data/0/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_has_a_value_of_authors_when_updating_a_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->patchJson("/api/v1/users/{$user->id}/relationships/comments", [
            'data' => [
                ['id' => '1',
                    'type' => 'random',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The selected data.0.type is invalid.',
                    'details' => 'Выбранное значение для data.0.type некорректно.',
                    'source' => [
                        'pointer' => '/data/0/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_get_all_related_comments_as_resource_objects_from_related_link()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->getJson("/api/v1/users/{$user->id}/comments", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        "id" => '1',
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[0]->message,
                            'created_at' => $comments[0]->created_at->toJSON(),
                            'updated_at' => $comments[0]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '2',
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[1]->message,
                            'created_at' => $comments[1]->created_at->toJSON(),
                            'updated_at' => $comments[1]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '3',
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[2]->message,
                            'created_at' => $comments[2]->created_at->toJSON(),
                            'updated_at' => $comments[2]->updated_at->toJSON(),
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_does_not_include_related_resource_objects_when_an_include_query_param_is_not_given()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);
        $this->getJson("/api/v1/users/{$user->id}?include=comments", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJsonMissing([
                'included' => [],
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_includes_related_resource_objects_for_a_collection_when_an_include_query_parameter()
    {
        $users = User::factory(3)->create()->sortBy(function (
            $item) {
            return $item->id;
        })->values();
        $comments = Comment::factory(3)->make();
        $users->first()->comments()->saveMany($comments);
        $this->actingAs($users->first(), 'sanctum');
        $this->getJson("/api/v1/users?include=comments", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => $users[0]->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $users[0]->name,
                            'email' => $users[0]->email,
                            'role' => 'admin',
                            'created_at' => $users[0]->created_at->toJSON(),
                            'updated_at' => $users[0]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'comments' => [
                                'links' => [
                                    'self' => route(
                                        'users.relationships.comments',
                                        ['user' => $users->first()->id]
                                    ),
                                    'related' => route(
                                        'users.comments',
                                        ['user' => $users->first()->id]
                                    ),
                                ],
                                'data' => [
                                    [
                                        'id' => $comments->get(0)->id,
                                        'type' => 'comments'
                                    ],
                                    [
                                        'id' => $comments->get(1)->id,
                                        'type' => 'comments'
                                    ],
                                    [
                                        'id' => $comments->get(2)->id,
                                        'type' => 'comments'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => $users[1]->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $users[1]->name,
                            'email' => $users[1]->email,
                            'role' => 'admin',
                            'created_at' => $users[1]->created_at->toJSON(),
                            'updated_at' => $users[1]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $users[2]->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $users[2]->name,
                            'email' => $users[2]->email,
                            'role' => 'admin',
                            'created_at' => $users[2]->created_at->toJSON(),
                            'updated_at' => $users[2]->updated_at->toJSON(),
                        ]
                    ],
                ],
                'included' => [
                    [
                        'id' => '1',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(0)->message,
                            'created_at' => $comments->get(0)->created_at->toJson(),
                            'updated_at' => $comments->get(0)->updated_at->toJson(),
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(1)->message,
                            'created_at' => $comments->get(1)->created_at->toJson(),
                            'updated_at' => $comments->get(1)->updated_at->toJson(),
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'comments',
                        'attributes' => [
                            'message' => $comments->get(2)->message,
                            'created_at' => $comments->get(2)->created_at->toJson(),
                            'updated_at' => $comments->get(2)->updated_at->toJson(),
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_does_not_include_related_resource_objects_for_a_collection_when_an_include_parametr_is_not_given()
    {
        $users = User::factory(3)->create()->sortBy(function (
            $item) {
            return $item->id;
        })->values();
        $this->actingAs($users->first(), 'sanctum');
        $this->getJson("/api/v1/users", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJsonMissing([
                'included' => [],
            ]);
    }
}
