<?php

use App\Enum\Can;
use App\Livewire\Admin;
use App\Models\Permission;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use Illuminate\Pagination\LengthAwarePaginator;use Livewire\Livewire;

it("should be able to access the route customers", function () {
    actingAs(User::factory()->create());

    get(route('customers'))
        ->assertOk();
});

test("let's create a livewire component to list all customers in the page", function () {
    actingAs(User::factory()->admin()->create());
    $customers = User::factory()->count(10)->create();

    $lw = Livewire::test(Customers\Index::class);
    $lw->assertSet('customers', function ($customers) {
        expect($customers)
        // ->toBeInstanceOf(LengthAwarePaginator::class)
            ->toHaveCount(11);

        return true;
    });

    foreach ($customers as $user) {
        $lw->assertSee($user->name);
    };
});

test("check the table format", function () {
    actingAs(User::factory()->admin()->create());

    Livewire::test(Customers\Index::class)
        ->assertSet('headers', [
            ['key' => 'id', 'label' => '#', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'name', 'label' => 'Name', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'email', 'label' => 'Email', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
        ]);
});

it("should be able to filter by permission.key", function () {
    $admin = User::factory()->admin()->create(['name' => 'Divino', "email" => 'admin@gmail.com']);
    $mario = User::factory()->create(['name' => 'Mario', 'email' => "little_guy@gmail.com"]);

    actingAs($admin);

    Livewire::test(Customers\Index::class)
        ->assertSet('customers', function ($customers) {
            expect($customers)->toHaveCount(2);

            return true;
        })
        ->set('search', 'mar')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(1)
                ->first()->name->toBe("Mario");

            return true;
        })
        ->set('search', 'guy')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(1)
                ->first()->name->toBe("Mario");

            return true;
        });
});

it("should be able to filter by name and email", function () {
    $admin = User::factory()->admin()->create(['name' => 'Divino', "email" => 'admin@gmail.com']);
    $nonAdmin = User::factory()->withPermission(Can::TESTING)->create(['name' => 'Mario', 'email' => "little_guy@gmail.com"]);

    $permission = Permission::where('key', '=', Can::BE_AN_ADMIN->value)->first();
    $permission2 = Permission::where('key', '=', Can::TESTING->value)->first();

    actingAs($admin);

    Livewire::test(Customers\Index::class)
        ->assertSet('customers', function ($customers) {
            expect($customers)->toHaveCount(2);

            return true;
        })
        ->set('search_permissions', [$permission->id, $permission2->id])
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(2);

            return true;
        });
});

it("should be able to list deleted customers", function () {
    $admin = User::factory()->admin()->create(['name' => 'Divino', "email" => 'admin@gmail.com']);
    $deletedcustomers = User::factory()->count(2)->create(['deleted_at' => now()]);

    actingAs($admin);

    Livewire::test(Customers\Index::class)
        ->assertSet('customers', function ($customers) {
            expect($customers)->toHaveCount(1);

            return true;
        })
        ->set('search_trash', true)
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->toHaveCount(2);

            return true;
        });
});

it("should be able to sort by name", function () {
    $admin = User::factory()->admin()->create(['name' => 'Divino', "email" => 'admin@gmail.com']);
    $nonAdmin = User::factory()->withPermission(Can::TESTING)->create(['name' => 'Mario', 'email' => "little_guy@gmail.com"]);

    actingAs($admin);

    Livewire::test(Customers\Index::class)
        ->set('sortDirection', 'asc')
        ->set('sortColumnBy', 'name')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->first()->name->toBe('Divino')
                ->and($customers)->last()->name->toBe('Mario');

            return true;
        })
        ->set('sortDirection', 'desc')
        ->set('sortColumnBy', 'name')
        ->assertSet('customers', function ($customers) {
            expect($customers)
                ->first()->name->toBe('Mario')
                ->and($customers)->last()->name->toBe('Divino');

            return true;
        });
});

it("should be able to paginate the result", function () {
    $admin = User::factory()->admin()->create(['name' => 'Divino', "email" => 'admin@gmail.com']);
    $nonAdmin = User::factory()->withPermission(Can::TESTING)->count(30)->create();

    actingAs($admin);

    Livewire::test(Customers\Index::class)
        ->set('perPage', 20)
        ->assertSet('customers', function (LengthAwarePaginator $customers) {
            expect($customers)
                ->toHaveCount(20);

            return true;
        });
});
