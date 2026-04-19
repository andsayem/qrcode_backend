<!-- Min Redeem Point Field -->
<div class="col-sm-12">
    {!! Form::label('min_redeem_point', 'Min Redeem Point:') !!}
    <p>{{ $settings->min_redeem_point }}</p>
</div>

<!-- Point Rate Field -->
<div class="col-sm-12">
    {!! Form::label('point_rate', 'Point Rate:') !!}
    <p>{{ $settings->point_rate }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $settings->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $settings->updated_at }}</p>
</div>

