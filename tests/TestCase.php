<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\TeamPermissionServiceProvider;
use Chama\TeamPermission\Tests\Models\Gym;
use Chama\TeamPermission\Tests\Models\User;
use CreateTeamPermissionTable;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public const USER_MASTER_ID = 1;
    public const USER_FIRST_OWNER_ID = 7;
    public const USER_SECOND_OWNER_ID = 12;

    /**
     * @var Generator
     */
    protected $faker;

    public function setUp(): void
    {
        $this->checkCustomRequirements();

        Relation::morphMap([
            'gym' => Gym::class,
        ]);

        parent::setUp();
        $this->setupFaker();
        $this->setupFactories();
        $this->setUpDatabase();
    }

    protected function checkCustomRequirements(): void
    {
        collect($this->getAnnotations())->filter(function ($location) {
            return in_array('!Travis', Arr::get($location, 'requires', []), true);
        })->each(function ($location) {
            getenv('TRAVIS') && $this->markTestSkipped('Travis will not run this test.');
        });
    }

    /**
     * @param Generator $faker
     * @return TestCase
     */
    public function setFaker(Generator $faker): TestCase
    {
        $this->faker = $faker;

        return $this;
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
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('app.key', 'base64:'.base64_encode(
                Encrypter::generateKey($app['config']['app.cipher'])
            ));
    }

    protected function setUpDatabase(): void
    {
        $this->createTables();
        $this->createTeamPermissionTable();
        $this->seedModels();
    }

    protected function createTeamPermissionTable(): void
    {
        include_once __DIR__.'/../database/migrations/create_team_permission_table.php.stub';
        (new CreateTeamPermissionTable())->up();
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
        factory(User::class, 5)->state('master')->create();
        factory(User::class, 20)->state('registered')->create();

        // Total of 15 Gyms
        factory(Gym::class, 5)->create(['owner_id' => self::USER_FIRST_OWNER_ID]);
        factory(Gym::class, 10)->create(['owner_id' => self::USER_SECOND_OWNER_ID]);
    }

//    public function getLastActivity(): ?Activity
//    {
//        return Activity::all()->last();
//    }

    public function markTestAsPassed(): void
    {
        $this->assertTrue(true);
    }

    public function isLaravel7OrLower(): bool
    {
        $majorVersion = (int) substr(App::version(), 0, 1);

        return $majorVersion <= 7;
    }

    public function setupFaker(): void
    {
        $this->setFaker(Factory::create());
    }

    protected function setupFactories(): void
    {
        $this->withFactories(__DIR__.'/../database/factories');
    }
}
