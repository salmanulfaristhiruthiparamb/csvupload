@component('mail::message')
Hi, 
Following errors found on csv file "{{ $file }}". <br/>

@foreach ($errors as $key => $message)
    {{ $key }} at rows @json($message) <br/>
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent
