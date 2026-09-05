<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\Classroom;

class CampusAndClassroomSeeder extends Seeder
{
    public function run()
    {
        // Default Main Campus
        $campus = Campus::firstOrCreate(
            ['code' => 'MAIN-01'],
            [
                'name' => 'Montessori Main Campus',
                'city' => 'Islamabad',
                'address' => 'Sector F-7/2, Hillside Road',
                'phone' => '+92 51 111 222 333',
                'status' => 'active',
            ]
        );

        // Default Montessori Classrooms
        Classroom::firstOrCreate(
            ['code' => 'TOD-01'],
            [
                'campus_id' => $campus->id,
                'name' => 'Nido & Toddler Nest',
                'age_group' => 'Toddler (1.5-3 yrs)',
                'capacity' => 15,
                'description' => 'Prepared environment for toddlers emphasizing sensory-motor development and independence.',
                'status' => 'active',
            ]
        );

        Classroom::firstOrCreate(
            ['code' => 'PRI-01'],
            [
                'campus_id' => $campus->id,
                'name' => 'Children\'s House Alpha',
                'age_group' => 'Primary (3-6 yrs)',
                'capacity' => 25,
                'description' => 'Classic Casa dei Bambini environment with Practical Life, Sensorial, Language, and Math materials.',
                'status' => 'active',
            ]
        );

        Classroom::firstOrCreate(
            ['code' => 'ELE-01'],
            [
                'campus_id' => $campus->id,
                'name' => 'Lower Elementary Cosmos',
                'age_group' => 'Lower Elementary (6-9 yrs)',
                'capacity' => 25,
                'description' => 'Cosmic Education framework fostering scientific inquiry, grammar, and collaborative research.',
                'status' => 'active',
            ]
        );
    }
}
