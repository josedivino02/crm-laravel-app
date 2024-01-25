<?php

namespace App\Livewire\Dev;

use Illuminate\Support\Facades\Process;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BranchEnv extends Component
{
    #[Computed]
    public function env(): string
    {
        return config('app.env');
    }

    #[Computed]
    public function branch(): string
    {
        $process = Process::run('git branch --show-current');

        return trim($process->output()) ?: 'no branch';
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div class="flex items-center space-x-2">
                <x-badge :value="$this->branch"/>
                <x-badge :value="$this->env" />
            </div>
            BLADE;
    }
}
