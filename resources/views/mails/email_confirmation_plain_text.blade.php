Halo, {{ $name }}.
Anda berhasil mendaftar di BFUB UPI menggunakan email {{ $email }}.
Sebelum bisa melanjutkan, silakan konfirmasi email anda dengan membuka tautan dibawah ini.

{!! env('APP_URL').'/user/email/verify?email='.$email.'&token=' .$token !!}
