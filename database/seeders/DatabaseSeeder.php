<?php

namespace Database\Seeders;

use App\Models\AdminProfile;
use App\Models\Candidate;
use App\Models\District;
use App\Models\ElectionSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $districts = collect([
            'Makwanpur',
            'Kathmandu',
            'Lalitpur',
            'Bhaktapur',
            'Chitwan',
            'Pokhara',
            'Biratnagar',
            'Dharan',
            'Hetauda',
            'Butwal',
            'Nepalgunj',
            'Dhangadhi',
            'Janakpur',
        ])->map(fn (string $name) => District::firstOrCreate(['name' => $name]));

        $admin = User::updateOrCreate(
            ['contact_number' => '9800000000'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin12345'),
                'pattern_lock' => '1258',
                'role' => 'admin',
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        AdminProfile::updateOrCreate(
            ['user_id' => $admin->id],
            ['age' => 35, 'contact_number' => '9800000000']
        );

        $positions = [
            1 => 'President',
            2 => 'President',
            3 => 'Vice President',
            4 => 'Vice President',
        ];
        $parties = ['Unity Party', 'Forward Nepal', 'Citizen Forum', 'Independent'];

        foreach ($districts as $index => $district) {
            foreach (range(1, 4) as $candidateNumber) {
                Candidate::updateOrCreate(
                    [
                        'district_id' => $district->id,
                        'email' => "candidate{$candidateNumber}{$index}@example.com",
                    ],
                    [
                        'name' => fake()->unique()->firstName().' '.$district->name,
                        'party' => Arr::random($parties),
                        'age' => fake()->numberBetween(35, 68),
                        'position' => $positions[$candidateNumber],
                        'is_active' => true,
                    ]
                );
            }
        }

        $approvedDistrict = $districts->first();
        $pendingDistrict = $districts->get(1);
        $rejectedDistrict = $districts->get(2);

        User::updateOrCreate(
            ['contact_number' => '9811111111'],
            [
                'name' => 'Approved User',
                'password' => Hash::make('user12345'),
                'pattern_lock' => '2589',
                'role' => 'user',
                'status' => 'approved',
                'approved_at' => now(),
                'date_of_birth' => '1998-04-17',
                'district_id' => $approvedDistrict?->id,
                'citizenship_number' => '12345-67890',
                'voter_id_number' => '100200300',
            ]
        );

        User::updateOrCreate(
            ['contact_number' => '9822222222'],
            [
                'name' => 'Pending User',
                'password' => Hash::make('user12345'),
                'pattern_lock' => '1236',
                'role' => 'user',
                'status' => 'pending',
                'date_of_birth' => '2000-08-10',
                'district_id' => $pendingDistrict?->id,
                'citizenship_number' => '55555/22222',
                'voter_id_number' => '100200301',
            ]
        );

        User::updateOrCreate(
            ['contact_number' => '9833333333'],
            [
                'name' => 'Rejected User',
                'password' => Hash::make('user12345'),
                'pattern_lock' => '1478',
                'role' => 'user',
                'status' => 'rejected',
                'date_of_birth' => '2001-01-19',
                'district_id' => $rejectedDistrict?->id,
                'citizenship_number' => '99999-11111',
                'voter_id_number' => '100200302',
                'rejection_message' => 'You can try once again',
            ]
        );

        foreach ($districts as $district) {
            ElectionSetting::updateOrCreate(
                ['district_id' => $district->id],
                [
                    'is_active' => true,
                    'ended_at' => null,
                    'ends_at' => now()->addDays(2),
                ]
            );
        }
    }
}
