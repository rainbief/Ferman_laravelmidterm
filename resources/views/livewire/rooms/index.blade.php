<?php

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $room_number = '';
    public int $floor = 1;
    public string $status = 'available';
    public ?string $description = null;
    public ?int $room_type_id = null;
    
    public ?int $editing = null;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?Room $roomToDelete = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        //
    }

    /**
     * Get statistics for the dashboard.
     */
    public function getTotalRoomsProperty(): int
    {
        return Room::count();
    }

    /**
     * Get total room types count.
     */
    public function getTotalRoomTypesProperty(): int
    {
        return RoomType::count();
    }

    /**
     * Get all rooms with their room types.
     */
    public function getRoomsProperty()
    {
        return Room::with('roomType')->latest()->get();
    }

    /**
     * Get all room types for the dropdown.
     */
    public function getRoomTypesProperty()
    {
        return RoomType::orderBy('name')->get();
    }

    /**
     * Store a new room.
     */
    public function store(): void
    {
        $validated = $this->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique(Room::class)],
            'floor' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['required', 'string', Rule::in(['available', 'occupied', 'maintenance'])],
            'description' => ['nullable', 'string'],
            'room_type_id' => ['nullable', 'exists:room_types,id'],
        ]);

        Room::create($validated);

        $this->reset(['room_number', 'floor', 'status', 'description', 'room_type_id']);
        $this->dispatch('room-created');
        
        session()->flash('message', 'Room created successfully.');
    }

    /**
     * Start editing a room.
     */
    public function edit(int $id): void
    {
        $room = Room::findOrFail($id);
        $this->editing = $room->id;
        $this->room_number = $room->room_number;
        $this->floor = $room->floor;
        $this->status = $room->status;
        $this->description = $room->description;
        $this->room_type_id = $room->room_type_id;
        $this->showEditModal = true;
    }

    /**
     * Update a room.
     */
    public function update(): void
    {
        if (!$this->editing) {
            return;
        }

        $validated = $this->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique(Room::class)->ignore($this->editing)],
            'floor' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['required', 'string', Rule::in(['available', 'occupied', 'maintenance'])],
            'description' => ['nullable', 'string'],
            'room_type_id' => ['nullable', 'exists:room_types,id'],
        ]);

        $room = Room::findOrFail($this->editing);
        $room->update($validated);

        $this->reset(['editing', 'room_number', 'floor', 'status', 'description', 'room_type_id', 'showEditModal']);
        $this->dispatch('room-updated');
        
        session()->flash('message', 'Room updated successfully.');
    }

    /**
     * Cancel editing.
     */
    public function cancelEdit(): void
    {
        $this->reset(['editing', 'room_number', 'floor', 'status', 'description', 'room_type_id', 'showEditModal']);
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(int $id): void
    {
        $this->roomToDelete = Room::findOrFail($id);
        $this->showDeleteModal = true;
    }

    /**
     * Delete a room.
     */
    public function delete(): void
    {
        if ($this->roomToDelete) {
            $this->roomToDelete->delete();
            $this->reset(['roomToDelete', 'showDeleteModal']);
            $this->dispatch('room-deleted');
            
            session()->flash('message', 'Room deleted successfully.');
        }
    }

    /**
     * Cancel delete.
     */
    public function cancelDelete(): void
    {
        $this->reset(['roomToDelete', 'showDeleteModal']);
    }
}; ?>

