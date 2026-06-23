<x-modal id="edit-supplier">
    <div class="flex flex-col items-center w-full gap-2 my-2">
        <form class="flex flex-col items-center w-full gap-2" id="edit-supplier-form">
            @csrf
            <input type="hidden" id="edit_original_code" name="original_code">

            <x-wrapper-form-1>
                <x-tittle-form>Edit Supplier</x-tittle-form>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_Name">Supplier Name</x-label>
                    <x-input-1 required name="Name" id="edit_Name"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="edit_Code_supplier">Supplier Code</x-label>
                    <x-input-1 required type="number" name="Code_supplier" id="edit_Code_supplier"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_Product">Main Product</x-label>
                    <x-input-1 name="Product" id="edit_Product"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="edit_RFC">RFC</x-label>
                    <x-input-1 name="RFC" id="edit_RFC"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_Address">Address</x-label>
                    <x-textarea-1 name="Address" id="edit_Address"></x-textarea-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_Phone">Phone Number</x-label>
                    <x-input-1 type="tel" name="Phone" id="edit_Phone"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="edit_Email">Email Address</x-label>
                    <x-input-1 type="email" name="Email" id="edit_Email"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_Contact">Contact Person</x-label>
                    <x-input-1 name="Contact" id="edit_Contact"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-button-1 class="close-modal" type="button" onclick="document.getElementById('edit-supplier').close()" colorBtn="red">Close</x-button-1>
                <x-button-1 type="submit" colorBtn="sky">Update Supplier</x-button-1>
            </x-wrapper-form-1>
        </form>
    </div>
</x-modal>