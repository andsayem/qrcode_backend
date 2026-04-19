<div class="table-responsive">
    <table class="table" id="channelSettings-table">
        <thead>
            <tr>
                <th>Channel Id</th>
        <th>Slab Value</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($channelSettings as $channelSettings)
            <tr>
                <td>{{ $channelSettings->channel_id }}</td>
            <td>{{ $channelSettings->slab_value }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['channelSettings.destroy', $channelSettings->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('channelSettings.show', [$channelSettings->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('channelSettings.edit', [$channelSettings->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
