{{--
Profile
Fecha de creación: 23-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 23-09-2025
--}}
<section class="flex flex-col items-start gap-4 rounded-lg w-full bg-white shadow-md p-4">
    <div class="flex flex-col items-start gap-2 border-b-2 border-green-300 w-full pb-2">
        <h2 class="text-2xl text-gray-700 tracking-[3px] font-semibold">Update Password</h2>
        <p class="text-gray-500 text-sm tracking-[2px]">Ensure your account is using a long, random password to stay secure.</p>
    </div>
    <form action="{{ route('profile.update-password') }}" method="POST" class="flex flex-col items-center w-full gap-4" id="update-password-form">
        @csrf
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="" class="mb-1 block font-medium text-md text-gray-700">Current Password</label>
                <input required type="password" placeholder="Please write your current password" minlength="8" id="" name="current_password" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
            </div>
        </div>
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="" class="mb-1 block font-medium text-md text-gray-700">New Password</label>
                <input required type="password" placeholder="Please write your new password" minlength="8" id="" name="password" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
            </div>
        </div>
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="" class="mb-1 block font-medium text-md text-gray-700">Confirm Password</label>
                <input required type="password" placeholder="" minlength="8" id="" name="password_confirmation" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
            </div>
        </div>
        <div class="flex justify-end items-center w-full gap-2">
            <x-button type="submit" id="">Save</x-button>
        </div>
    </form>
</section>