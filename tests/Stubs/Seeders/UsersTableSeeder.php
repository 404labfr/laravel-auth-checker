<?php

namespace Lab404\Tests\Stubs\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;
use Lab404\Tests\Stubs\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param   void
     * @return  void
     */
    public function run()
    {
        // Create first user
        $user = (new User(['name' => 'John', 'email' => 'john@doe.com', 'password' => bcrypt('secret')]))->save();

        // Create two logins
        $date = Carbon::now();
        $login1 = (new Login(['ip_address' => '1.2.3.4', 'created_at' => $date->addDay(1)->toDateTimeString()]));
        $login2 = (new Login(['ip_address' => '5.6.7.8', 'created_at' => $date->addDays(5)->toDateTimeString()]));

        // Attach logins
        $user->logins()->saveMany([$login1, $login2]);

        // Create one device
        $device = (new Device(['browser' => 'Chrome', 'browser_version' => 50, 'platform' => 'OS X', 'platform_version' => '10_10', 'is_desktop' => 1, 'language' => 'fr_FR']));
        $login1->device()->save($device);
        $login2->device()->save($device);

    }
}
