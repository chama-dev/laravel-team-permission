<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\TeamPermissionServiceProvider;
use Chama\TeamPermission\Tests\Models\Gym;
use Chama\TeamPermission\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public const USER_MASTER_ID = 1;
    public const USER_FIRST_OWNER_ID = 7;
    public const USER_SECOND_OWNER_ID = 12;

    public function setUp(): void
    {
        parent::setUp();

        Relation::morphMap([
            'gym' => Gym::class,
        ]);

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Chama\TeamPermission\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            TeamPermissionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('app.key', 'base64:'.base64_encode(Encrypter::generateKey($app['config']['app.cipher'])));
    }

    protected function setUpDatabase(): void
    {
        $this->createTables();
        $this->runTeamPermissionMigrations();
        $this->seedModels();
    }

    protected function runTeamPermissionMigrations(): void
    {
        include_once __DIR__.'/../database/migrations/create_team_permission_table.php.stub';
        (new \CreateTeamPermissionTable())->up();
    }

    protected function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->nullable(); // master, registerd
            $table->timestamps();
        });
        Schema::create('gyms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('owner_id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    protected function seedModels(): void
    {
        // Total of 25 users, having master role the first user
        // factory(User::class, 5)->state('master')->create();
        User::factory()->count(5)->master()->create();

        // factory(User::class, 20)->state('registered')->create();
        User::factory()->count(20)->registered()->create();

        // Total of 15 Gyms
        // factory(Gym::class, 5)->create(['owner_id' => self::USER_FIRST_OWNER_ID]);
        Gym::factory()->count(5)->create(['owner_id' => self::USER_FIRST_OWNER_ID]);

        // factory(Gym::class, 10)->create(['owner_id' => self::USER_SECOND_OWNER_ID]);
        Gym::factory()->count(10)->create(['owner_id' => self::USER_SECOND_OWNER_ID]);
    }

}
