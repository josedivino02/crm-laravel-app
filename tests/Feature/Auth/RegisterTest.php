<?php

use App\Livewire\Auth\Register;
use App\Models\User;

use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Facades\{Event, Notification};
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

    expect(auth()->check())
        ->and(auth()->user())
        ->id->toBe(User::first()->id);
});

test('validation rules', function ($f) {
    if ($f->rule == 'unique') {
        User::factory()->create([$f->field => $f->value]);
    }

    $livewire = Livewire::test(Register::class)
        ->set($f->field, $f->value);

    if (property_exists($f, 'aValue')) {
        $livewire->set($f->aField, $f->value);
    }

    $livewire->call('submit')
        ->assertHasErrors([$f->field => $f->rule]);

})->with([
    'name::required'     => (object) ['field' => 'name', 'value' => '', 'rule' => 'required'],
    'name::max:255'      => (object) ['field' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max:255'],
    'email::required'    => (object) ['field' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email'       => (object) ['field' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::max:255'     => (object) ['field' => 'email', 'value' => str_repeat('*', 256) . '@divino.com', 'rule' => 'max:255'],
    'email::confirmed'   => (object) ['field' => 'email', 'value' => 'josedivino@divino.com', 'rule' => 'confirmed'],
    'email::unique'      => (object) ['field' => 'email', 'value' => 'josedivino@divino.com', 'rule' => 'unique', 'aField' => 'email_confirmation', 'aValue' => 'josedivino@divino.com'],
    'password::required' => (object) ['field' => 'password', 'value' => '', 'rule' => 'required'],
]);

it("should dispatch Registered event", function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('name', 'Jose Divino')
        ->set('email', 'josedivino@divino.com')
        ->set('email_confirmation', 'josedivino@divino.com')
        ->set('password', 'password')
        ->call('submit');

    Event::assertDispatched(Registered::class);
});
