<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;

use function Pest\Laravel\{assertDatabaseCount, assertDatabaseHas};

it('renders successfully', function () {
    Livewire::test(Register::class)
        ->assertStatus(200);
});

it("should render the component", function () {
    Livewire::test(Register::class)
        ->assertOk();
});

it("should be able to register a new user in  the system", function () {
    Livewire::test(Register::class)
        ->set('name', 'Jose Divino')
        ->set('email', 'josedivino@divino.com')
        ->set('email_confirmation', 'josedivino@divino.com')
        ->set('password', 'password')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas('users', [
        'name'  => 'Jose Divino',
        'email' => 'josedivino@divino.com',
    ]);

    assertDatabaseCount('users', 1);
});
