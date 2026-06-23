<x-modal id="certificadoProveedor">
    <form action="{{ route('supplier_certificates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Proveedor</label>
            <select name="supplier_id" id="supplier_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                <option value="">-- Seleccione un proveedor --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="product_id" class="block text-sm font-medium text-gray-700">Producto</label>
            <select name="product_id" id="product_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                <option value="">-- Seleccione un producto --</option>
                @foreach($products as $product)
                    <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="file" class="block text-sm font-medium text-gray-700">Certificado (PDF)</label>
            <input type="file" name="file" id="file" accept="application/pdf" 
                class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>

        <div class="flex justify-end space-x-2">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2">Save</x-button>
        </div>
    </form>
</x-modal>
