<?php
$paginator = getFormattedPaginatedArray($paginator)
?>

<div className="float-left">
    Showing {{$paginator->current_page==1 ? 1 : ($paginator->current_page-1)*$paginator->record_per_page+1}}
    to {{$paginator->current_page==1 ? $paginator->current_page_items_count : ($paginator->current_page-1)*$paginator->record_per_page+$paginator->current_page_items_count}}
    of {{$paginator->total_count}} entries
</div>

@if($paginator->total_pages>1)
    <ul class="pagination mt-2 float-right">
        <li class="page-item {{ ($paginator->current_page == 1) ? ' disabled' : '' }}">
            <a href="?page=1{{generate_all_req_to_query_params(request()->all())}}" class="page-link"
               aria-label="First page" data-toggle="tooltip" title="First page">
                <i class="fa fa-fast-backward" aria-hidden="true"></i>
            </a>
        </li>
        <li class="page-item {{ ($paginator->current_page == 1) ? ' disabled' : '' }}">
            <a class="page-link"
               href="?page={{ $paginator->current_page-1 }}{{generate_all_req_to_query_params(request()->all())}}"
               aria-label="Previous page" data-toggle="tooltip" title="Previous page"><span
                    aria-hidden="true">&laquo;</span></a>
        </li>

        @if($paginator->current_page > 3)
            <li class="page-item">
                <a class="page-link" href="?page={{ 1 }}{{generate_all_req_to_query_params(request()->all())}}">1</a>
            </li>
        @endif

        @if($paginator->current_page > 4)
            <li class="mx-1"><span>...</span></li>
        @endif
        @for ($i = 1; $i <= $paginator->pagination_last_page; $i++)
            @if($i >= $paginator->current_page - 2 && $i <= $paginator->current_page + 2)
                @if ($i == $paginator->current_page)
                    <li class="page-item {{ ($paginator->current_page == $i) ? ' active' : '' }}"><a
                            class="page-link">{{ $i }}</a></li>
                @else
                    <li><a class="page-link"
                           href="?page={{ $i }}{{generate_all_req_to_query_params(request()->all())}}">{{ $i }}</a></li>
                @endif
            @endif
        @endfor

        @if($paginator->current_page < $paginator->pagination_last_page - 3)
            <li class="mx-1"><span>...</span></li>
        @endif

        <li class="page-item {{ ($paginator->current_page == $paginator->pagination_last_page) ? ' disabled' : '' }}">
            <a class="page-link" href="?page={{ $paginator->current_page+1 }}{{generate_all_req_to_query_params(request()->all())}}" aria-label="Next page" data-toggle="tooltip" title="Next page"><span aria-hidden="true">&raquo;</span></a>
        </li>
        <li class="page-item {{ ($paginator->current_page == $paginator->pagination_last_page) ? ' disabled' : '' }}">
            <a href="?page={{$paginator->pagination_last_page}}{{generate_all_req_to_query_params(request()->all())}}" class="page-link" aria-label="Last page" data-toggle="tooltip" title="Last page">
                <i class="fa fa-fast-forward" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
@endif

<style>
    .page-item.active .page-link {
        background-color: #17a2b8;
    !important;
    }
</style>
