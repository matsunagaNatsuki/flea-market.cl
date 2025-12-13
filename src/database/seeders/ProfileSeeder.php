<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1,
            'name' => '松永 菜月',
            'postal_code' => '111-1111',
            'address' => '佐賀県佐賀市',
            'building' => '111ビル',
        ];
        Profile::create($param);

        $param = [
            'user_id' => 2,
            'name' => '堤 菜月',
            'postal_code' => '222-2222',
            'address' => '佐賀県伊万里市',
            'building' => '222マンション',
        ];
        Profile::create($param);

        $param = [
            'user_id' => 3,
            'name' => 'まっちゃん',
            'postal_code' => '333-3333',
            'address' => '佐賀県佐賀市大和町',
            'building' => '333アパート',
        ];
        Profile::create($param);
    }

}
