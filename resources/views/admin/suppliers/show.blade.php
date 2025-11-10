@extends('layouts.admin')

@section('title', 'Tedarikçi Detayı: ' . $supplier->name)
@section('page-title', 'Tedarikçi Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tedarikçi Bilgileri -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Tedarikçi Bilgileri</h2>
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Düzenle
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Tedarikçi Adı</p>
                <p class="font-semibold text-lg">{{ $supplier->name }}</p>
            </div>

            @if($supplier->code)
                <div>
                    <p class="text-sm text-gray-600">Kod</p>
                    <p class="font-semibold font-mono">{{ $supplier->code }}</p>
                </div>
            @endif

            @if($supplier->xml_url)
                <div>
                    <p class="text-sm text-gray-600">XML URL</p>
                    <a href="{{ $supplier->xml_url }}" target="_blank" class="font-semibold text-primary-600 hover:text-primary-800">
                        {{ $supplier->xml_url }}
                    </a>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">XML Tipi</p>
                <p class="font-semibold">{{ ucfirst($supplier->xml_type) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Güncelleme Sıklığı</p>
                <p class="font-semibold">{{ $supplier->update_frequency }} günde bir</p>
            </div>

            @if($supplier->last_import_at)
                <div>
                    <p class="text-sm text-gray-600">Son Import</p>
                    <p class="font-semibold">{{ $supplier->last_import_at->format('d.m.Y H:i') }}</p>
                </div>
            @endif

            <div>
                <p class="text-sm text-gray-600">Durum</p>
                @if($supplier->is_active)
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                @endif
            </div>

            @if($supplier->notes)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Notlar</p>
                    <p class="text-gray-900">{{ $supplier->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">İstatistikler</h2>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Ürün Sayısı</p>
                <p class="font-bold text-2xl text-primary-600">{{ $supplier->products->count() ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">XML Mapping Sayısı</p>
                <p class="font-bold text-2xl text-blue-600">{{ $supplier->xmlMappings->count() ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Import Log Sayısı</p>
                <p class="font-bold text-2xl text-green-600">{{ $supplier->importLogs->count() ?? 0 }}</p>
            </div>
        </div>

        <div class="mt-6 space-y-2">
            <a href="{{ route('admin.xml-mapping.index', $supplier) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
                XML Mapping Yönet
            </a>
            <form action="{{ route('admin.suppliers.import', $supplier) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    XML Import Başlat
                </button>
            </form>
            <div id="importProgress" class="hidden mt-4">
                <div class="bg-gray-200 rounded-full h-4 mb-2">
                    <div id="progressBar" class="bg-green-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div id="progressText" class="text-sm text-gray-600 text-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- XML Mappings -->
@if($supplier->xmlMappings->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">XML Mappings</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">XML Field</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Local Field</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transform Rule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zorunlu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplier->xmlMappings as $mapping)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mapping->xml_field }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mapping->local_field }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $mapping->transform_rule ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($mapping->is_required)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Evet</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Hayır</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Import Logs -->
@if($supplier->importLogs->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Import Logları</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İçe Aktarılan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Güncellenen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başarısız</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplier->importLogs->take(10) as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($log->status == 'completed') bg-green-100 text-green-800
                                    @elseif($log->status == 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->total_items ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $log->imported_items ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">{{ $log->updated_items ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $log->failed_items ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@push('scripts')
<script>
// Auto-refresh import progress if import is running
@if($supplier->latestImportLog && in_array($supplier->latestImportLog->status, ['processing', 'pending']))
    let progressInterval = setInterval(async function() {
        try {
            const response = await fetch('{{ route("admin.suppliers.import-progress", $supplier) }}');
            const data = await response.json();
            
            if (data.status === 'processing' || data.status === 'pending') {
                const progressDiv = document.getElementById('importProgress');
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                
                if (progressDiv) {
                    progressDiv.classList.remove('hidden');
                }
                
                if (progressBar && data.percentage !== undefined) {
                    progressBar.style.width = data.percentage + '%';
                }
                
                if (progressText && data.progress) {
                    progressText.textContent = `İşleniyor: ${data.progress.processed} / ${data.progress.total} (${data.percentage}%)`;
                }
            } else {
                clearInterval(progressInterval);
                location.reload(); // Reload page when import completes
            }
        } catch (error) {
            console.error('Progress fetch error:', error);
        }
    }, 2000); // Check every 2 seconds
@endif
</script>
@endpush
@endsection

