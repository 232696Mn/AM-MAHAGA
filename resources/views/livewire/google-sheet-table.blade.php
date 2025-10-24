<div class="p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Daftar Material</h3>
        <div class="flex items-center gap-2">
            <button wire:click="refreshData" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0 1 0-14M19 5a9 9 0 0 1 0 14" /></svg>
                Refresh
            </button>
        </div>
    </div>

    @if($loading)
        <div class="p-6 text-center text-sm text-gray-500">Memuat data...</div>
    @elseif($error)
        <div class="p-4 bg-red-50 text-red-700 rounded">
            <strong>Error:</strong> {{ $error }}
        </div>
    @elseif($rows->isEmpty())
        <div class="p-4 text-sm text-gray-600">Tidak ada data.</div>
    @else
        <div class="overflow-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium">Material Name</th>
                        <th class="px-3 py-2 text-right font-medium">PO PSN</th>
                        <th class="px-3 py-2 text-right font-medium">PO OTHER</th>
                        <th class="px-3 py-2 text-right font-medium">PO OTHER 2</th>
                        <th class="px-3 py-2 text-right font-medium">Stock Mahaga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rows as $row)
                        <tr>
                            <td class="px-3 py-2">{{ $row['material_name'] ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['po_psn'] ?? '0' }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['po_other'] ?? '0' }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['po_other_2'] ?? '0' }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['stock_mahaga'] ?? '0' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
