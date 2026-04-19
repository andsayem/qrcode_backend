@if($errors->has($field_name))
    <p style="color:red;  margin-bottom: 5px;">{{ $errors->first($field_name) }}</p>
@elseif (isset($session_field_name) && session('fail')!=null)
    <p style="color:red;  margin-bottom: 5px;">{{ session('fail')[0] }}</p>
@endif