<x-layouts.app :title="__('Rooms')">
    <div>
        <div class="space-y-6">
            {{-- Statistics Cards --}}
            <div class="grid gap-4 md:grid-cols-3">
            <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $this->totalRooms }}</flux:heading>
                        <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Total Rooms') }}
                        </flux:text>
                    </div>
                    <flux:icon icon="home" class="h-12 w-12 text-blue-500 dark:text-blue-400" />
                </div>
            </flux:callout>

            <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $this->totalRoomTypes }}</flux:heading>
                        <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Total Room Types') }}
                        </flux:text>
                    </div>
                    <flux:icon icon="folder" class="h-12 w-12 text-green-500 dark:text-green-400" />
                </div>
            </flux:callout>

            <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ __('Hotel') }}</flux:heading>
                        <flux:text class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Management System') }}
                        </flux:text>
                    </div>
                    <flux:icon icon="building-office" class="h-12 w-12 text-purple-500 dark:text-purple-400" />
                </div>
            </flux:callout>
        </div>

        {{-- Success Message --}}
        @if (session('message'))
            <flux:callout variant="success" class="rounded-xl">
                {{ session('message') }}
            </flux:callout>
        @endif

        {{-- Add New Room Form --}}
        <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ __('Add New Room') }}</flux:heading>
            
            <form wire:submit="store" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="room_number" :label="__('Room Number')" placeholder="{{ __('Enter room number') }}" required />
                    <flux:input wire:model="floor" :label="__('Floor')" type="number" placeholder="{{ __('Enter floor number') }}" required />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <flux:select wire:model="status" :label="__('Status')" required>
                        <option value="available">{{ __('Available') }}</option>
                        <option value="occupied">{{ __('Occupied') }}</option>
                        <option value="maintenance">{{ __('Maintenance') }}</option>
                    </flux:select>
                    <flux:select wire:model="room_type_id" :label="__('Room Type')" placeholder="{{ __('Select a room type (optional)') }}">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($this->roomTypes as $roomType)
                            <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Enter room description (optional)') }}" rows="3" />

                <div class="flex justify-end">
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Add Room') }}</span>
                        <span wire:loading>{{ __('Adding...') }}</span>
                    </flux:button>
                </div>
            </form>
        </flux:callout>

        {{-- Rooms Table --}}
        <flux:callout class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ __('All Rooms') }}</flux:heading>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Room Number') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Floor') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Room Type') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Description') }}</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->rooms as $room)
                            <tr class="border-b border-neutral-200 dark:border-neutral-700">
                                <td class="px-4 py-3 text-sm font-semibold">{{ $room->room_number }}</td>
                                <td class="px-4 py-3 text-sm">{{ $room->floor }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($room->status === 'available')
                                        <flux:badge variant="success">{{ __('Available') }}</flux:badge>
                                    @elseif($room->status === 'occupied')
                                        <flux:badge variant="danger">{{ __('Occupied') }}</flux:badge>
                                    @else
                                        <flux:badge variant="warning">{{ __('Maintenance') }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <flux:badge variant="filled">{{ $room->roomType?->name ?? __('N/A') }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $room->description ? \Illuminate\Support\Str::limit($room->description, 50) : __('N/A') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button variant="filled" size="sm" wire:click="edit({{ $room->id }})">
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $room->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-neutral-500">
                                    {{ __('No rooms found. Add your first room above!') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:callout>
        </div>

        {{-- Edit Modal --}}
        <flux:modal name="edit-room-modal" wire:model="showEditModal" class="max-w-2xl">
        <form wire:submit="update" class="space-y-4">
            <flux:heading size="lg" class="mb-4">{{ __('Edit Room') }}</flux:heading>
            
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="room_number" :label="__('Room Number')" placeholder="{{ __('Enter room number') }}" required />
                <flux:input wire:model="floor" :label="__('Floor')" type="number" placeholder="{{ __('Enter floor number') }}" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model="status" :label="__('Status')" required>
                    <option value="available">{{ __('Available') }}</option>
                    <option value="occupied">{{ __('Occupied') }}</option>
                    <option value="maintenance">{{ __('Maintenance') }}</option>
                </flux:select>
                <flux:select wire:model="room_type_id" :label="__('Room Type')" placeholder="{{ __('Select a room type (optional)') }}">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($this->roomTypes as $roomType)
                        <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <flux:textarea wire:model="description" :label="__('Description')" placeholder="{{ __('Enter room description (optional)') }}" rows="3" />

            <div class="flex justify-end gap-2">
                <flux:button variant="filled" type="button" wire:click="cancelEdit">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Update Room') }}</span>
                    <span wire:loading>{{ __('Updating...') }}</span>
                </flux:button>
            </div>
        </form>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal name="delete-room-modal" wire:model="showDeleteModal" class="max-w-lg">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Delete Room') }}</flux:heading>
            
            <flux:text>
                {{ __('Are you sure you want to delete this room? This action cannot be undone.') }}
            </flux:text>

            @if ($roomToDelete)
                <flux:callout variant="danger" class="rounded-lg">
                    <div class="font-semibold">{{ __('Room') }} {{ $roomToDelete->room_number }}</div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400">
                        {{ __('Floor') }} {{ $roomToDelete->floor }}
                    </div>
                </flux:callout>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button variant="filled" type="button" wire:click="cancelDelete">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="danger" type="button" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete Room') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
            </div>
        </div>
        </flux:modal>
    </div>
</x-layouts.app>
