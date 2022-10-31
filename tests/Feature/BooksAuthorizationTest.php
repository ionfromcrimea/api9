<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BooksAuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function a_user_cannot_create_a_book()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(403)->assertJson([
                'errors' => [
                    [
                        'title' => 'Access Denied Http Exception',
                        'details' => 'This action is unauthorized.',
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function an_admin_can_create_a_book()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($user, 'sanctum');
        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(201);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_cannot_update_a_book()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->postJson('/api/v1/books', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_update_a_book()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->patchJson('/api/v1/books/1', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_cannot_delete_a_book()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->delete('/api/v1/books/1', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_delete_a_book()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->delete('/api/v1/books/1', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_can_fetch_a_list_of_books()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->actingAs($user, 'sanctum');
        $books = Book::factory(3)->create();
        $this->get('/api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_fetch_a_list_of_books()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($user, 'sanctum');
        $books = Book::factory(3)->create();
        $this->get('/api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_can_fetch_a_single_book()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_fetch_a_single_book()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $this->actingAs($user, 'sanctum');
        $book = Book::factory()->create();
        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_cannot_modify_relationship_links_for_authors()
    {
        $book = Book::factory()->create();
        $authors = Author::factory(10)->create();
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        $this->actingAs($user, 'sanctum');
        $this->patchJson('/api/v1/books/1/relationships/authors', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'authors',
                ],
                [
                    'id' => '6',
                    'type' => 'authors',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_modify_relationship_links_for_authors()
    {
        $book = Book::factory()->create();
        $authors = Author::factory(10)->create();
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $this->actingAs($user, 'sanctum');
        $this->patchJson('/api/v1/books/1/relationships/authors', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'authors',
                ],
                [
                    'id' => '6',
                    'type' => 'authors',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
    }
}
