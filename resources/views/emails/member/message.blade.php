@component('mail::message')
# New {{ $data['message_type'] }}


# Message
{{ $data['message_text'] }}



Thanks,<br>
{{ config('app.name') }}
@endcomponent
