<div class="table-responsive">
    <table class="table" id="channels-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Status</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($channels as $channel)
            <tr>
                <td>{{ $channel->name }}</td>
            <td>{{ $channel->status }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['channels.destroy', $channel->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('channels.show', [$channel->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('channels.edit', [$channel->id]) }}" class='btn btn-default btn-xs'>
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
