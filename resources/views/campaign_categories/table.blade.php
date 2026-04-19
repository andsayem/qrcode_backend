<div class="table-responsive">
    <table class="table" id="campaignCategories-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Details</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($campaignCategories as $campaignCategory)
            <tr>
                <td>{{ $campaignCategory->name }}</td>
            <td>{{ $campaignCategory->details }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['campaignCategories.destroy', $campaignCategory->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('campaignCategories.show', [$campaignCategory->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('campaignCategories.edit', [$campaignCategory->id]) }}" class='btn btn-default btn-xs'>
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
