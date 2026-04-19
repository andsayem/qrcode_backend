

<script src="{{ asset('/assets/js/sweetalert2.all.min.js') }}"></script>

@if (session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: '{!! session('success')[0] !!}',
        icon: 'success',
        confirmButtonText: 'OK'
    })
</script>
@endif


@if (session('fail'))
<script>
    Swal.fire({
        title: 'Error!',
        text: '{!! session('fail')[0] !!}',
        icon: 'error',
        confirmButtonText: 'OK'
    })
</script>
@endif


{{--@if ($errors->any())
<script>
    Swal.fire({
        title: 'Error!',
        text: '{!! $errors->all()[0] !!}',
        icon: 'error',
        confirmButtonText: 'Ok'
    })
</script>
@endif--}}



