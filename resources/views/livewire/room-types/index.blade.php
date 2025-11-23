<?php

use App\Models\RoomType;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public ?string $description = null;
    public float $price_per_night = 0;
    public int $max_occupancy = 1;
    
    public ?int $editing = null;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?RoomType $roomTypeToDelete = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        //
    }

    /**
     * Get all room types with room counts.
     */
    public function getRoomTypesProperty()
    {
        return RoomType::withCount('rooms')->orderBy('name')->get();
    }

    /**
     * Store a new room type.
     */
    public function store(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(RoomType::class)],
            'description' => ['nullable', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        RoomType::create($validated);

        $this->reset(['name', 'description', 'price_per_night', 'max_occupancy']);
        $this->dispatch('room-type-created');
        
        session()->flash('message', 'Room type created successfully.');
    }

    /**
     * Start editing a room type.
     */
    public function edit(int $id): void
    {
        $roomType = RoomType::findOrFail($id);
        $this->editing = $roomType->id;
        $this->name = $roomType->name;
        $this->description = $roomType->description;
        $this->price_per_night = $roomType->price_per_night;
        $this->max_occupancy = $roomType->max_occupancy;
        $this->showEditModal = true;
    }

    /**
     * Update a room type.
     */
    public function update(): void
    {
        if (!$this->editing) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(RoomType::class)->ignore($this->editing)],
            'description' => ['nullable', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $roomType = RoomType::findOrFail($this->editing);
        $roomType->update($validated);

        $this->reset(['editing', 'name', 'description', 'price_per_night', 'max_occupancy', 'showEditModal']);
        $this->dispatch('room-type-updated');
        
        session()->flash('message', 'Room type updated successfully.');
    }

    /**
     * Cancel editing.
     */
    public function cancelEdit(): void
    {
        $this->reset(['editing', 'name', 'description', 'price_per_night', 'max_occupancy', 'showEditModal']);
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(int $id): void
    {
        $this->roomTypeToDelete = RoomType::findOrFail($id);
        $this->showDeleteModal = true;
    }

    /**
     * Delete a room type.
     */
    public function delete(): void
    {
        if ($this->roomTypeToDelete) {
            $this->roomTypeToDelete->delete();
            $this->reset(['roomTypeToDelete', 'showDeleteModal']);
            $this->dispatch('room-type-deleted');
            
            session()->flash('message', 'Room type deleted successfully.');
        }
    }

    /**
     * Cancel delete.
     */
    public function cancelDelete(): void
    {
        $this->reset(['roomTypeToDelete', 'showDeleteModal']);
    }
}; ?>

<x-layouts.app :title="__('Room Types')">
    <div>
        <div class="space-y-6">
        {{-- Success Message --}}
        @if (session('message'))
            <flux:callout variant="success" class="rounded-xl">
                {{ session('message') }}
            </flux:callout>
        @endif

        {{-- Add New Room Type Form --}}
        <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ __('Add New Room Type') }}</flux:heading>
            
            <form wire:submit="store" class="space-y-4">
                <flux:input wire:model="name" :label="__('Room Type Name')" placeholder="{{ __('Enter room type name') }}" required />

                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="price_per_night" :label="__('Price Per Night')" type="number" step="0.01" placeholder="{{ __('Enter price per night') }}" required />
                    <flux:input wire:model="max_occupancy" :label="__('Max Occupancy')" type="number" placeholder="{{ __('Enter max occupancy') }}" required />
                </div>

                <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Enter room type description (optional)') }}" rows="3" />

                <div class="flex justify-end">
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Add Room Type') }}</span>
                        <span wire:loading>{{ __('Adding...') }}</span>
                    </flux:button>
                </div>
            </form>
        </flux:callout>

        {{-- Room Types Table --}}
        <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ __('All Room Types') }}</flux:heading>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Description') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Price/Night') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Max Occupancy') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Rooms Count') }}</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->roomTypes as $roomType)
                            <tr class="border-b border-neutral-200 dark:border-neutral-700">
                                <td class="px-4 py-3 text-sm font-semibold">{{ $roomType->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $roomType->description ?? __('N/A') }}</td>
                                <td class="px-4 py-3 text-sm">${{ number_format($roomType->price_per_night, 2) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $roomType->max_occupancy }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <flux:badge variant="filled">{{ $roomType->rooms_count }} {{ __('rooms') }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button variant="filled" size="sm" wire:click="edit({{ $roomType->id }})">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $roomType->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-neutral-500">
                                    {{ __('No room types found. Add your first room type above!') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:callout>
        </div>

        {{-- Edit Modal --}}
        <flux:modal name="edit-room-type-modal" wire:model="showEditModal" class="max-w-2xl">
        <form wire:submit="update" class="space-y-4">
            <flux:heading size="lg" class="mb-4">{{ __('Edit Room Type') }}</flux:heading>
            
            <flux:input wire:model="name" :label="__('Room Type Name')" placeholder="{{ __('Enter room type name') }}" required />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="price_per_night" :label="__('Price Per Night')" type="number" step="0.01" placeholder="{{ __('Enter price per night') }}" required />
                <flux:input wire:model="max_occupancy" :label="__('Max Occupancy')" type="number" placeholder="{{ __('Enter max occupancy') }}" required />
            </div>

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Enter room type description (optional)') }}" rows="3" />

            <div class="flex justify-end gap-2">
                <flux:button variant="filled" type="button" wire:click="cancelEdit">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Update Room Type') }}</span>
                    <span wire:loading>{{ __('Updating...') }}</span>
                </flux:button>
            </div>
        </form>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal name="delete-room-type-modal" wire:model="showDeleteModal" class="max-w-lg">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Delete Room Type') }}</flux:heading>
            
            <flux:text>
                {{ __('Are you sure you want to delete this room type? This action cannot be undone.') }}
            </flux:text>

            @if ($roomTypeToDelete)
                <flux:callout variant="danger" class="rounded-lg">
                    <div class="font-semibold">{{ $roomTypeToDelete->name }}</div>
                    @if ($roomTypeToDelete->rooms_count > 0)
                        <flux:text class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('This room type has :count rooms. They will have their room type set to null.', ['count' => $roomTypeToDelete->rooms_count]) }}
                        </flux:text>
                    @endif
                </flux:callout>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button variant="filled" type="button" wire:click="cancelDelete">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="danger" type="button" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete Room Type') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
            </div>
        </div>
        </flux:modal>
    </div>
</x-layouts.app>
