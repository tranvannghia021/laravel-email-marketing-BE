<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $faker = \Faker\Factory::create();
        for($i=1;$i<=1;$i++){
        //     DB::table('customers')->insert([
        //         'id_cus_shopify'=>$i,
        //     'id_shops'=>$i,
        //     'first_name'=>$faker->firstName,
        //     'last_name'=>$faker->lastName,
        //     'country'=>'ud',
        //     'phone'=>'0945251832',
        //     'email'=>$faker->email,
        //     'total_order'=>$i ,
        //     'total_spent'=>$i ,
        //     'cus_created_at'=>"2022-07-".$i." 22:00:".$i,
        //         'created_at'=>date('Y-m-d H:i:s'),
        //         'updated_at'=>date('Y-m-d H:i:s'),
        //     ]);
            DB::table('campaigns')->insert([
            'id_shop'=>'1',
            'name'=>$faker->firstName,
            'thumb'=>'ud',
            'subject'=>'sadadas',
            'email_content'=>'$faker->email',
           
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
            ]);

        }
    }

}
