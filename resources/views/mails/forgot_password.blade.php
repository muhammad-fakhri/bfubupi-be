@component('mail::panel')
## Halo, {{ $name }}.
Anda melakukan permintaan untuk mengatur ulang kata sandi dengan akun {{ $email }}.
Untuk mengatur ulang kata sandi, silakan tekan tombol dibawah ini.
@endcomponent

@component('mail::button', ['url' => env('APP_URL').'/forgot-password?email='.$email.'&token=' .$token, 'color' => 'success'])
Atur Ulang Kata Sandi
@endcomponent
