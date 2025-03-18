<div>
    <select wire:model="selectedPlanId" wire:change="changePlan">
        <option value="">Selecciona un plan</option>
        @foreach($plans as $plan)
            <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
        @endforeach
    </select>
</div>