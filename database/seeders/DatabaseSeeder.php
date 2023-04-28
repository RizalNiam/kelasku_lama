<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
        public function run()
    {
        School::create([
            "school_name"=>"SMK Hidayah"
        ]);

    }
}
