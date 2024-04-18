<?php

use App\Livewire\Admin;
use App\Livewire\Opportunities;
use App\Models\Customer;
use App\Models\Opportunity;
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
    actingAs(User::factory()->create());
    $opportunities = Opportunity::factory()->count(10)->create();

    $lw = Livewire::test(Opportunities\Index::class);
    $lw->assertSet('items', function ($items) {
        expect($items)
            ->toHaveCount(10);

        return true;
    });

    foreach ($opportunities as $opportunity) {
        $lw->assertSee($opportunity->title);
    };
});

test("check the table format", function () {
    actingAs(User::factory()->admin()->create());

    Livewire::test(Opportunities\Index::class)
        ->assertSet('headers', [
            ['key' => 'id', 'label' => '#', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'title', 'label' => 'Title', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'customer.name', 'label' => 'Customer', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'status', 'label' => 'Status', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
            ['key' => 'amount', 'label' => 'Amount', 'sortColumnBy' => 'id', 'sortDirection' => 'asc'],
        ]);
});

it('should be able to filter by title', function () {
    $user = User::factory()->create();
    $divino = Opportunity::factory()->create(['title' => 'Divino']);
    $mario = Opportunity::factory()->create(['title' => 'Mario']);

    actingAs($user);
    Livewire::test(Opportunities\Index::class)
        ->assertSet('items', function ($items) {
            expect($items)->toHaveCount(2);

            return true;
        })
        ->set('search', 'mar')
        ->assertSet('items', function ($items) {
            expect($items)
                ->toHaveCount(1)
                ->first()->title->toBe('Mario');

            return true;
        });
});

it("should be able to sort by title", function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['name' => 'Zack']);
    $divino = Opportunity::factory()->create(['title' => 'Divino', 'customer_id' => $customer->id]);
    $mario = Opportunity::factory()->create(['title' => 'Mario', 'customer_id' => $customer->id]);

    actingAs($user);

    Livewire::test(Opportunities\Index::class)
        ->set('sortDirection', 'asc')
        ->set('sortColumnBy', 'title')
        ->assertSet('items', function ($items) {
            expect($items)
                ->first()->title->toBe('Divino')
                ->and($items)->last()->title->toBe('Mario');

            return true;
        })
        ->set('sortDirection', 'desc')
        ->set('sortColumnBy', 'title')
        ->assertSet('items', function ($items) {
            expect($items)
                ->first()->title->toBe('Mario')
                ->and($items)->last()->title->toBe('Divino');

            return true;
        });
});

it("should be able to paginate the result", function () {
    $user = User::factory()->create();
    Opportunity::factory()->create(['title' => 'Divino', 'status' => 'admin@gmail.com']);
    Opportunity::factory()->count(30)->create();

    actingAs($user);
    Livewire::test(Opportunities\Index::class)
        ->assertSet('items', function (LengthAwarePaginator $items) {
            expect($items)
                ->toHaveCount(15);

            return true;
        });

    Livewire::test(Opportunities\Index::class)
        ->set('perPage', 20)
        ->assertSet('items', function (LengthAwarePaginator $items) {
            expect($items)
                ->toHaveCount(20);

            return true;
        });
});