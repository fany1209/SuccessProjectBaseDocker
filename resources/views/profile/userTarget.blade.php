{{--
Profile
Fecha de creación: 23-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 23-09-2025
--}}
<section class="flex flex-col items-center gap-4 rounded-lg w-full bg-white shadow-md p-4">
    @php
        $name = substr($user->profile_photo_path,15);
    @endphp
    <div class="flex justify-center items-center">
        <img src="/profile-photo/{{ $name }}" alt="Foto de perfil" class="w-24 h-24 sm:w-48 sm:h-48 rounded-full object-cover shadow-md"/>
    </div>
    <h1 class="text-2xl border-b-2 border-green-700 pb-2 px-4 tracking-[3px]">{{ $user->name }}</h1>
    <div class="flex flex-col items-start w-full gap-2">
        <h2 class="text-md tracking-[3px] text-gray-400">Email:</h2>
        <p class="bg-gray-100 w-full text-gray-500 rounded-md p-2 font-semibold tracking-[1px]">{{ $user->email }}</p>
        {{-- Email verification notice --}}
        @if (!$user->hasVerifiedEmail())
            <p class="text-sm text-red-500 mt-1">Your email address is unverified. 
                <form action="{{ route('verification.send') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="underline text-sm text-blue-600 hover:text-blue-800">Click here to re-send the verification email.</button>
                </form>
            </p>
        @endif
    </div>
</section>