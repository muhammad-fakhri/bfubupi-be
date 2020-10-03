@component('mail::panel')
## Halo, {{ $name }}.
Anda berhasil mendaftar di BFUB UPI menggunakan email {{ $email}}.
Sebelum bisa melanjutkan, silakan konfirmasi email anda dengan menekan tombol dibawah ini.
@endcomponent

@component('mail::button', ['url' => env('APP_URL').'/verify-email/' .$token, 'color' => 'success'])
Konfirmasi Email
@endcomponent