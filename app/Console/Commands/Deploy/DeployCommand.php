<?php

namespace App\Console\Commands\Deploy;

use App\User;
use App\Models\Rol;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launch:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This script is launched every time it is deployed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createUpdatePermissions();

        $this->info('Scripts launched successfully. administration (Email: superadmin@filsend.com, Password: password)');
    }

    private function createUpdatePermissions()
    {
        $this->call('migrate');

        $permissions = config('filsend.permissions');
        $array_permissions = [];

        foreach ($permissions as $p) {
            $p = Permission::updateOrCreate(
                ['name' => $p['name']],
                ['display_name' => $p['display_name']]
            );
            $array_permissions[] = $p['name'];
        }

        $role_superadmin = Rol::updateOrCreate(['name' => 'Super Admin']);
        $role_superadmin->syncPermissions($array_permissions);
        if(!$user_admin = User::where('username','superadmin')->first()) {
            $user_admin = factory(User::class)->create([
                    'email' => 'superadmin@filsend.com',
                    'firstname' => 'Superadmin',
                    'secondname' => null,
                    'lastname' => 'Superadmin',
                    'username' => 'superadmin',
                    'password' => Hash::make('password'),
                ]);
        }
        $user_admin->assignRole($role_superadmin->name);

        if(User::count() < 2) {
            $this->call('db:seed', [
                '--class' => 'UsersTableSeeder'
            ]);
        }

        $this->call('config:clear');
        $this->call('cache:clear');
    }
}
