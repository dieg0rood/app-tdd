<?php

namespace Tests\Feature\API;

use App\Models\Book;
use Database\Factories\BookFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BooksControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_books_endpoint(): void
    {
        $books = Book::factory(3)->create();
        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($books) {

            $firstBook = $books->first();

            $json->whereAllType([
                '0.id' => 'integer',
                '0.title' => 'string',
                '0.isbn' => 'string'
            ]);

            $json->hasAll(
                '0.id',
                '0.title',
                '0.isbn'
            );

            $json->whereAll([
                '0.id' => $firstBook->id,
                '0.title' => $firstBook->title,
                '0.isbn' => $firstBook->isbn,
            ]);
        });
    }

    public function test_get_single_book_endpoint(): void
    {
        $book = Book::factory(1)->createOne();
        $response = $this->getJson('/api/books/' . $book->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($book) {

            $json->whereAllType([
                'id' => 'integer',
                'title' => 'string',
                'isbn' => 'string'
            ]);

            $json->hasAll(
                'id',
                'title',
                'isbn',
                'created_at',
                'updated_at'
            );

            $json->whereAll([
                'id' => $book->id,
                'title' => $book->title,
                'isbn' => $book->isbn,
            ]);
        });
    }

    public function test_post_books_endpoint(): void
    {
        $book = Book::factory(1)->makeOne()->toArray();

        $response = $this->postJson('/api/books', $book);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($book) {
            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn'],
                
            ])->etc();

            $json->hasAll(
                'id',
                'title',
                'isbn',
                'created_at',
                'updated_at'
            );
        });
    }
}
