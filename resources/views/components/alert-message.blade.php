@props(['level' => 'info', 'message' => ''])

@if($message)
<div {{ $attributes->merge(['class' => 'alert alert-' . $level]) }} role="alert">
    {!! $message !!}
</div>
@endif