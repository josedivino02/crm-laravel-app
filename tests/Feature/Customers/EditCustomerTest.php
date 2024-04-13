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
        ->call('load', $this->customer->id)
        ->set('form.name', 'Divino')
        ->assertPropertyWired('form.name')
        ->set('form.email', 'jose@divino.com')
        ->assertPropertyWired('form.email')
        ->set('form.phone', '123456789')
        ->assertPropertyWired('form.phone')
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
            ->call('load', $this->customer->id)
            ->set('form.name', $value)
            ->call('save')
            ->assertHasErrors(['name' => $rule]);
    })->with([
        'required' => ['required', ''],
        'min' => ['min', 'Jo'],
        'max' => ['max', str_repeat('a', 256)],
    ]);

    test('email should be required if we dont have a phone number', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasErrors(['email' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '1232132')
            ->call('save')
            ->assertHasNoErrors(['email' => 'required_without']);
    });

    test('email should be valid', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['email' => 'email']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'jose@divino.com')
            ->call('save')
            ->assertHasNoErrors(['email' => 'email']);
    });

    test('email should be unique', function () {
        Customer::factory()->create(['email' => 'jose@divino.com']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'jose@divino.com')
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    });

    test('phone should be required if email is empty', function () {
        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', '')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasErrors(['phone' => 'required_without']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.email', 'jose@divino.com')
            ->set('form.phone', '')
            ->call('save')
            ->assertHasNoErrors(['phone' => 'required_without']);
    });

    test('phone should be unique', function () {

        Customer::factory()->create(['phone' => '123456789']);

        Livewire::test(Customers\Update::class)
            ->call('load', $this->customer->id)
            ->set('form.phone', '123456789')
            ->call('save')
            ->assertHasErrors(['phone' => 'unique']);

    });
});

test("check if component is in the page", function () {
    Livewire::test(Customers\Index::class)
        ->assertContainsLivewireComponent('customer.update');
});