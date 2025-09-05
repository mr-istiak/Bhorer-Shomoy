<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Position::$positions as $position) {
            $newPosition = new Position();
            $newPosition->name = $position;
            $newPosition->save();
        }
    }
}
