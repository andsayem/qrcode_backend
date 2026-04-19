<div class="table-responsive">
    <table class="table" id="campaigns-table">
        <thead>
            <tr>
                <th>Campaign Category Id</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Product Id</th>
                <th>Point</th>
                <th>Title</th>
                <th>Image</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->campaign_category_id }}</td>
                <td>{{ $campaign->start_date }}</td>
                <td>{{ $campaign->end_date }}</td>
                <td>{{ $campaign->product_id }}</td>
                <td>{{ $campaign->point }}</td>
                <td>{{ $campaign->title }}</td>
                <td> 
                    @if($campaign->image)
                    <img src="{{ asset('storage/campaign/'.$campaign->image) }}" style="width: 200px;" alt="Image ss">
                    @else
                    <img src="{{ asset('no-image.png') }}" alt="Image">
                    @endif

                </td>
                <td width="120">
                    {!! Form::open(['route' => ['campaigns.destroy', $campaign->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('campaigns.show', [$campaign->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('campaigns.edit', [$campaign->id]) }}" class='btn btn-default btn-xs'>
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