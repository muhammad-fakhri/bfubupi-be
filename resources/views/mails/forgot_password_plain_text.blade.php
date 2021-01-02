Halo, {{ $name }}.
Anda melakukan permintaan untuk mengatur ulang kata sandi dengan akun {{ $email }}.
Untuk mengatur ulang kata sandi, silakan buka tautan dibawah ini.

{!! env('FRONTEND_URL').'/user/reset-password?email='.$email.'&token=' .$token !!}
