<?php

use App\Livewire\Opportunities;
use App\Models\{Customer, User};
use Livewire\Livewire;

use function Pest\Laravel\{actingAs, assertDatabaseHas};

beforeEach(function () {
    $user = User::factory()->create();
    actingAs($user);
});

it("should be able to create a opportunity", function () {
    $customer = Customer::factory()->create();
    ;

    Livewire::test(Opportunities\Create::class)
        ->set('form.customer_id', $customer->id)
        ->set('form.title', 'Divino')
        ->assertPropertyWired('form.title')
        ->set('form.status', 'won')
        ->assertPropertyWired('form.status')
        ->set('form.amount', '123.45')
        ->assertPropertyWired('form.amount')
        ->call('save')
        ->assertMethodWiredToForm('save')
        ->assertHasNoErrors();

    assertDatabaseHas('opportunities', [
        'customer_id' => $customer->id,
        'title'       => 'Divino',
        'status'      => 'won',
        'amount'      => '123.45',
    ]);
});

describe('validations', function () {
    test('customer', function ($rule, $value) {
        Livewire::test(Opportunities\Create::class)
            ->set('form.customer_id' . $value)
            ->call('save')
            ->assertHasErrors(['customer_id' => $rule]);
    })->with([
        'required' => ['required', ''],
        'exists'   => ['exists', 9430],
    ]);

    test('title', function ($rule, $value) {
        Livewire::test(Opportunities\Create::class)
            ->set('form.title', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
        'min'      => ['min', 'Jo'],
        'max'      => ['max', str_repeat('a', 256)],
    ]);

    test('status', function ($rule, $value) {
        Livewire::test(Opportunities\Create::class)
            ->set('form.status', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
        'in'       => ['in', 'Jo'],
    ]);

    test('amount', function ($rule, $value) {
        Livewire::test(Opportunities\Create::class)
            ->set('form.amount', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
    ]);
});

test('check if component is in the page', function () {
    Livewire::test(Opportunities\Index::class)
        ->assertContainsLivewireComponent('opportunities.create');
});
