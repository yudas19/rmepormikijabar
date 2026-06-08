<div class="py-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm">
        <div class="mb-6 flex items-start justify-between">
            <div>
                <a href="{{ route('master.index') }}" wire:navigate class="inline-flex items-center text-xs font-semibold text-zinc-500 hover:text-indigo-600 transition mb-3">
                    <flux:icon.chevron-left class="w-3 h-3 mr-1" /> Kembali ke Dashboard Master
                </a>
                <flux:heading size="xl">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading class="mt-1">{{ $subheading ?? '' }}</flux:subheading>
            </div>
            @if (isset($actions))
                <div>
                    {{ $actions }}
                </div>
            @endif
        </div>

        <div class="w-full">
            {{ $slot }}
        </div>
    </div>
</div>
