Halo, {{ $name }}.
Anda melakukan permintaan untuk mengatur ulang kata sandi dengan akun {{ $email }}.
Untuk mengatur ulang kata sandi, silakan buka tautan dibawah ini.

{{-- //TODO: Link to frontend --}}
{!! env('APP_URL').'/forgot-password?email='.$email.'&token=' .$token !!}
