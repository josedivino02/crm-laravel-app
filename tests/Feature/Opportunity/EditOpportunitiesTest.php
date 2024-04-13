<?php

use App\Livewire\Opportunities;
use App\Models\Opportunity;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;use Livewire\Livewire;

beforeEach(function () {
    actingAs(User::factory()->Update());
    $this->opportunity = Opportunity::factory()->create();
});

it("should be able to update a customer", function () {
    Livewire::test(Opportunities\Create::class)
        ->call('load', $this->opportunity->id)
        ->set('form.title', 'Divino')
        ->assertPropertyWired('form.title')
        ->set('form.status', 'won')
        ->assertPropertyWired('form.status')
        ->set('form.amount', '123456789')
        ->assertPropertyWired('form.amount')
        ->call('save')
        ->assertMethodWiredToForm('save')
        ->assertHasNoErrors();

    assertDatabaseHas('opportunities', [
        'title' => 'Divino',
        'status' => 'won',
        'amount' => '123456789',
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
        'in' => ['in', 'Jo'],
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

test("check if component is in the page", function () {
    Livewire::test(Opportunities\Index::class)
        ->assertContainsLivewireComponent('opportunity.update');
});