<x-filament::page>
    <div class="space-y-6">
        <div class="w-full">
            @livewire(\App\Filament\Widgets\GoogleSheetStats::class)
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @livewire(\App\Filament\Widgets\GoogleSheetViewWidget::class)
            </div>

            <div>
                <div class="grid grid-cols-2 gap-2">
                    @livewire(\App\Filament\Widgets\GoogleSheetMaterialStats::class)
                    @livewire(\App\Filament\Widgets\GoogleSheetMaterialKekuranganStats::class)
                </div>
            </div>
        </div>

        <div class="w-full">
            @livewire(\App\Filament\Widgets\GoogleSheetLineStats::class)
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @livewire(\App\Filament\Widgets\FinishingMillitaryBandBarChart::class)
            </div>

            <div>
                @livewire(\App\Filament\Widgets\FinishingStandardBandLineChart::class)
            </div>
        </div>
    </div>
</x-filament::page>
