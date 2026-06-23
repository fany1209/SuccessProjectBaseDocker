{{--
Catalog
Agregar Product
Fecha de creación: 10-90-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 10-09-2025
--}}
<x-application.section-1>
    <x-wrapper-h-1>
        <p class="bg-purple-700 text-white p-1 rounded-md font-semibold tracking-[5px]">All products: {{ $total_products }}</p>
        @can('products.create')
        <x-button data-target="add-product" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Product</x-button>@include('catalog.modals.addProduct')
        @endcan
    </x-wrapper-h-1>
</x-application.section-1>