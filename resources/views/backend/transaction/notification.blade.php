@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row mb-2 px-md-5 px-3">
			<div class="col-6 p-0 text-start {{ $notificationsUnread>0?'text-danger':'' }}">
				<span class="me-2">Unread</span> : {{ $notificationsUnread }}
			</div>
			<div class="col-6 p-0 text-end">
				<span class="me-5"><span class="me-2">Total</span> : {{ $notificationsCount }}</span>
			</div>
		</div>
		<div class="row px-md-5 px-4 mb-3">
            <div class="col-12 p-0 mb-3">
                <form class="row justify-content-end" id="selected_noti_delete_form" action="{{ route('notifiaction.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="notifications" id="notifications_ids">
                    <div class="col-md-1 col-4 p-0">
                        <button class="btn btn-danger form-control" type="button" onclick="deleteSelectedNoti()">
                            Delete
                        </button>
                    </div>
                    <div class="col-md-1 col-2 d-flex flex-column justify-content-center align-items-center p-0">
                        <span class="mb-1 text-muted">Select All</span>
                        <input type="checkbox" class="form-check-input" name="" value="checked_all" id="checked_all" onchange="checkedAll()">
                    </div>
                </form>
            </div>
            @foreach ($notifications as $noti)
                <div class="col-12 p-0 mb-1">
                    <div class="row px-0">
                        <div class="col-10 px-0 col-md-11 alert border @if ($noti->status == 'unread') alert-primary @endif py-2 px-1 my-1 rounded justify-content-between text-center">
                            <div class="row p-1 text-small" >
                                <div class="col d-none d-sm-block">#{{ $noti->user->driver_id }}</div>
                                <div class="col text-start">{{ $noti->user->name }}</div>
                                <div class="col text-end">{{ $noti->amount.' Ks' }}</div>
                                <div class="col d-none d-sm-block">{{ $noti->service }}</div>
                                <div class="col d-none d-sm-block">{{ Carbon\Carbon::parse($noti->created_at)->diffForHumans() }}</div>
                                <div class="col">
                                    <!-- Modal -->
                                    <div class="modal fade" id="topUpNotiModal{{ $noti->id }}"
                                        aria-labelledby="topUpNotiModal{{ $noti->id }}Label" aria-hidden="true" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header px-3 py-2">
                                                    <h5 class="modal-title" id="topUpNotiModal{{ $noti->id }}Label">TopUp Notification</h5>
                                                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-group ">
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <h6 class=" text-muted">TopUp Username:</h6> <span class="">{{ $noti->user->name }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <h6 class=" text-muted">Service:</h6><span>{{ $noti->service }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <h6 class=" text-muted">Account Username</h6><span>{{ $noti->account_name }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <h6 class=" text-muted">Transfer Phone Number</h6><span>{{ $noti->phone }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <h6 class=" text-muted">Amount</h6><span>{{ $noti->amount }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-center">
                                                            <img class="img-fluid img-thumbnail rounded-top" id="screenshot{{ $noti->id }}"
                                                                src="#" alt="Top Up Screenshot"
                                                                width="100%">
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    @if ($noti->status == 'unread')
                                                        <button class="btn btn-dark" data-bs-dismiss="modal" type="button">Close</button>
                                                        <form action="{{ route('topup.done', $noti) }}" method="post">
                                                            @csrf
                                                            <button class="btn btn-primary" type="submit">Done</button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-dark" data-bs-dismiss="modal" type="button">Close</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($noti->status == 'unread')
                                        <button class="btn btn-primary btn-sm px-2 py-0" data-bs-toggle="modal"
                                            data-bs-target="#topUpNotiModal{{ $noti->id }}" type="button" onclick="fetch_screenshot({{ $noti->id }})">
                                            check
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm px-2 py-0 " data-bs-toggle="modal"
                                            data-bs-target="#topUpNotiModal{{ $noti->id }}" type="button" onclick="fetch_screenshot({{ $noti->id }})">
                                            Done
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-2 col-md-1 my-1 d-flex justify-content-center align-items-center">
                            <input type="checkbox" class="form-check-input" name="{{ $noti->id }}" value="{{ $noti->id }}" id="select_noti">
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="row m-0 justify-content-between px-5 pb-3">
                <nav class="row m-0 py-2 px-5">
                    <ul class="pagination pagination-sm justify-content-end p-0">
                        <li class="page-item {{ $notifications->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" id="pre-page-link" href="{{ $notifications->previousPageUrl() }}" rel="prev"><</a>
                        </li>
                        @if ($notifications->lastPage() > 1 && $notifications->lastPage() <= 10)
                                        @for ($i = 1 ; $i <= $notifications->lastPage() ; $i++)
                                            <li class="page-item {{ ($notifications->currentPage() == $i)? 'active':'' }} ">
                                                <a class="page-link" id="next-page-link" href="{{ $notifications->url($i) }}" rel="next">{{ $i }}</a>
                                            </li>
                                        @endfor
                                @elseif ($notifications->lastPage() > 10 && $notifications->lastPage() <= 40)
                                        @for ($i = 2 ; $i <= $notifications->lastPage() ; $i=$i+2)
                                            <li class="page-item {{ ($notifications->currentPage() == $i)? 'active':'' }} ">
                                                <a class="page-link" id="next-page-link" href="{{ $notifications->url($i) }}" rel="next">{{ $i }}</a>
                                            </li>
                                            @if ($notifications->currentPage()%2 != 0 && $i < $notifications->currentPage() && ($i+2) > $notifications->currentPage() )
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                @elseif ($notifications->lastPage() > 20 && $notifications->lastPage() <= 100)
                                        @for ($i = 5 ; $i <= $notifications->lastPage() ; $i=$i+5)
                                            @if ($notifications->currentPage() < 5 && ($i-5) < $notifications->currentPage())
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                            <li class="page-item {{ ($notifications->currentPage() == $i)? 'active':'' }} ">
                                                <a class="page-link" id="next-page-link" href="{{ $notifications->url($i) }}" rel="next">{{ $i }}</a>
                                            </li>
                                            @if ($notifications->currentPage()%5 != 0 && $i < $notifications->currentPage() && ($i+5) > $notifications->currentPage() )
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                @elseif ($notifications->lastPage() > 50 && $notifications->lastPage() <= 1000)
                                        @for ($i = 50 ; $i <= $notifications->lastPage() ; $i=$i+50)
                                            @if ($notifications->currentPage() < 50 && ($i-50) < $notifications->currentPage())
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                            <li class="page-item {{ ($notifications->currentPage() == $i)? 'active':'' }} ">
                                                <a class="page-link" id="next-page-link" href="{{ $notifications->url($i) }}" rel="next">{{ $i }}</a>
                                            </li>
                                            @if ($notifications->currentPage()%50 != 0 && $i < $notifications->currentPage() && ($i+50) > $notifications->currentPage() )
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                @elseif ($notifications->lastPage() > 1000 && $notifications->lastPage() <= 10000)
                                        @for ($i = 500 ; $i <= $notifications->lastPage() ; $i=$i+500)
                                            @if ($notifications->currentPage() < 500 && ($i-500) < $notifications->currentPage())
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                            <li class="page-item {{ ($notifications->currentPage() == $i)? 'active':'' }} ">
                                                <a class="page-link" id="next-page-link" href="{{ $notifications->url($i) }}" rel="next">{{ $i }}</a>
                                            </li>
                                            @if ($notifications->currentPage()%500 != 0 && $i < $notifications->currentPage() && ($i+500) > $notifications->currentPage() )
                                                <li class="page-item active ">
                                                    <a class="page-link" id="next-page-link" href="{{ $notifications->url($notifications->currentPage()) }}" rel="next">{{ $notifications->currentPage() }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                @endif
                                <li class="page-item {{ $notifications->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" id="next-page-link" href="{{ $notifications->nextPageUrl() }}" rel="next">></a>
                                </li>
                    </ul>
                </nav>
            </div>
        </div>
	</div>
@endsection
@push('script')
	<script>
        function checkedAll() {
            const all_checked = document.querySelector('#checked_all');
            const checkboxes = document.querySelectorAll('#select_noti');
            if(all_checked.checked == true){
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            }else{
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        }

        function deleteSelectedNoti() {
            const selected_noti = document.querySelectorAll('#select_noti:checked');
            const noti_ids = document.querySelector('#notifications_ids');
            const selected_noti_delete_form = document.querySelector('#selected_noti_delete_form');

            const selectedValues = Array.from(selected_noti).map(checkbox => checkbox.value);
            noti_ids.value = JSON.stringify(selectedValues);
            selected_noti_delete_form.submit();
        }

        async function fetch_screenshot(id){
            const domain = window.location.origin;
            await axios.get(`${domain}/topup/notification/${id}`)
                .then((response) => {
                    const noti_screenshot = document.getElementById(`screenshot${id}`);
                    noti_screenshot.src = `${domain}/uploads/images/screenshots/${response.data.screenshot}`;
                })
            .catch((error) => {
                    console.log(error);
                });
        }

    </script>
@endpush
