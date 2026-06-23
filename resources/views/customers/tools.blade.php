{{--
Customers
Agregar Customer
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
--}}
<x-section-1>
    <x-wrapper-h-1>
        <p class="bg-purple-700 text-white p-1 rounded-md font-semibold tracking-[5px]">All customers: {{ $total_customers }}</p>
        @can('customers.create')
        <x-button data-target="add-customer" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Customer</x-button>@include('customers.modals.addCustomer')
        @endcan
    </x-wrapper-h-1>
</x-section-1>