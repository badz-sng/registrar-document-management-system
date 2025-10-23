@if(!auth()->check())
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
@endif
