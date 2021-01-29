@component('mail::message')
# Welcome to Api Social {{$user->nickname}} !!

Thanks for create an account with us!

Please complete the verification by clicking button below:

@component('mail::button', ['url' => route('users.verify', $user->verification_token)])
Verify your account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

