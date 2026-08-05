@props([
    'sidebar' => false,
])

@php
    $profile = \App\Models\FaskesProfile::first();
    $faskesName = $profile->nama_faskes ?? 'RME';
    $logoPath = $profile && $profile->logo_path ? asset('storage/' . $profile->logo_path) : null;
@endphp

@if($sidebar)
    <flux:sidebar.brand name="{{ $faskesName }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground {{ $logoPath ? 'bg-transparent' : '' }}">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $faskesName }}" class="h-full w-full object-contain rounded-md" />
            @else
                <x-app-logo-icon class="size-5 fill-current text-white" />
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $faskesName }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground {{ $logoPath ? 'bg-transparent' : '' }}">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $faskesName }}" class="h-full w-full object-contain rounded-md" />
            @else
                <x-app-logo-icon class="size-5 fill-current text-white" />
            @endif
        </x-slot>
    </flux:brand>
@endif
