<?php

use App\Livewire\Admin;
use App\Models\User;
use App\Notifications\UserRestoredAccessNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\{actingAs, assertNotSoftDeleted, assertSoftDeleted, assertSoftRestored};

it("should be able to Restore a user", function () {
    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);

    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->set('confirmation_confirmation', 'CONFIRMAR R')
        ->call('restore')
        ->assertDispatched('user::restored');

    assertNotSoftDeleted('users', [
        'id' => $forRestoration->id,
    ]);
});

it("should have a confirmation before deletion", function () {

    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);

    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->call('restore')
        ->assertHasErrors(['confirmation' => 'confirmed'])
        ->assertNotDispatched('user::restored');

    assertSoftDeleted('users', [
        'id' => $forRestoration->id,
    ]);
});

it("should send a notification to the user telling him that he has again access to the application", function () {
    Notification::fake();

    $user           = User::factory()->admin()->create();
    $forRestoration = User::factory()->deleted()->create();

    actingAs($user);

    Livewire::test(Admin\Users\Restore::class)
        ->set('user', $forRestoration)
        ->set('confirmation_confirmation', 'CONFIRMAR R')
        ->call('restore');

    Notification::assertSentTo($forRestoration, UserRestoredAccessNotification::class);
});
