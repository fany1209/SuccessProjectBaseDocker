<x-modal id="movements">
    <x-wrapper-form-1>
        <x-tittle-form>Movements</x-tittle-form>

        <x-toggle-switch
            id="type"
            name="type"
            value="Input"
            :checked="true"
            onLabel="Input"
            offLabel="Output"
            onColor="emerald-500"
            offColor="red-700"
            textColor="white"
            class=""
        />
    </x-wrapper-form-1>

    <x-wrapper-form-1>
        <x-button-1 id="back" type="button" colorBtn="orange">Back</x-button-1>
    </x-wrapper-form-1>

    <x-wrapper-form-2 id="movements" class="my-2 space-y-1">
        @foreach ($inputs_years as $item)
            <span class="year cursor-pointer w-full bg-green-300 hover:bg-green-400 text-green-600 hover:text-white text-2xl font-semibold tracking-[2px] rounded-md p-3">
                {{ $item->year }}
            </span>
        @endforeach
    </x-wrapper-form-2>

    <x-wrapper-form-1>
        <x-button class="close-modal">Close</x-button>
    </x-wrapper-form-1>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#movements');

    const outputs_years    = @json($outputs_years);
    const outputs_months   = @json($outputs_months);
    const outputs_dates    = @json($outputs_dates);
    const outputs_products = @json($outputs_products);
    const inputs_years     = @json($inputs_years);
    const inputs_months    = @json($inputs_months);
    const inputs_dates     = @json($inputs_dates);
    const inputs_products  = @json($inputs_products);

    function isInput(){
        return father.find('input[type="checkbox"]#type').prop('checked') === true;
    }

    function clearMovements(){
        father.find('#movements-wrapper').empty();
    }

    function renderYears(){
        clearMovements();

        if(isInput()){
            inputs_years.forEach(item => {
                father.find('#movements-wrapper').append(`
                    <span class="year cursor-pointer w-full bg-green-300 hover:bg-green-400 text-green-600 hover:text-white text-2xl font-semibold tracking-[2px] rounded-md p-3">
                        ${item.year}
                    </span>
                `);
            });
        }else{
            outputs_years.forEach(item => {
                father.find('#movements-wrapper').append(`
                    <span class="year cursor-pointer w-full bg-red-300 hover:bg-red-400 text-red-600 hover:text-white text-2xl font-semibold tracking-[2px] rounded-md p-3">
                        ${item.year}
                    </span>
                `);
            });
        }
    }

    function reactiveMovementsModal(){
        const month_name = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        father.on('click','.edit-transaction-btn',function(){
            const id = $(this).data('id');
            const typeLower = isInput() ? 'input' : 'output';

            if(typeof window.fillEditTransactionModal === 'function'){
                window.fillEditTransactionModal(id, typeLower);
            }else{
                console.error('fillEditTransactionModal() no existe. Asegúrate de que el script del modal edit-transaction se cargue antes.');
            }
        });

        father.on('click','.alter-date',function(){
            const modal = $('#alterDate');
            const id = $(this).data('id');
            const date = $(this).data('date');
            const type = isInput() ? 'input' : 'output';

            modal.find('#tittle').text(`Alter Date ${type}`);
            modal.find('#date').text(`${date}`);
            modal.find('#id').val(id);
            modal.find('#type').val(type);
            modal.find('#updated_at').val('');
        });

        father.on('click','.printFormat',function(){
            const id = $(this).data('id');
            const route = `download-pdf/${isInput() ? 'inputs' : 'outputs'}/${id}`;
            window.open(route, '_blank');
        });

        father.on('click','.accordion-toggle',function(){
            const content = $(this).next();
            const id = $(this).data('id');

            content.toggleClass('max-h-0');
            content.empty();

            if(isInput()){
                inputs_products.forEach(item => {
                    if(item.input_id == id){
                        const quantity = Number(item.quantity);
                        content.append(`<p class="py-2 text-gray-600">${item.name} | <b>${quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} ${item.unit}</b></p>`);
                    }
                });
            }else{
                outputs_products.forEach(item => {
                    if(item.output_id == id){
                        const quantity = Number(item.quantity);
                        content.append(`<p class="py-2 text-gray-600">${item.name} | <b>${quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} ${item.unit}</b></p>`);
                    }
                });
            }
        });

        father.on('click','.month',function(){
            const year = $(this).data('year');
            const month = $(this).data('month');

            clearMovements();

            if(isInput()){
                inputs_dates.forEach(item => {
                    if(item.year == year && item.month == month){
                        father.find('#movements-wrapper').append(`
                            <div class="w-full my-1">
                                <span data-id="${item.input_id}" class="accordion-toggle block cursor-pointer w-full bg-gray-300 hover:bg-gray-400 text-gray-600 hover:text-white text-md font-semibold tracking-[2px] rounded-md p-2">${item.date}</span>
                                <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300"></div>

                                <button data-id="${item.input_id}" class="printFormat h-full w-auto bg-yellow-400 hover:bg-yellow-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Print format</button>
                                <button data-id="${item.input_id}" data-date="${item.date}" data-target="alterDate" class="open-modal alter-date h-full w-auto bg-purple-400 hover:bg-purple-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Alter Date</button>
                                <button data-id="${item.input_id}" data-target="edit-transaction" class="open-modal edit-transaction-btn h-full w-auto bg-sky-400 hover:bg-sky-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Edit format</button>
                            </div>
                        `);
                    }
                });
            }else{
                outputs_dates.forEach(item => {
                    if(item.year == year && item.month == month){
                        father.find('#movements-wrapper').append(`
                            <div class="w-full my-1">
                                <span data-id="${item.output_id}" class="accordion-toggle block cursor-pointer w-full bg-gray-300 hover:bg-gray-400 text-gray-600 hover:text-white text-md font-semibold tracking-[2px] rounded-md p-2">${item.date}</span>
                                <div class="accordion-content max-h-0 overflow-hidden transition-all duration-300"></div>

                                <button data-id="${item.output_id}" class="printFormat h-full w-auto bg-yellow-400 hover:bg-yellow-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Print format</button>
                                <button data-id="${item.output_id}" data-date="${item.date}" data-target="alterDate" class="open-modal alter-date h-full w-auto bg-purple-400 hover:bg-purple-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Alter Date</button>
                                <button data-id="${item.output_id}" data-target="edit-transaction" class="open-modal edit-transaction-btn h-full w-auto bg-sky-400 hover:bg-sky-700 hover:text-white text-gray-700 px-1 py-1 rounded-sm text-xs">Edit format</button>
                            </div>
                        `);
                    }
                });
            }
        });

        father.on('click','.year',function(){
            const year = parseInt($(this).text());

            clearMovements();

            if(isInput()){
                inputs_months.forEach(item => {
                    if(item.year == year){
                        father.find('#movements-wrapper').append(`
                            <span data-year="${year}" data-month="${item.month}" class="month cursor-pointer w-full bg-blue-300 hover:bg-blue-400 text-blue-600 hover:text-white text-lg font-semibold tracking-[2px] rounded-md p-2">
                                ${month_name[item.month-1]}
                            </span>
                        `);
                    }
                });
            }else{
                outputs_months.forEach(item => {
                    if(item.year == year){
                        father.find('#movements-wrapper').append(`
                            <span data-year="${year}" data-month="${item.month}" class="month cursor-pointer w-full bg-blue-300 hover:bg-blue-400 text-blue-600 hover:text-white text-lg font-semibold tracking-[2px] rounded-md p-2">
                                ${month_name[item.month-1]}
                            </span>
                        `);
                    }
                });
            }
        });

        father.on('click','#back',function(){
            renderYears();
        });

        father.on('change','input[type="checkbox"]#type',function(){
            renderYears();
        });
    }

    // INIT
    reactiveMovementsModal();

});
</script>
@endpush
