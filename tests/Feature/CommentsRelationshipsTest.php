<?php


namespace Tests\Feature;


use App\Models\Book;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CommentsRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_a_relationship_to_user_adhering_to_json_api_spec()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $this->getJson("/api/v1/comments/1?include=users", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comment->message,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at' => $comment->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.users',
                                    ['comment' => $comment->id]
                                ),
                                'related' => route(
                                    'comments.users',
                                    ['comment' => $comment->id]
                                ),
                            ],
                            'data' => [
                                'id' => $user->id,
                                'type' => 'users',
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_returns_a_relationship_to_book_adhering_to_json_api_spec()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->getJson("/api/v1/comments/1?include=books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comment->message,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at' => $comment->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.books',
                                    ['comment' => $comment->id]
                                ),
                                'related' => route(
                                    'comments.books',
                                    ['comment' => $comment->id]
                                ),
                            ],
                            'data' => [
                                'id' => $book->id,
                                'type' => 'books',
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_a_relationship_to_both_book_and_user_adhering_to_json_api()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->getJson("/api/v1/comments/1?include=users,books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comment->message,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at' => $comment->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.books',
                                    ['comment' => $comment->id]
                                ),
                                'related' => route(
                                    'comments.books',
                                    ['comment' => $comment->id]
                                ),
                            ],
                            'data' => [
                                'id' => $book->id,
                                'type' => 'books',
                            ]
                        ],
                        'users' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.users',
                                    ['comment' => $comment->id]
                                ),
                                'related' => route(
                                    'comments.users',
                                    ['comment' => $comment->id]
                                ),
                            ],
                            'data' => [
                                'id' => $user->id,
                                'type' => 'users',
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_user_returns_related_user_as_resource_id_object()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $this->getJson("/api/v1/comments/1/relationships/users", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'type' => 'users'
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_book_returns_related_book_as_resource_id_object()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->getJson("/api/v1/comments/1/relationships/books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'type' => 'books'
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationship_to_a_user_and_change_to_another_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $anotherUser = User::factory()->create();

        $this->patchJson('/api/v1/comments/1/relationships/users', [
            'data' => [
                'id' => $anotherUser->id,
                'type' => 'users',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'user_id' => $anotherUser->id,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationship_to_a_book_and_change_to_another_book()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $anotherBook = Book::factory()->create();

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'id' => (string)$anotherBook->id,
                'type' => 'books',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'book_id' => $anotherBook->id,
        ]);
    }

    /**
     * @test
     */
    public function it_can_modify_relationship_to_a_user_and_remove_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/users', [
            'data' => null
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'user_id' => null
        ]);
    }

    /**
     * @test
     */
    public function it_can_modify_relationship_to_a_book_and_remove_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => null
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'book_id' => null
        ]);
    }

    /**
     * @test
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);


        $this->patchJson('/api/v1/comments/1/relationships/users', [
            'data' => [
                'id' => 'this-id-does-not-exist',
                'type' => 'users',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(404);

    }

    /**
     * @test
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'id' => '2',
                'type' => 'books',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(404);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_id_member_is_given_when_updating_a_relationship()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);


        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'type' => 'books',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.id field is required.',
                    'details' => 'Поле data.id обязательно для заполнения.',
                    'source' => [
                        'pointer' => '/data/id',
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

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'id' => 1,
                'type' => 'books',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.id must be a string.',
                    'details' => 'Значение поля data.id должно быть строкой.',
                    'source' => [
                        'pointer' => '/data/id',
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

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'id' => '1',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.type field is required.',
                    'details' => 'Поле data.type обязательно для заполнения.',
                    'source' => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_has_a_value_of_books_when_updating_a_relationship()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->patchJson('/api/v1/comments/1/relationships/books', [
            'data' => [
                'id' => '1',
                'type' => 'random',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The selected data.type is invalid.',
                    'details' => 'Выбранное значение для data.type некорректно.',
                    'source' => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_can_get_related_user_as_a_resource_object_from_related_link()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);


        $this->getJson("/api/v1/comments/1/users", [
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
                        'role' => 'admin',
                        'created_at' => $user->created_at->toJSON(),
                        'updated_at' => $user->updated_at->toJSON(),
                    ]
                ],
            ]);
    }

    /**
     * @test
     */
    public function it_can_get_related_book_as_a_resource_object_from_related_link()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comment = Comment::factory()->make();
        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->getJson("/api/v1/comments/1/books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $book->title,
                        'description' => $book->description,
                        'publication_year' => $book->publication_year,
                        'created_at' => $book->created_at->toJSON(),
                        'updated_at' => $book->updated_at->toJSON(),
                    ]
                ],
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

        $comment = Comment::factory()->make();
        $user->comments()->save($comment);

        $book = Book::factory()->create();
        $book->comments()->save($comment);

        $this->getJson("/api/v1/comments/1", [
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
     */
    public function it_includes_related_resource_objects_for_a_collection_when_an_include_query_param_is_given()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);

        $book = Book::factory()->create();
        $book->comments()->saveMany($comments);

        $this->getJson("/api/v1/comments?include=users,books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => (string)$comments[0]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[0]->message,
                            'created_at' => $comments[0]->created_at->toJSON(),
                            'updated_at' => $comments[0]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[0]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[0]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[0]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[0]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => (string)$comments[1]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[1]->message,
                            'created_at' => $comments[1]->created_at->toJSON(),
                            'updated_at' => $comments[1]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[1]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[1]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[1]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[1]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => (string)$comments[2]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[2]->message,
                            'created_at' => $comments[2]->created_at->toJSON(),
                            'updated_at' => $comments[2]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[2]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[2]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[2]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[2]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_does_not_include_related_resource_objects_for_a_collection_when_an_include_query_param_is_not_given()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);

        $book = Book::factory()->create();
        $book->comments()->saveMany($comments);

        $this->getJson("/api/v1/comments", [
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
     */
    public function it_only_includes_a_related_resource_object_once_for_a_collection()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $comments = Comment::factory(3)->make();
        $user->comments()->saveMany($comments);

        $book = Book::factory()->create();
        $book->comments()->saveMany($comments);

        $this->getJson("/api/v1/comments?include=users,books", [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => (string)$comments[0]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[0]->message,
                            'created_at' => $comments[0]->created_at->toJSON(),
                            'updated_at' => $comments[0]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[0]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[0]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[0]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[0]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => (string)$comments[1]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[1]->message,
                            'created_at' => $comments[1]->created_at->toJSON(),
                            'updated_at' => $comments[1]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[1]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[1]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[1]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[1]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => (string)$comments[2]->id,
                        "type" => "comments",
                        "attributes" => [
                            'message' => $comments[2]->message,
                            'created_at' => $comments[2]->created_at->toJSON(),
                            'updated_at' => $comments[2]->updated_at->toJSON(),
                        ],
                        'relationships' => [
                            'books' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.books',
                                        ['comment' => $comments[2]->id]
                                    ),
                                    'related' => route(
                                        'comments.books',
                                        ['comment' => $comments[2]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $book->id,
                                    'type' => 'books',
                                ]
                            ],
                            'users' => [
                                'links' => [
                                    'self' => route(
                                        'comments.relationships.users',
                                        ['comment' => $comments[2]->id]
                                    ),
                                    'related' => route(
                                        'comments.users',
                                        ['comment' => $comments[2]->id]
                                    ),
                                ],
                                'data' => [
                                    'id' => $user->id,
                                    'type' => 'users',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ]
                ]
            ])->assertJsonMissing([
                'included' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $book->title,
                            'description' => $book->description,
                            'publication_year' => $book->publication_year,
                            'created_at' => $book->created_at->toJSON(),
                            'updated_at' => $book->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => $user->id,
                        "type" => "users",
                        "attributes" => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'admin',
                            'created_at' => $user->created_at->toJSON(),
                            'updated_at' => $user->updated_at->toJSON(),
                        ]
                    ]
                ]
            ]);
    }


    /**
     * @test
     * @watch
     */
    public function when_creating_a_comment_it_can_also_add_relationships_right_away()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ],
                'relationships' => [
                    'users' => [
                        'data' => [
                            'id' => $user->id,
                            'type' => 'users',
                        ]
                    ],
                    'books' => [
                        'data' => [
                            'id' => (string)$book->id,
                            'type' => 'books',
                        ]
                    ]
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
                    "type" => 'comments',
                    "attributes" => [
                        'message' => 'Hello world',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ],
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.books',
                                    ['comment' => 1]
                                ),
                                'related' => route(
                                    'comments.books',
                                    ['comment' => 1]
                                ),
                            ],
                            'data' => [
                                'id' => $book->id,
                                'type' => 'books',
                            ]
                        ],
                        'users' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.users',
                                    ['comment' => 1]
                                ),
                                'related' => route(
                                    'comments.users',
                                    ['comment' => 1]
                                ),
                            ],
                            'data' => [
                                'id' => $user->id,
                                'type' => 'users',
                            ]
                        ]
                    ]
                ]
            ])->assertHeader('Location', url('/api/v1/comments/1'));
        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'message' => 'Hello world',
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * @test
     */
    public function it_validates_relationships_given_when_creating_comment()
    {
//        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ],
                'relationships' => [
                    'users' => [],
                    'books' => [
                        'data' => [
                            'id' => 1,
                            'type' => 'random',
                        ]
                    ]
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.relationships.users.data field is required.',
                    'details' => 'Поле data.relationships.users.data обязательно для заполнения.',
                    'source' => [
                        'pointer' => '/data/relationships/users/data',
                    ]
                ],
                [
                    'title' => 'Validation Error',
//                    'details' => 'The data.relationships.books.data.id must be a string.',
                    'details' => 'Значение поля data.relationships.books.data.id должно быть строкой.',
                    'source' => [
                        'pointer' => '/data/relationships/books/data/id',
                    ]
                ],
                [
                    'title' => 'Validation Error',
//                    'details' => 'The selected data.relationships.books.data.type is invalid.',
                    'details' => 'Выбранное значение для data.relationships.books.data.type некорректно.',
                    'source' => [
                        'pointer' => '/data/relationships/books/data/type',
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function when_updating_a_comment_it_can_also_update_relationships()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comment = Comment::factory()->make();
        $user->comments()->save($comment);
        $book = Book::factory()->create();
        $book->comments()->save($comment);
        $anotherUser = User::factory()->create();
        $anotherBook = Book::factory()->create();
        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => (string)$comment->id,
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ],
                'relationships' => [
                    'users' => [
                        'data' => [
                            'id' => $anotherUser->id,
                            'type' => 'users',
                        ]
                    ],
                    'books' => [
                        'data' => [
                            'id' => (string)$anotherBook->id,
                            'type' => 'books',
                        ]
                    ]
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => 'comments',
                    "attributes" => [
                        'message' => 'Hello world',
//                        'created_at' => now()->setMilliseconds(0)->toJSON(),
//                        'updated_at' => now()->setMilliseconds(0)->toJSON(),
                    ],
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.books',
                                    ['comment' => 1]
                                ),
                                'related' => route(
                                    'comments.books',
                                    ['comment' => 1]
                                ),
                            ],
                            'data' => [
                                'id' => $anotherBook->id,
                                'type' => 'books',
                            ]
                        ],
                        'users' => [
                            'links' => [
                                'self' => route(
                                    'comments.relationships.users',
                                    ['comment' => 1]
                                ),
                                'related' => route(
                                    'comments.users',
                                    ['comment' => 1]
                                ),
                            ],
                            'data' => [
                                'id' => $anotherUser->id,
                                'type' => 'users',
                            ]
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'message' => 'Hello world',
            'user_id' => $anotherUser->id,
            'book_id' => $anotherBook->id,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_relationships_given_when_updating_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $comment = Comment::factory()->make();
        $user->comments()->save($comment);
        $book = Book::factory()->create();
        $book->comments()->save($comment);
        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => (string)$comment->id,
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ],
                'relationships' => [
                    'users' => [],
                    'books' => [
                        'data' => [
                            'id' => 1,
                            'type' => 'random',
                        ]
                    ]
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.relationships.users.data field is required.',
                        'details' => 'Поле data.relationships.users.data обязательно для заполнения.',
                        'source' => [
                            'pointer' => '/data/relationships/users/data',
                        ]
                    ],
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The data.relationships.books.data.id must be a string.',
                        'details' => 'Значение поля data.relationships.books.data.id должно быть строкой.',
                        'source' => [
                            'pointer' => '/data/relationships/books/data/id',
                        ]
                    ],
                    [
                        'title' => 'Validation Error',
//                        'details' => 'The selected data.relationships.books.data.type is invalid.',
                        'details' => 'Выбранное значение для data.relationships.books.data.type некорректно.',
                        'source' => [
                            'pointer' => '/data/relationships/books/data/type',
                        ]
                    ],
                ]
            ]);
    }
}
