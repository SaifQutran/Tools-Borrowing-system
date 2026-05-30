<?php

namespace App\Livewire;

use App\Models\Tool;
use Livewire\Component;

class ToolVisibility extends Component
{
    public Tool $tool;

    public function toggleStd()
    {
        $this->tool->seen_by_std = !$this->tool->seen_by_std;
        $this->tool->save();
    }

    public function toggleEmp()
    {
        $this->tool->seen_by_emp = !$this->tool->seen_by_emp;
        $this->tool->save();
    }

    public function render()
    {
        return view('livewire.tool-visibility');
    }
}
