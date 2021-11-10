@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Let's assume this is the form. 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
