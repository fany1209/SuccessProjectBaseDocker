<x-modal id="check-requisition" class="modal hidden">
    <div class="flex flex-col items-center w-full gap-2 my-2">
        <form id="check-requisition-form" class="flex flex-col items-center w-full gap-2">
            @csrf
            <input type="hidden" name="id" id="requisition_id_input">

            <x-wrapper-form-1>
                <x-tittle-form>Check Requisition</x-tittle-form>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="consecutive">Consecutive</x-label>
                    <x-input-1 id="consecutive_input" name="consecutive" required/>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="purchase_order">Purchase Order</x-label>
                    <x-input-1 id="purchase_order_input" name="purchase_order" list="po_list" required placeholder="Select or type Folio..."/>
                    <datalist id="po_list"></datalist>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-button-1 type="button" class="close-modal-check" colorBtn="red">
                    Close
                </x-button-1>
                <x-button-1 type="submit" colorBtn="sky">
                    Finish
                </x-button-1>
            </x-wrapper-form-1>
        </form>
    </div>
</x-modal>

<script>
$(document).ready(function () {

    function loadPurchaseOrders() {
        $.get("{{ route('requisitions.getPurchaseOrderFolios') }}", function(data) {
            let options = '';
            data.forEach(po => { options += `<option value="${po.id}">`; });
            $('#po_list').html(options);
        });
    }

    $(document).on('click', '.check-requisition-btn', function (e) {
        e.preventDefault();
        
        const $btn = $(this);
        const id = $btn.attr('data-id'); 
        const consecutive = $btn.attr('data-consecutive');
        const po = $btn.attr('data-po');

        $('#requisition_id_input').val(id);
        $('#consecutive_input').val(consecutive);
        $('#purchase_order_input').val(po);

        loadPurchaseOrders();
        $('#check-requisition').removeClass('hidden');
    });

    $(document).on('click', '.close-modal-check', function(){
        $('#check-requisition').addClass('hidden');
    });

    $('#check-requisition-form').on('submit', function (e) {
        e.preventDefault();

        const currentId = $('#requisition_id_input').val();
        if(!currentId){
            Swal.fire('Error', 'El ID de la requisición no se cargó correctamente.', 'error');
            return;
        }

        let formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ route('requisitions.checkRequisition') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                Swal.fire('Success', 'Requisición revisada correctamente', 'success')
                    .then(() => location.reload());
            },
            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.error || 'Error al procesar';
                if(xhr.responseJSON?.details) {
                    errorMsg = Object.values(xhr.responseJSON.details).flat().join("<br>");
                }
                Swal.fire({ icon: 'error', title: 'Error', html: errorMsg });
            }
        });
    });
});
</script>