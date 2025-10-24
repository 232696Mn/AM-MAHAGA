<x-filament::page>
    <div class="space-y-6">

        {{-- Widget 2: Full width di atas --}}
        <div class="w-full">
            @livewire(\App\Filament\Widgets\GoogleSheetStats::class)
        </div>

        {{-- Widget 1 & 3: Dua kolom sejajar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Kiri --}}
            <div>
                @livewire(\App\Filament\Widgets\GoogleSheetMaterialStats::class)
            </div>

            {{-- Kanan --}}
            <div>
                @livewire(\App\Filament\Widgets\GoogleSheetViewWidget::class)
            </div>
        </div>

    </div>
</x-filament::page>
