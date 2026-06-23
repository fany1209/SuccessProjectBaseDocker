{{--
Profile
Fecha de creación: 23-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 23-09-2025
--}}
<section class="flex flex-col items-start gap-4 rounded-lg w-full bg-white shadow-md p-4">
    <div class="flex flex-col items-start gap-2 border-b-2 border-green-300 w-full pb-2">
        <h2 class="text-2xl text-gray-700 tracking-[3px] font-semibold">Browser Sessions</h2>
        <p class="text-gray-500 text-sm tracking-[2px]">Manage and log out your active sessions on other browsers and devices.</p>
    </div>
    <p class="text-gray-500 text-sm tracking-[2px]">If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.</p>
    @if(!empty($sessions) && $sessions->count() > 0)
    @foreach($sessions as $session)
        <span class="flex flex-col lg:flex-row lg:justify-center lg:items-center items-start gap-4 lg:w-1/2">
            <x-nav-button icon="ri-computer-line">{{ $session['ip_address'] ?? 'Unknown IP' }} - {{ $session['agent']['platform'] ?? 'Unknown' }} / {{ $session['agent']['browser'] ?? 'Unknown' }}</x-nav-button>
            @if($session['is_current_device'])<span class="w-1/3 bg-green-500 text-white text-md font-semibold tracking-[2px] px-2 py-1 rounded">This device</span>@endif
        </span>
    @endforeach
    @else
        <p class="text-gray-500">No active sessions found.</p>
    @endif
    <button id="logoutOtherSessionsBtn" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
        Log Out Other Browser Sessions
    </button>
</section>
@push('js')
<script>
$(document).ready(function(){
    $("#logoutSessionsForm").on("submit", function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: $(this).serialize(),
            success: function(){
                $("#logoutMessage").removeClass('hidden').fadeIn();
            }
        });
    });
    $("#logoutOtherSessionsBtn").on("click", function() {
        Swal.fire({
            title: 'Log Out Other Browser Sessions',
            html: `
                <p class="text-sm text-gray-700 mb-3">
                    Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.
                </p>
                <input type="password" id="swalPassword" class="w-full border-gray-300 rounded-md shadow-sm p-2" placeholder="Password">
            `,
            showCancelButton: true,
            confirmButtonText: 'Log Out Other Browser Sessions',
            cancelButtonText: 'Cancel',
            focusConfirm: false,
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#swalPassword').value;
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar vía AJAX
                $.ajax({
                    url: "{{ route('other-browser-sessions.destroy') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        password: result.value
                    },
                    success: function(res) {
                        Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            } else {
                                Swal.fire('Cancelled', 'No changes were made.', 'info');
                            }
                        });
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Password incorrect or something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
</script>
{{-- Mostrar alert con errores o éxito --}}
@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        html: `
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        `,
        confirmButtonColor: '#d33'
    });
</script>
@endif
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6'
    });
</script>
@endif
@endpush