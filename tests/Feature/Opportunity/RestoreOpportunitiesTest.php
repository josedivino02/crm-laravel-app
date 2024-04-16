<?php

use App\Livewire\Opportunities;
use App\Models\Opportunity;
use function Pest\Laravel\assertNotSoftDeleted;
use Livewire\Livewire;

it("should be able to restore a customer", function () {
    $opportunity = Opportunity::factory()->deleted()->create();

    Livewire::test(Opportunities\Restore::class)
        ->set('opportunity', $opportunity)
        ->call('restore');

    assertNotSoftDeleted('opportunity', [
        'id' => $opportunity->id,
    ]);
});

test("when confirming we should load the opportunity and set modal to true", function () {
    $opportunity = Opportunity::factory()->deleted()->create();

    Livewire::test(Opportunities\Restore::class)
        ->call('confirmAction', $opportunity->id)
        ->assertSet('opportunity.id', $opportunity->id)
        ->assertSet('modal', true);

});

test("after restoring we should disparch an event to tell the list to reload", function () {
    $opportunity = Opportunity::factory()->deleted()->create();

    Livewire::test(Opportunities\Restore::class)
        ->set('opportunity', $opportunity)
        ->call('restore')
        ->assertDispatched('Opportunity::reload');

});

test("after restoring we should close the modal", function () {
    $opportunity = Opportunity::factory()->deleted()->create();

    Livewire::test(Opportunities\Restore::class)
        ->set('opportunity', $opportunity)
        ->call('restore')
        ->assertSet('modal', false);

});