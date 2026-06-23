@php
    $imgCls  = 'h-16 w-16 bg-green-400 rounded-md p-1 object-contain pointer-events-none select-none me-2';
    $economic_factors = [
        (object)[ "criteria" => "Precio del producto", "description" => "El proveedor ofrece varios precios del producto; por ejemplo si ... que otorgan la competencia a comparación del proveedor actua"],
        (object)[ "criteria" => "Descuentos", "description" => "El proveedor ofrece disponibilidad de descuentos acumulativos en... servicios especiales para clientes habituales y ofertas de productos."],
        (object)[ "criteria" => "Seguros", "description" => "El proveedor ofrece en caso de perdida o daño parcial o total de la mercancía, la reposición de esta."],
        (object)[ "criteria" => "Formas", "description" => "El proveedor ofrece distintas formas de pago como transferencias, efectivo, cheques, etc."],
        (object)[ "criteria" => "Crédito", "description" => "El proveedor ofrece plazos de pago"],
        (object)[ "criteria" => "Facturación", "description" => "El proveedor ofrece facturación rápida y sin complicaciones"],
        (object)[ "criteria" => "Pedido mínimo", "description" => "El proveedor negocia la cantidad a consumir o fabricar de acuerdo a nuestras necesidades"],
    ];
    $quality_factors = [
        (object)[ "criteria" => "Calidad de los productos", 'description' => "El proveedor ofrece la calidad que se requiere o mas en los productos solicitados"],
        (object)[ "criteria" => "Garantías", 'description' => "El proveedor ofrece el tiempo de garantía en los productos "],
        (object)[ "criteria" => "Servicio de atención técnica", 'description' => "El proveedor brinda asesoría técnica en la entrega del producto"],
        (object)[ "criteria" => "Servicio de atención al cliente", 'description' => "El proveedor nos ofrece un servicio o producto con atención adecuada a nuestra necesidad"],
        (object)[ "criteria" => "Asesoramiento técnico", 'description' => "El proveedor ofrece asistencia técnica posterior a la compra para aclaración de dudas y/o recomendaciones"],
    ];
    $other_factors = [
        (object)[ "criteria" => "Plazo de entrega ", 'description' => "El proveedor entrega en tiempo de acuerdo a lo solicitado "],
        (object)[ "criteria" => "Devoluciones", 'description' => "El proveedor acepta la devolución de producto por que no cumple con las especificaciones, por defecto, por violación del sello de garantía y en servicio por que no cumplió la necesidad, por falta de asesoría técnica, deficiencia en el servicio en general."],
        (object)[ "criteria" => "Capacidad", 'description' => "El proveedor tiene solvencia de producción o distribución del producto o servicio requerido"],
        (object)[ "criteria" => "Cantidades mínimas que fabrica", 'description' => "El proveedor tiene flexibilidad en cuanto a la venta por la cantidad de fabricación y pueden llegar a un acuerdo"],
        (object)[ "criteria" => "Surtimiento de cantidades solicitadas", 'description' => "El proveedor surte en cantidades solicitadas y no manda excesos"],
        (object)[ "criteria" => "Sectores con los que tiene experiencia", 'description' => "El proveedor tiene experiencia de trabajar con los sectores del mismo rubro"],
        (object)[ "criteria" => "Referencias comerciales", 'description' => "Esto brindará confianza y credibilidad en cuanto a los precios, procesos logísticos y materias primas."],
        (object)[ "criteria" => "Ubicación", 'description' => "Considera aspectos como los tiempos de desplazamiento, los posibles retrasos, la flexibilidad en las entregas, etc."],
        (object)[ "criteria" => "Tamaño de la empresa", 'description' => "El proveedor es una empresa seria por micro que sea"],
        (object)[ "criteria" => "Proveedor único", 'description' => "No existe otro proveedor para este producto"],
        (object)[ "criteria" => "Capacitación", 'description' => "Comparte los conocimientos necesarios de los productos que ofrece"],
        (object)[ "criteria" => "Certificaciones", 'description' => "Tipos de certificaciones que tiene"],
        (object)[ "criteria" => "Tecnología", 'description' => "Aporta innovaciones a los clientes"],
        (object)[ "criteria" => "Seguridad", 'description' => "Ofrece un seguro contra robo de mercancía, así como demostrar protección contra los fraudes a través de certificaciones."],
    ];
