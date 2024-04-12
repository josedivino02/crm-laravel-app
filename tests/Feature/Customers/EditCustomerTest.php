<?php

use App\Livewire\Customers;
use App\Models\Customer;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;use Livewire\Livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
    $this->customer = Customer::factory()->create();
});

it("should be able to update a customer", function () {
    Livewire::test(Customers\Update::class)
        ->set('customer', $this->customer)
        ->set('customer.name', 'Divino')
        ->assertPropertyWired('customer.name')
        ->set('customer.email', 'jose@divino.com')
        ->assertPropertyWired('customer.email')
        ->set('customer.phone', '123456789')
        ->assertPropertyWired('customer.phone')
        ->call('save')
        ->assertMethodWiredToForm('save')
        ->assertHasNoErrors();

    assertDatabaseHas('customers', [
        'id' => $this->customer->id,
        'name' => 'Divino',
        'email' => 'jose@divino.com',
        'phone' => '123456789',
        'type' => 'customer',
    ]);
});

describe('validations', function () {
    test('name', function ($rule, $value) {
        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.name', $value)
            ->call('save')
            ->assertHasErrors(['customer.name' => $rule]);
    })->with([
        'required' => ['required', ''],
        'min' => ['min', 'Jo'],
        'max' => ['max', str_repeat('a', 256)],
    ]);

    test('email should be required if we dont have a phone number', function () {
        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', '')
            ->set('customer.phone', '')
            ->call('save')
            ->assertHasErrors(['customer.email' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', '')
            ->set('customer.phone', '1232132')
            ->call('save')
            ->assertHasNoErrors(['customer.email' => 'required_without']);
    });

    test('email should be valid', function () {
        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['customer.email' => 'email']);

        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', 'jose@divino.com')
            ->call('save')
            ->assertHasNoErrors(['customer.email' => 'email']);
    });

    test('email should be unique', function () {
        Customer::factory()->create(['email' => 'jose@divino.com']);

        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', 'jose@divino.com')
            ->call('save')
            ->assertHasErrors(['customer.email' => 'unique']);
    });

    test('phone should be required if email is empty', function () {
        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', '')
            ->set('customer.phone', '')
            ->call('save')
            ->assertHasErrors(['customer.phone' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.email', 'jose@divino.com')
            ->set('customer.phone', '')
            ->call('save')
            ->assertHasNoErrors(['customer.phone' => 'required_without']);
    });

    test('phone should be unique', function () {

        Customer::factory()->create(['phone' => '123456789']);

        Livewire::test(Customers\Update::class)
            ->set('customer', $this->customer)
            ->set('customer.phone', '123456789')
            ->call('save')
            ->assertHasErrors(['customer.phone' => 'unique']);

    });
});

test("check if component is in the page", function () {
    Livewire::test(Customers\Index::class)
        ->assertContainsLivewireComponent('customers.create');
});