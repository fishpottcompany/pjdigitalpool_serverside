@component('mail::message')
# New {{ $data['message_type'] }}


# Message
{{ $data['message_text'] }}


# User's Name
{{ $data['user_name'] }}


# User's Phone Number
{{ $data['user_phone_number'] }}

# User's Email
{{ $data['user_email'] }}

# User's Email
{{ $data['user_country'] }}


<br>
{{ config('app.name') }} APP
@endcomponent
