@component('mail::message')
# Hi!

{{ $invite->team->owner->name }} has re-invited you to join their {{ $invite->team->name }} team!

Click here to join:

@component('mail::button', ['url' => url('teams/accept/'.$invite->accept_token)])
    Accept
@endcomponent

Thanks,<br>
{{ $invite->team->owner->name }} | {{ $invite->team->name }}
@endcomponent
