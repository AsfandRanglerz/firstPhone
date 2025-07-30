<?php

namespace Database\Seeders;

use App\Models\SideMenu;
use Illuminate\Database\Seeder;

class sideMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SideMenu::insert([
            [
                'id' => '1',
                'name' => 'Sub Admins',
            ],
            [
                'id' => '2',
                'name' => 'Authorized Dealers',
            ],
            [
                'id' => '3',
                'name' => 'Farmers',
            ],
            [
                'id' => '4',
                'name' => 'Ensured Crops',
            ],
            [
                'id' => '5',
                'name' => 'Land Data Management',
            ],
            [
                'id' => '6',
                'name' => 'Insurance Companies',
            ],
            [
                'id' => '7',
                'name' => 'Insurance Types & Sub-Types',
            ],
            [
                'id' => '8',
                'name' => 'Insurance Claim Requests',
            ],
            [
                'id' => '9',
                'name' => 'Notifications',
            ],
        ]);
    }
}
