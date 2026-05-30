<div>
    <span wire:click="toggleStd" 
          style="cursor: pointer;"
          class="p-2 badge {{ $tool->seen_by_std ? 'badge-success' : 'badge-danger' }} rounded-circle"
          title="{{ $tool->seen_by_std ? 'إخفاء عن الطلاب' : 'إظهار للطلاب' }}">
        الطلاب
        
    </span>

    <span wire:click="toggleEmp" 
          style="cursor: pointer;"
          class="p-2 badge {{ $tool->seen_by_emp ? 'badge-success' : 'badge-danger' }} rounded-circle"
          title="{{ $tool->seen_by_emp ? 'إخفاء عن الموظفين' : 'إظهار للموظفين' }}">
        الموظفين

    </span>
</div>
