<div class="w-full">
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        {{-- Header Judul --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                {{ static::$heading ?? 'Daftar Material (Google Sheet)' }}
            </h3>

            {{-- Tombol Refresh (kalau pakai Livewire) --}}
            @if(method_exists($this, 'refreshData'))
                <button
                    wire:click="refreshData"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition"
                >
                    <svg class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="refreshData" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0 1 0-14M19 5a9 9 0 0 1 0 14"/>
                    </svg>
                    <span wire:loading.remove wire:target="refreshData">Refresh</span>
                    <span wire:loading wire:target="refreshData">Loading...</span>
                </button>
            @endif
        </div>

        {{-- Error Message --}}
        @if(isset($error) && $error)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded">
                <strong>Error:</strong> {{ $error }}
            </div>
        @elseif(isset($data) && $data->isEmpty())
            <div class="p-4 text-sm text-gray-500 dark:text-gray-400">Tidak ada data.</div>
        @else
            {{-- Tabel Rapi --}}
            <div class="overflow-x-auto w-full">
                <table class="min-w-full border-collapse text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-800/70">
                        <tr class="text-left">
                            <th class="px-4 py-3 font-semibold w-[40%]">Material Name</th>
                            <th class="px-4 py-3 font-semibold text-center w-[15%]">PO PSN</th>
                            <th class="px-4 py-3 font-semibold text-center w-[15%]">PO OTHER</th>
                            <th class="px-4 py-3 font-semibold text-center w-[15%]">PO OTHER 2</th>
                            <th class="px-4 py-3 font-semibold text-center w-[15%]">Stock Mahaga</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($data as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $row['material_name'] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-center">{{ $row['po_psn'] ?? '0' }}</td>
                                <td class="px-4 py-2 text-center">{{ $row['po_other'] ?? '0' }}</td>
                                <td class="px-4 py-2 text-center">{{ $row['po_other_2'] ?? '0' }}</td>
                                <td class="px-4 py-2 text-center">{{ $row['stock_mahaga'] ?? '0' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
