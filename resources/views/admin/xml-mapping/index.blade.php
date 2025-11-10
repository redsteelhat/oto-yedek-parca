@extends('layouts.admin')

@section('title', 'XML Mapping - ' . $supplier->name)
@section('page-title', 'XML Mapping: ' . $supplier->name)

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="mb-4">
        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="text-primary-600 hover:text-primary-800 underline">
            ← Tedarikçiye Dön
        </a>
    </div>

    <h2 class="text-xl font-bold mb-4">XML Alan Eşleştirme</h2>

    <!-- Current Mappings -->
    @if($supplier->xmlMappings->count() > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Mevcut Eşleştirmeler</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">XML Alanı</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yerel Alan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transform</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zorunlu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıra</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($supplier->xmlMappings->sortBy('sort_order') as $mapping)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono">{{ $mapping->xml_field }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mapping->local_field }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mapping->transform_rule ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($mapping->is_required)
                                        <span class="text-red-600">✓</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mapping->sort_order }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Mapping Form -->
    <form id="mappingForm" action="{{ route('admin.xml-mapping.store', $supplier) }}" method="POST">
        @csrf

        <div id="mappings-container">
            <!-- Existing mappings -->
            @if($supplier->xmlMappings->count() > 0)
                @foreach($supplier->xmlMappings->sortBy('sort_order') as $index => $mapping)
                    <div class="mapping-row bg-gray-50 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">XML Alanı *</label>
                                <select name="mappings[{{ $index }}][xml_field]" required class="w-full border rounded px-3 py-2 xml-field-select">
                                    <option value="">Seçiniz</option>
                                    @foreach($xmlFields as $field)
                                        <option value="{{ $field }}" {{ $mapping->xml_field == $field ? 'selected' : '' }}>{{ $field }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Yerel Alan *</label>
                                <select name="mappings[{{ $index }}][local_field]" required class="w-full border rounded px-3 py-2">
                                    <option value="">Seçiniz</option>
                                    @foreach($localFields as $key => $label)
                                        <option value="{{ $key }}" {{ $mapping->local_field == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Transform Kuralı</label>
                                <select name="mappings[{{ $index }}][transform_rule]" class="w-full border rounded px-3 py-2">
                                    <option value="">Yok</option>
                                    <option value="trim" {{ $mapping->transform_rule == 'trim' ? 'selected' : '' }}>Trim</option>
                                    <option value="uppercase" {{ $mapping->transform_rule == 'uppercase' ? 'selected' : '' }}>Uppercase</option>
                                    <option value="lowercase" {{ $mapping->transform_rule == 'lowercase' ? 'selected' : '' }}>Lowercase</option>
                                    <option value="float" {{ $mapping->transform_rule == 'float' ? 'selected' : '' }}>Float</option>
                                    <option value="int" {{ $mapping->transform_rule == 'int' ? 'selected' : '' }}>Integer</option>
                                    <option value="slug" {{ $mapping->transform_rule == 'slug' ? 'selected' : '' }}>Slug</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sıra</label>
                                <input type="number" name="mappings[{{ $index }}][sort_order]" value="{{ $mapping->sort_order ?? $index }}" min="0" class="w-full border rounded px-3 py-2">
                            </div>
                            
                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input type="checkbox" name="mappings[{{ $index }}][is_required]" value="1" {{ $mapping->is_required ? 'checked' : '' }} class="mr-2">
                                    <span class="text-sm text-gray-700">Zorunlu</span>
                                </label>
                                <button type="button" class="ml-auto text-red-600 hover:text-red-800 remove-mapping" onclick="removeMapping(this)">
                                    Sil
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="mt-4 flex justify-between">
            <button type="button" id="addMapping" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                + Yeni Eşleştirme Ekle
            </button>
            
            <div class="space-x-2">
                <button type="button" id="testMapping" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Test Et
                </button>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Kaydet
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Sample XML Display -->
@if($sampleXml)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Örnek XML Yapısı</h3>
        <pre class="bg-gray-100 p-4 rounded overflow-x-auto text-xs">{{ htmlspecialchars($sampleXml->asXML()) }}</pre>
    </div>
@endif

<!-- Test Result Modal -->
<div id="testModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Mapping Test Sonucu</h3>
            <button onclick="closeTestModal()" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div id="testResult" class="mt-4"></div>
    </div>
</div>

@push('scripts')
<script>
let mappingIndex = {{ $supplier->xmlMappings->count() }};

document.getElementById('addMapping').addEventListener('click', function() {
    const container = document.getElementById('mappings-container');
    const row = document.createElement('div');
    row.className = 'mapping-row bg-gray-50 rounded-lg p-4 mb-4';
    row.setAttribute('data-index', mappingIndex);
    
    const xmlFieldsOptions = @json($xmlFields).map(field => `<option value="${field}">${field}</option>`).join('');
    const localFieldsOptions = @json($localFields).map((label, key) => `<option value="${key}">${label}</option>`).join('');
    
    row.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XML Alanı *</label>
                <select name="mappings[${mappingIndex}][xml_field]" required class="w-full border rounded px-3 py-2 xml-field-select">
                    <option value="">Seçiniz</option>
                    ${xmlFieldsOptions}
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Yerel Alan *</label>
                <select name="mappings[${mappingIndex}][local_field]" required class="w-full border rounded px-3 py-2">
                    <option value="">Seçiniz</option>
                    ${localFieldsOptions}
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Transform Kuralı</label>
                <select name="mappings[${mappingIndex}][transform_rule]" class="w-full border rounded px-3 py-2">
                    <option value="">Yok</option>
                    <option value="trim">Trim</option>
                    <option value="uppercase">Uppercase</option>
                    <option value="lowercase">Lowercase</option>
                    <option value="float">Float</option>
                    <option value="int">Integer</option>
                    <option value="slug">Slug</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıra</label>
                <input type="number" name="mappings[${mappingIndex}][sort_order]" value="${mappingIndex}" min="0" class="w-full border rounded px-3 py-2">
            </div>
            
            <div class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" name="mappings[${mappingIndex}][is_required]" value="1" class="mr-2">
                    <span class="text-sm text-gray-700">Zorunlu</span>
                </label>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800 remove-mapping" onclick="removeMapping(this)">
                    Sil
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(row);
    mappingIndex++;
});

function removeMapping(button) {
    button.closest('.mapping-row').remove();
}

document.getElementById('testMapping').addEventListener('click', async function() {
    const form = document.getElementById('mappingForm');
    const formData = new FormData(form);
    
    // Get mappings data
    const mappings = [];
    document.querySelectorAll('.mapping-row').forEach((row, index) => {
        const xmlField = row.querySelector('[name*="[xml_field]"]').value;
        const localField = row.querySelector('[name*="[local_field]"]').value;
        const transformRule = row.querySelector('[name*="[transform_rule]"]').value;
        const isRequired = row.querySelector('[name*="[is_required]"]').checked;
        
        if (xmlField && localField) {
            mappings.push({
                xml_field: xmlField,
                local_field: localField,
                transform_rule: transformRule || null,
                is_required: isRequired
            });
        }
    });
    
    try {
        const response = await fetch('{{ route("admin.xml-mapping.test", $supplier) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                xml_url: '{{ $supplier->xml_url }}',
                mappings: mappings
            })
        });
        
        const result = await response.json();
        
        const modal = document.getElementById('testModal');
        const resultDiv = document.getElementById('testResult');
        
        if (result.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded p-4 mb-4">
                    <p class="text-green-800 font-semibold">✓ Test Başarılı!</p>
                </div>
                <div class="bg-gray-50 rounded p-4">
                    <h4 class="font-semibold mb-2">Eşleştirilmiş Veri:</h4>
                    <pre class="text-xs overflow-x-auto">${JSON.stringify(result.data, null, 2)}</pre>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded p-4">
                    <p class="text-red-800 font-semibold">✗ Test Başarısız</p>
                    <p class="text-red-600 mt-2">${result.message}</p>
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
    } catch (error) {
        alert('Test sırasında bir hata oluştu: ' + error.message);
    }
});

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}
</script>
@endpush
@endsection

