<?php

use App\Livewire\Opportunities;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;use Livewire\Livewire;

beforeEach(function () {
    actingAs(User::factory()->Update());
    $this->opportunity = Opportunity::factory()->create();
});

it("should be able to update a opportunity", function () {
    $customer = Customer::factory()->create();

    Livewire::test(Opportunities\Update::class)
        ->call('load', $this->opportunity->id)
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
        'id' => $this->opportunity->id,
        'customer_id' => $customer->id,
        'title' => 'Divino',
        'status' => 'won',
        'amount' => '12345',
    ]);
});

describe('validations', function () {
    test('title', function ($rule, $value) {
        Livewire::test(Opportunities\Update::class)
            ->call('load', $this->opportunity->id)
            ->set('form.title', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
        'min' => ['min', 'Jo'],
        'max' => ['max', str_repeat('a', 256)],
    ]);

    test('status', function ($rule, $value) {
        Livewire::test(Opportunities\Update::class)
            ->call('load', $this->opportunity->id)
            ->set('form.status', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
        'in' => ['in', 'Josedivino'],
    ]);

    test('amount', function ($rule, $value) {
        Livewire::test(Opportunities\Update::class)
            ->call('load', $this->opportunity->id)
            ->set('form.amount', $value)
            ->call('save')
            ->assertHasErrors(['title' => $rule]);
    })->with([
        'required' => ['required', ''],
    ]);
});

test('check if component is in the page', function () {
    Livewire::test(Opportunities\Index::class)
        ->assertContainsLivewireComponent('opportunities.update');
});