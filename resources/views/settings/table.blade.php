<div class="table-responsive">
    <table class="table" id="settings-table">
        <thead>
            <tr>
                <th>Min Redeem Point</th>
        <th>Point Rate</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($settings as $settings)
            <tr>
                <td>{{ $settings->min_redeem_point }}</td>
            <td>{{ $settings->point_rate }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['settings.destroy', $settings->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('settings.show', [$settings->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('settings.edit', [$settings->id]) }}" class='btn btn-default btn-xs'>
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
