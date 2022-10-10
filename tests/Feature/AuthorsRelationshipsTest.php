<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthorsRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_a_relationship_to_books_adhering_to_json_api_spec()
    {
        $author = Author::factory()->create();
        $books = Book::factory(3)->create();
        $author->books()->sync($books->pluck('id'));
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->getJson('/api/v1/authors/1?include=books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'authors',
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['author' => $author->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['author' => $author->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $books->get(0)->id,
                                    'type' => 'books'
                                ],
                                [
                                    'id' => $books->get(1)->id,
                                    'type' => 'books'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_books_returns_all_related_books_as_resource_id_objects()
    {
        $author = Author::factory()->create();
        $books = Book::factory(3)->create();
        $author->books()->sync($books->pluck('id'));
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->getJson('/api/v1/authors/1/relationships/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'books',
                    ],
                    [
                        'id' => '2',
                        'type' => 'books',
                    ],
                    [
                        'id' => '3',
                        'type' => 'books',
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_authors_and_add_new_relationships()
    {
        $author = Author::factory()->create();
        $books = Book::factory(10)->create();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->patchJson('/api/v1/authors/1/relationships/books', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'books',
                ],
                [
                    'id' => '6',
                    'type' => 'books',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        $this->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 5,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 6,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_get_all_related_books_as_resource_objects_from_related_link()
    {
        $author = Author::factory()->create();
        $books = Book::factory(3)->create();
        $author->books()->sync($books->pluck('id'));
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $this->getJson('/api/v1/authors/1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        "id" => '1',
                        "type" => "books",
                        "attributes" => [
                            'title' => $books[0]->title,
                            'description' => $books[0]->description,
                            'publication_year' => $books[0]->publication_year,
                            'created_at' => $books[0]->created_at->toJSON(),
                            'updated_at' => $books[0]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '2',
                        "type" => "books",
                        "attributes" => [
                            'title' => $books[1]->title,
                            'description' => $books[1]->description,
                            'publication_year' => $books[1]->publication_year,
                            'created_at' => $books[1]->created_at->toJSON(),
                            'updated_at' => $books[1]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '3',
                        "type" => "books",
                        "attributes" => [
                            'title' => $books[2]->title,
                            'description' => $books[2]->description,
                            'publication_year' => $books[2]->publication_year,
                            'created_at' => $books[2]->created_at->toJSON(),
                            'updated_at' => $books[2]->updated_at->toJSON(),
                        ]
                    ],
                ]
            ]);
    }
}
