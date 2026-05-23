<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.url', 'http://localhost');
        URL::forceRootUrl('http://localhost');
    }

    public function test_registration_rejects_duplicate_citizenship_number(): void
    {
        Storage::fake('public');
        $district = District::query()->create(['name' => 'Kathmandu']);

        User::query()->create([
            'name' => 'Existing User',
            'contact_number' => '9800000000',
            'password' => 'password123',
            'pattern_lock' => '1234',
            'role' => 'user',
            'status' => 'approved',
            'date_of_birth' => '1990-01-01',
            'district_id' => $district->id,
            'last_known_district_name' => $district->name,
            'citizenship_number' => '12345-67890',
            'voter_id_number' => '1111111111',
        ]);

        $response = $this->from('/register')->post('/register', $this->validPayload($district, [
            'contact_number' => '9800000001',
            'citizenship_number' => '12345-67890',
            'voter_id_number' => '2222222222',
        ]));

        $response->assertSessionHasErrors('citizenship_number');
    }

    public function test_registration_rejects_duplicate_voter_id_number(): void
    {
        Storage::fake('public');
        $district = District::query()->create(['name' => 'Lalitpur']);

        User::query()->create([
            'name' => 'Existing User',
            'contact_number' => '9800000002',
            'password' => 'password123',
            'pattern_lock' => '1234',
            'role' => 'user',
            'status' => 'approved',
            'date_of_birth' => '1990-01-01',
            'district_id' => $district->id,
            'last_known_district_name' => $district->name,
            'citizenship_number' => '55555/22222',
            'voter_id_number' => '3333333333',
        ]);

        $response = $this->from('/register')->post('/register', $this->validPayload($district, [
            'contact_number' => '9800000003',
            'citizenship_number' => '77777-88888',
            'voter_id_number' => '3333333333',
        ]));

        $response->assertSessionHasErrors('voter_id_number');
    }

    public function test_registration_rejects_underage_user(): void
    {
        Storage::fake('public');
        $district = District::query()->create(['name' => 'Bhaktapur']);

        $response = $this->from('/register')->post('/register', $this->validPayload($district, [
            'contact_number' => '9800000004',
            'citizenship_number' => '88888-99999',
            'voter_id_number' => '4444444444',
            'date_of_birth' => now()->subYears(17)->toDateString(),
        ]));

        $response->assertSessionHasErrors('date_of_birth');
    }

    public function test_registration_accepts_exactly_eighteen_year_old_user(): void
    {
        Storage::fake('public');
        $district = District::query()->create(['name' => 'Pokhara']);

        $response = $this->from('/register')->post('/register', $this->validPayload($district, [
            'contact_number' => '9800000005',
            'citizenship_number' => '10101-20202',
            'voter_id_number' => '5555555555',
            'date_of_birth' => now()->subYears(18)->toDateString(),
        ]));

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'contact_number' => '9800000005',
            'citizenship_number' => '10101-20202',
            'voter_id_number' => '5555555555',
        ]);
    }

    private function validPayload(District $district, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'contact_number' => '9800000099',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'pattern_lock' => '1234',
            'date_of_birth' => '2000-01-01',
            'district_id' => $district->id,
            'citizenship_number' => '99999-00000',
            'voter_id_number' => '9999999999',
            'image' => UploadedFile::fake()->image('user.png'),
        ], $overrides);
    }
}
