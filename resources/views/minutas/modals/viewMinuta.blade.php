<x-modal id="view-minuta">
    <div class="flex flex-col w-full max-w-2xl mx-auto p-2">
        
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Detalles de la Minuta</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div id="view-content" class="w-full flex flex-col gap-4 max-h-[65vh] overflow-y-auto px-2">
        </div>

        <x-wrapper-form-1 class="border-t pt-4 mt-2">
            <x-wrapper-form-2 class="flex justify-end">
                <x-button-1 type="button" class="close-modal" colorBtn="red">
                    Cerrar Vista
                </x-button-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
    </div>
</x-modal>

<style>
    #view-minuta {
        display: none; 
        position: fixed;
        inset: 0;
        z-index: 999; 
        background-color: rgba(0, 0, 0, 0.6); 
        
        
        display: none; 
        align-items: center; 
        justify-content: center;
    }

    #view-minuta > div {
        margin: auto !important;
        display: block !important;
        position: relative;
    }
    
    #view-minuta .view-card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
</style>