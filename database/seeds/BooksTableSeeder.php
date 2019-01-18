<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('books')->insert([
            'title' => 'C++ High Performance',
            'slug' => 'c++-high-performance',
            'description' => 'Write code that scales across CPU registers, multi-core, and machine clusters',
            'author' => 'Viktor Sehr, Björn Andrist',
            'publisher' => 'Packtpub',
            'cover' => 'c++-high-performance.png',
            'price' => 100000,
            // 'weight' => 0.5,
            'status' => 'PUBLISH',
            'created_by' => 1,
        ],[
            'title' => 'Mastering Linux Security and Hardening',
            'slug' => 'mastering-linux-security-and-hardening',
            'description' => 'A comprehensive guide to mastering the art of preventing your Linux system from getting compromised',
            'author' => 'Donald A. Tevault',
            'publisher' => 'Packtpub',
            'cover' => 'mastering-linux-security-and-hardening.png',
            'price' => 125000,
            // 'weight' => 0.5,
            'status' => 'PUBLISH',
            'created_by' => 1,
        ],[
            'title' => 'Mastering PostgreSQL 10',
            'slug' => 'mastering-postgresql-10',
            'description' => 'Master the capabilities of PostgreSQL 10 to efficiently manage and maintain your database',
            'author' => 'Hans-Jürgen Schönig',
            'publisher' => 'Packtpub',
            'cover' => 'mastering-postgresql-10.png',
            'price' => 90000,
            // 'weight' => 0.5,
            'status' => 'PUBLISH',
            'created_by' => 1,
        ],[
            'title' => 'Python Programming Blueprints',
            'slug' => 'c++-high-performance',
            'description' => 'How to build useful, real-world applications in the Python programming language',
            'author' => 'Daniel Furtado, Marcus Pennington',
            'publisher' => 'Packtpub',
            'cover' => 'python-programming-blueprints.png',
            'price' => 75000,
            // 'weight' => 0.5,
            'status' => 'PUBLISH',
            'created_by' => 1,
        ]);
    }
}
