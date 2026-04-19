<!-- Channel Id Field -->
<div class="col-sm-12">
    {!! Form::label('channel_id', 'Channel Id:') !!}
    <p>{{ $channelSettings->channel_id }}</p>
</div>

<!-- Slab Value Field -->
<div class="col-sm-12">
    {!! Form::label('slab_value', 'Slab Value:') !!}
    <p>{{ $channelSettings->slab_value }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $channelSettings->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $channelSettings->updated_at }}</p>
</div>

