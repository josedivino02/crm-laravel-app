<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Customer $customer;

    public bool $modal = false;

    public Collection|array $customers = [];

    public function render(): View
    {
        return view('livewire.customers.update');
    }

    #[On('customer::update')]
    public function load(int $id): void
    {
        $customer = Customer::find($id);
        $this->form->setCustomer($customer);

        $this->form->resetErrorBag();
        $this->search();
        $this->modal = true;
    }

    public function save(): void
    {
        $this->form->update();

        $this->modal = false;
        $this->dispatch("customer::reload")->to("customers.index");
    }

    public function search(string $value = '')
    {
        $this->customers = Customer::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->merge(
                Customer::query()->whereId($this->form->customer_id)->get(['id', 'name'])
            );
    }
}