@endphp
<x-modal id="g-supp-select-crit">
    <form class="flex flex-col items-center w-full gap-2" action="{{ route('suppliers.gSupSelectCrit') }}" method="POST" id="supp-select-crit-form" target="_blank">
        @csrf
        <x-wrapper-form-1>
            <img src="{{ asset('images/purchases/02.png') }}" alt="Supplier selection criteria image" class="{{ $imgCls }}">
            <x-tittle-form class="border-b-2 border-green-700 pb-2">Supplier selection criteria</x-tittle-form>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="supplier">Supplier</x-label>
                <x-input-1 required name="supplier"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="date">Date</x-label>
                <x-input-1 required type="date" name="date"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="address">Address</x-label>
                <x-input-1 required name="address"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="products">Products</x-label>
                <x-textarea-1 required name="products" placeholder="Please, write yours items"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-2>
            <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Economic Factors</span>
            <div class="accordion-body w-full hidden">
                @foreach ($economic_factors as $index => $item)
                <x-wrapper-form-1>
                    <div class="flex-1 p-1">
                        <p class="text-md text-gray-700">{{ $item->criteria }}</p>
                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                    </div>
                    <div class="flex-1 p-1">
                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label>Yes</x-label>
                                <x-radio-1 required name="e-{{ $index }}" value="1"></x-radio-1>
                            </x-wrapper-form-2>
                            <x-wrapper-form-2>
                                <x-label>No</x-label>
                                <x-radio-1 required name="e-{{ $index }}" value="0"></x-radio-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>
                    </div>
                </x-wrapper-form-1>
                @endforeach
            </div>
        </x-wrapper-form-2>
        <x-wrapper-form-2>
            <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Quality Factors</span>
            <div class="accordion-body w-full hidden">
                @foreach ($quality_factors as $index => $item)
                <x-wrapper-form-1>
                    <div class="flex-1 p-1">
                        <p class="text-md text-gray-700">{{ $item->criteria }}</p>
                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                    </div>
                    <div class="flex-1 p-1">
                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label>Yes</x-label>
                                <x-radio-1 required name="q-{{ $index }}" value="1"></x-radio-1>
                            </x-wrapper-form-2>
                            <x-wrapper-form-2>
                                <x-label>No</x-label>
                                <x-radio-1 required name="q-{{ $index }}" value="0"></x-radio-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>
                    </div>
                </x-wrapper-form-1>
                @endforeach
            </div>
        </x-wrapper-form-2>    
        <x-wrapper-form-2>
            <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Other Factors</span>
            <div class="accordion-body w-full hidden">
                @foreach ($other_factors as $index => $item)
                <x-wrapper-form-1>
                    <div class="flex-1 p-1">
                        <p class="text-md text-gray-700">{{ $item->criteria }}</p>
                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                    </div>
                    <div class="flex-1 p-1">
                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label>Yes</x-label>
                                <x-radio-1 required name="o-{{ $index }}" value="1"></x-radio-1>
                            </x-wrapper-form-2>
                            <x-wrapper-form-2>
                                <x-label>No</x-label>
                                <x-radio-1 required name="o-{{ $index }}" value="0"></x-radio-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>
                    </div>
                </x-wrapper-form-1>
                @endforeach
            </div>
        </x-wrapper-form-2>
        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Close</x-button-1>
            <x-button-1 colorBtn="orange">Generate pdf</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#g-supp-select-crit');
    function reactiveSupSelectCritForm(){
        father.on('click','.accordion-header', function () {
            const $body = $(this).next('.accordion-body');
            father.find('.accordion-body').not($body).slideUp();
            $body.slideToggle();
        });
    }
    reactiveSupSelectCritForm();
});
</script>    
@endpush