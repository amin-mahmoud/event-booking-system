<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 admins
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@eventbooking.com',
            'password' => Hash::make('12341234'),
            'phone' => '+1-555-0101',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin2@eventbooking.com',
            'password' => Hash::make('12341234'),
            'phone' => '+1-555-0102',
            'role' => 'admin',
        ]);

        // Create 3 organizers
        User::create([
            'name' => 'Music Events Co.',
            'email' => 'organizer@organizer1.com',
            'password' => Hash::make('12341234'),
            'phone' => '+1-555-0201',
            'role' => 'organizer',
        ]);

        User::create([
            'name' => 'Sports Events LLC',
            'email' => 'organizer2@eventbooking.com',
            'password' => Hash::make('12341234'),
            'phone' => '+1-555-0202',
            'role' => 'organizer',
        ]);

        User::create([
            'name' => 'Cultural Events Inc.',
            'email' => 'organizer3@eventbooking.com',
            'password' => Hash::make('12341234'),
            'phone' => '+1-555-0203',
            'role' => 'organizer',
        ]);

         $customers = [
            ['name' => 'John Smith', 'email' => 'customer1@customer.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@customer.com'],
            ['name' => 'Michael Brown', 'email' => 'michael.brown@customer.com'],
            ['name' => 'Emma Davis', 'email' => 'emma.davis@customer.com'],
            ['name' => 'James Wilson', 'email' => 'james.wilson@customer.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.anderson@customer.com'],
            ['name' => 'Robert Taylor', 'email' => 'robert.taylor@customer.com'],
            ['name' => 'Jennifer Garcia', 'email' => 'jennifer.garcia@customer.com'],
            ['name' => 'David Martinez', 'email' => 'david.martinez@customer.com'],
            ['name' => 'Ashley Rodriguez', 'email' => 'ashley.rodriguez@customer.com'],
        ];

        foreach ($customers as $index => $customer) {
            User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'password' => Hash::make('12341234'),
                'phone' => '+1-555-03' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'role' => 'customer',
            ]);
        }
    }
}
