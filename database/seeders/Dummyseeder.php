<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Dummyseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Userdata=[
            [
            'name'=>'agus',
            'email'=>'admin@gmail.com',
            'role'=>'admin',
            'password'=>bcrypt('admin1234')

            ],
            

        ];
    }
}
