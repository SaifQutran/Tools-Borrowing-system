<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class IncreaseDecreaseAllowedTools extends Component
{
    public User $user;

    public int $allowedTools = 0;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->allowedTools = (int) $user->allowed_tools;
    }

    public function increase(): void
    {
        $this->allowedTools++;
        $this->saveAllowedTools();
    }

    public function decrease(): void
    {
        if ($this->allowedTools === 0) {
            return;
        }

        $this->allowedTools--;
        $this->saveAllowedTools();
    }

    private function saveAllowedTools(): void
    {
        $this->user->update([
            'allowed_tools' => $this->allowedTools,
        ]);
    }

    public function render()
    {
        return view('livewire.increase-decrease-allowed-tools');
    }
}
