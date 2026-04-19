@foreach ($status as $status_item)
    {{-- {{dd($status_item)}} --}}
    {{-- {{dd($status_item['key'])}} --}}
    @if ($status_item['value']==$selected)
        <span class="badge {{$status_item['class']}}">{{$status_item['key']}}</span>
    @endif
@endforeach
