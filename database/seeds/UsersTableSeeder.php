<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{

		DB::table('users')->truncate();
		DB::table('roles')->truncate();
        DB::table('role_users')->truncate();

		$role = [
			'name' => 'Administrator',
			'slug' => 'administrator',
			'permissions' => [
				'admin' => true,
			]
        ];

        $adminRole = Sentinel::getRoleRepository()->createModel()->fill($role)->save();

		$accountManagerRole = [
			'name' => 'Account Manager',
			'slug' => 'account_manager',
        ];

        Sentinel::getRoleRepository()->createModel()->fill($accountManagerRole)->save();

        $clientRole = [
			'name' => 'Client',
			'slug' => 'client',
        ];

        Sentinel::getRoleRepository()->createModel()->fill($clientRole)->save();

		$admin = [
            'email'    => 'admin@example.com',
			'username'    => 'admin',
			'password' => 'admin',
        ];

		$adminUser = Sentinel::registerAndActivate($admin);
		$adminUser->roles()->attach($adminRole);

	}
}
