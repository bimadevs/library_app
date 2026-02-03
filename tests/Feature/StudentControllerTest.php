<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private SchoolClass $class;
    private Major $major;
    private AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->class = SchoolClass::factory()->create();
        $this->major = Major::factory()->create();
        $this->academicYear = AcademicYear::factory()->create();
    }

    public function test_index_page_is_displayed()
    {
        $response = $this->actingAs($this->user)->get(route('students.index'));
        $response->assertOk();
    }

    public function test_create_page_is_displayed()
    {
        $response = $this->actingAs($this->user)->get(route('students.create'));
        $response->assertOk();
        $response->assertViewIs('students.form');
    }

    public function test_store_student_successfully()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('photo.jpg');

        $data = [
            'nis' => '12345',
            'name' => 'Test Student',
            'birth_place' => 'City',
            'birth_date' => '2005-01-01',
            'address' => 'Address',
            'class_id' => $this->class->id,
            'major_id' => $this->major->id,
            'gender' => 'male',
            'academic_year_id' => $this->academicYear->id,
            'phone' => '08123456789',
            'max_loan' => 3,
            'is_active' => true,
            'photo' => $file,
        ];

        $response = $this->actingAs($this->user)->post(route('students.store'), $data);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['nis' => '12345', 'name' => 'Test Student']);

        $student = Student::where('nis', '12345')->first();
        Storage::disk('public')->assertExists($student->photo);
    }

    public function test_store_student_validation_fails()
    {
        $response = $this->actingAs($this->user)->post(route('students.store'), []);
        $response->assertSessionHasErrors(['nis', 'name']);
    }

    public function test_edit_page_is_displayed()
    {
        $student = Student::factory()->create();
        $response = $this->actingAs($this->user)->get(route('students.edit', $student));
        $response->assertOk();
        $response->assertViewIs('students.form');
    }

    public function test_update_student_successfully()
    {
        $student = Student::factory()->create([
            'class_id' => $this->class->id,
            'major_id' => $this->major->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $data = [
            'nis' => $student->nis,
            'name' => 'Updated Name',
            'birth_place' => 'City',
            'birth_date' => '2005-01-01',
            'address' => 'Address',
            'class_id' => $this->class->id,
            'major_id' => $this->major->id,
            'gender' => 'female',
            'academic_year_id' => $this->academicYear->id,
            'phone' => '08123456789',
            'max_loan' => 5,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->put(route('students.update', $student), $data);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Updated Name']);
    }

    public function test_destroy_student_successfully()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('students.destroy', $student));

        $response->assertRedirect(route('students.index'));
        $this->assertSoftDeleted($student);
    }

    public function test_download_template()
    {
        $response = $this->actingAs($this->user)->get(route('students.import.template'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
