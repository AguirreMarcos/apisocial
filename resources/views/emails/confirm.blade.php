@component('mail::message')
# Hi again {{$user->nickname}} !!

You have received this email due to email address changing request

Please confirm your new email address by clicking the button below:

@component('mail::button', ['url' => route('users.verify', $user->verification_token)])
Verify your new email address
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
