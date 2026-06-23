@php
    $imgCls  = 'h-16 w-16 bg-green-400 rounded-md p-1 object-contain pointer-events-none select-none me-2';
    $questions = [
        (object)[ 
            "question" => "Cumplimiento en producto (Entrega de producto en tiempo)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Cumple siempre o entrega antes de lo pactado",
                "2" => "Incumple eventualmente",
                "3" => "Incumple permanentemente"
            ]
        ],
        (object)[ 
            "question" => "Entrega de producto en cantidad", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las cantidades pedidas ocomprometidas",
                "2" => "Algunas veces no cumple con las cantidades pedidas ocomprometidas",
                "3" => "Generalmente incumple con las cantidades pedidas o comprometidas"
            ]
        ],
        (object)[ 
            "question" => "Cumplimiento en servicios (Entrega de servicios en tiempo)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Cumple siempre o entrega antes de lo pactado",
                "2" => "Incumple eventualmente",
                "3" => "Incumple permanentemente"
            ]
        ],
        (object)[ 
            "question" => "Entrega de servicio en cantidad y forma", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las cantidades pedidas o comprometidas",
                "2" => "Algunas veces no cumple con las cantidades pedidas o comprometidas",
                "3" => "Generalmente incumple con las cantidades pedidas o comprometidas"
            ]
        ],
        (object)[ 
            "question" => "Calidad (Conformidad)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las especificaciones del producto o servicio prestado",
                "2" => "Algunas veces cumple con la calidad del producto o servicio prestado",
                "3" => "La mayoría de las veces no cumple con la calidad del producto o servicio prestado"
            ]
        ],
        (object)[ 
            "question" => "Capacidad de respuesta", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Atiende compras urgentes de forma inmediata",
                "2" => "La capacidad para cumplir urgencias no es la suficiente",
                "3" => "No tiene la capacidad para cubrir urgencias"
            ]
        ],
        (object)[ 
            "question" => "Gestión (Facturación)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "La facturacion es oportuna",
                "2" => "La factura es ocasional",
                "3" => "No cumple oportunamente con la facturacion"
            ]
        ],
        (object)[ 
            "question" => "Post contractual (Reclamaciones)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Atiende oportunamente las reclamaciones presentadas",
                "2" => "Atiende ocacionalmente las reclamaciones presentadas",
                "3" => "No atiende reclamaciones"
            ]
        ],
        (object)[ 
            "question" => "Servicio post venta", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "La asesoria es oportuna y acertada",
                "2" => "La asesoria es ocasional",
                "3" => "No presenta servicio de asesorias"
            ]
        ],
    ];
@endphp
<x-modal id="g-supplier-evaluation">
    <form class="flex flex-col items-center w-full gap-2" action="{{ route('suppliers.gSupplierEvaluation') }}" method="POST" id="supplier-evaluation-form" target="_blank">
        @csrf
        <x-wrapper-form-1>
            <img src="{{ asset('images/purchases/03.png') }}" alt="Supplier Evaluation image" class="{{ $imgCls }}">
            <x-tittle-form class="border-b-2 border-green-700 pb-2">Supplier Evaluation</x-tittle-form>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Supplier</x-label>
                <x-input-1 required name="supplier_id" list="suppliers" placeholder="Select a supplier"></x-input-1>
                <datalist id="suppliers">
                    @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <x-input-1 type="hidden" name="supplier_name"></x-input-1>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="rfc">RFC</x-label>
                <x-input-1 readonly name="rfc"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="address">Address</x-label>
                <x-input-1 readonly name="address" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="evaluator">Evaluator</x-label>
                <x-input-1 name="evaluator"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="evaluation_date">Evaluation date</x-label>
                <x-input-1 type="date" name="evaluation_date"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="products">Products</x-label>
                <x-textarea-1 required name="products" placeholder="Please, write yours items"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Questions</span>
        <div class="accordion-body w-full hidden">
            @foreach ($questions as $index => $q)
            <x-wrapper-form-2 class="border-b-2 border-dashed border-gray-300 pb-2">
                <x-label>{{ $index+1 .".- ". $q->question }}</x-label>
                <x-wrapper-form-1>
                    @foreach ($q->answers as $key => $answer)
                    <div class="flex justify-start items-center gap-1 p-2">
                        <x-radio-1 required id="q{{ $index }}_a{{ $key }}" name="answers[{{ $index }}]" value="{{ $key }}"></x-radio-1>
                        <p class="text-sm text-gray-700">{{$answer}}</p>
                    </div>
                    @endforeach
                </x-wrapper-form-1>
                <x-label>Qualification for question {{ $index+1 }}</x-label>
                <x-input-1 class="qualification" type="number" placeholder="Write beetwen 0 to 100" required name="qualification[]" min="0" max="100"></x-input-1>
            </x-wrapper-form-2>
            @endforeach
        </div>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="observations">Observations</x-label>
                <x-textarea-1 required name="observations"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Close</x-button-1>
            <x-button-1 colorBtn="orange">Generate pdf</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#supplier-evaluation-form');
    const suppliers = @json($suppliers);
    function reactiveSupplierEvaluation(){
        father.on('click','.accordion-header', function () {
            const $body = $(this).next('.accordion-body');
            father.find('.accordion-body').not($body).slideUp();
            $body.slideToggle();
        });

        father.on('keyup', '.qualification', function(){
            const qualification = $(this).val();
            const value = Number(qualification);
            if (value > 100 || value < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Hey, you crossed the limit!',
                    confirmButtonText: 'OK'
                });
                $(this).val('0');
            }
        });

        function getSupplierById(id) {
            return fetch(`/suppliers/${id}`)
                .then(res => res.json());
        }

        father.on('keyup','#supplier_id',function(){
            const id = $(this).val();
            if (/^\d+$/.test(id)){
                const item = suppliers.find(x => x.supplier_id == id);
                father.find('#supplier-name').text(`${item ? item.name : ''}`);
                getSupplierById(id).then(item => {
                    father.find('#supplier_name').val(item.supplier.name);
                    father.find('#rfc').val(item.supplier.rfc);
                    father.find('#address').val(`${item.supplier.address ? item.supplier.address : ''} ${item.supplier.city ? item.supplier.city : ''} ${item.supplier.district ? item.supplier.district : ''} ${item.supplier.state ? item.supplier.state : ''}`);
                });
            }else{
                father.find('#supplier-name').text(``);
                father.find('#rfc, #address, #supplier_name').val(``);
            }
        });
    }
    reactiveSupplierEvaluation();
});
</script>
@endpush