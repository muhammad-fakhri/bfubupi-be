@component('mail::panel')
## Halo, {{ $name }}.
Anda berhasil mendaftar di BFUB UPI menggunakan email {{ $email }}.
Sebelum bisa melanjutkan, silakan konfirmasi email anda dengan menekan tombol dibawah ini.
@endcomponent

@component('mail::button', ['url' => env('APP_URL').'/user/email/verify?email='.$email.'&token=' .$token, 'color' => 'success'])
Konfirmasi Email
@endcomponent
