<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::where('id', '>', 0)->delete();

        factory(App\User::class)->create([
            'email' => 'dapo@softcom.ng',
            'is_admin' => true,
            'is_active' => true,
            'can_allocate' => true,
            'can_unbundle' => true,
            'can_repack' => true,
            'can_enroll' => true,
        ]);

        factory(App\User::class)->create([
            'email' => 'test1@softcom.ng',
            'is_admin' => false,
            'is_active' => true,
            'can_allocate' => true,
        ]);

        factory(App\User::class)->create([
            'email' => 'test2@softcom.ng',
            'is_admin' => false,
            'is_active' => true,
            'can_unbundle' => true,
        ]);

        factory(App\User::class)->create([
            'email' => 'test3@softcom.ng',
            'is_admin' => false,
            'is_active' => true,
            'can_repack' => true,
        ]);

        factory(App\User::class)->create([
            'email' => 'test4@softcom.ng',
            'is_admin' => false,
            'is_active' => true,
            'can_enroll' => true,
        ]);
    }
}
