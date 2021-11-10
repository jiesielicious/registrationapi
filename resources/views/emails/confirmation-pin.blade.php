@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

<h3> This is your confirmation Pin: {{ $data->confirmation_pin }}</h3>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
