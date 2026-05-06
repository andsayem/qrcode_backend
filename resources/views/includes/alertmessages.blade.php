

<script src="{{ asset('/assets/js/sweetalert2.all.min.js') }}"></script>

@if (session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: '{!! is_array(session('success')) ? session('success')[0] : session('success') !!}',
        icon: 'success',
        confirmButtonText: 'OK'
    })
</script>
@endif


@if (session('fail'))
<script>
    Swal.fire({
        title: 'Error!',
        text: '{!! is_array(session('fail')) ? session('fail')[0] : session('fail') !!}',
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
