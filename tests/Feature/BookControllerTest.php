<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookSource;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Publisher $publisher;
    private Classification $classification;
    private Category $category;
    private BookSource $bookSource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->publisher = Publisher::factory()->create();
        $this->classification = Classification::factory()->create();
        $this->category = Category::factory()->create();
        $this->bookSource = BookSource::factory()->create();
    }

    public function test_index_page_is_displayed()
    {
        $response = $this->actingAs($this->user)->get(route('books.index'));
        $response->assertOk();
    }

    public function test_create_page_is_displayed()
    {
        $response = $this->actingAs($this->user)->get(route('books.create'));
        $response->assertOk();
        $response->assertViewIs('books.form');
    }

    public function test_store_book_successfully()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cover.jpg');

        $data = [
            'code' => 'B001',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publisher_id' => $this->publisher->id,
            'publish_place' => 'City',
            'publish_year' => 2024,
            'stock' => 5,
            'page_count' => 200,
            'classification_id' => $this->classification->id,
            'category_id' => $this->category->id,
            'shelf_location' => 'A-1',
            'book_source_id' => $this->bookSource->id,
            'entry_date' => now()->format('Y-m-d'),
            'cover_image' => $file,
        ];

        $response = $this->actingAs($this->user)->post(route('books.store'), $data);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['code' => 'B001', 'title' => 'Test Book']);

        $book = Book::where('code', 'B001')->first();
        Storage::disk('public')->assertExists($book->cover_image);
    }

    public function test_store_book_validation_fails()
    {
        $response = $this->actingAs($this->user)->post(route('books.store'), []);
        $response->assertSessionHasErrors(['code', 'title', 'author']);
    }

    public function test_edit_page_is_displayed()
    {
        $book = Book::factory()->create();
        $response = $this->actingAs($this->user)->get(route('books.edit', $book));
        $response->assertOk();
        $response->assertViewIs('books.form');
    }

    public function test_update_book_successfully()
    {
        $book = Book::factory()->create([
             'publisher_id' => $this->publisher->id,
             'classification_id' => $this->classification->id,
             'category_id' => $this->category->id,
             'book_source_id' => $this->bookSource->id,
        ]);

        $data = [
            'code' => $book->code,
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'publisher_id' => $this->publisher->id,
            'publish_place' => 'City',
            'publish_year' => 2024,
            'stock' => 5,
            'page_count' => 200,
            'classification_id' => $this->classification->id,
            'category_id' => $this->category->id,
            'shelf_location' => 'A-1',
            'book_source_id' => $this->bookSource->id,
            'entry_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)->put(route('books.update', $book), $data);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Updated Title']);
    }

    public function test_destroy_book_successfully()
    {
        $book = Book::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertSoftDeleted($book);
    }

    public function test_destroy_book_fails_with_active_loans()
    {
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);
        Loan::factory()->create(['book_copy_id' => $copy->id, 'status' => 'active']);

        $response = $this->actingAs($this->user)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'deleted_at' => null]);
    }

    public function test_download_template()
    {
        $response = $this->actingAs($this->user)->get(route('books.import.template'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
