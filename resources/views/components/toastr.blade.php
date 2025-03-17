@if($messages = session('alerts'))
    @foreach($messages as $message)
        @push('scripts')
            <script>toastr.{{$message['type'] ?? 'info'}}('{!!  addslashes($message['message'])!!}','{{ $message['title'] ?? '' }}', {!! $message['options'] ?? "{timeOut:5000}" !!} )</script>
        @endpush
    @endforeach
@endif
@if (isset($errors))
    @foreach ($errors->all() as $error)
        @push('scripts')
            <script>toastr.error('{{$error}}', '', {!! $message['options'] ?? "{timeOut:0}" !!})</script>
        @endpush
    @endforeach
@endif