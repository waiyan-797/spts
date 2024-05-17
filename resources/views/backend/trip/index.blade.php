@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-md-3">
			<form action="{{ route('trips.search') }}" method="GET">
				@csrf
				<div class="input-group mb-3">
					<button class="btn btn-outline-secondary text-dark" type="submit" style="border:1px solid #ced4da"><i
							class="fa-solid fa-magnifying-glass">
						</i>
					</button>
					<input class="form-control" name="key" type="text" placeholder="Search">
				</div>
			</form>
		</div>
		<div class="table-responsive small">
			<table class="table table-striped table-hover">
				<thead class="table-secondary align-top" style="border-bottom:1px solid #ccc">
					<tr class="">
						<th>Driver ID</th>
						<th>Driver Name</th>
						<th>Start</th>
						<th>End</th>
						<th>Total Cost (Ks)</th>
						<th>Date</th>
						@role('admin')
							<th>Action</th>
						@endrole
					</tr>
				</thead>
				<tbody class="table-group-divider" style="border-top:10px solid #ffffff">

                        @foreach ($trips as $key => $trip)
                            <tr class="">
                                <div class="accordion-item">
                                    <td scope="row">{{ $trip->user->driver_id }}</td>
                                    <td>
                                        <a class="text-dark text-decoration-none"
                                            href="{{ route('users.show', $trip->user->id) }}">{{ $trip->user->name }}</a>
                                    </td>
                                    <td>{{ $trip->start_pos }}</td>
                                    <td>{{ $trip->end_pos }}</td>
                                    <td>{{ $trip->total_cost }}</td>
                                    <td>{{ Carbon\Carbon::parse($trip->created_at)->format('d-m-Y') }}</td>
                                    @role('admin')
                                        <td>
                                            <span>
                                                <form class="d-inline" action="{{ route('trip.destroy', ['trip' => $trip]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-reset btn-clear" type="submit">
                                                        <i class="fa-regular fa-trash-can text-danger"></i>
                                                    </button>
                                                </form>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-none rounded-circle" data-bs-toggle="modal" data-bs-target="#tripId_{{ $trip->id }}">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                        </td>
                                    @endrole
                                </div>
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="tripId_{{ $trip->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">{{ $trip->user->name }}</h1>
                                            <span class="text-muted">{{ Carbon\Carbon::parse($trip->created_at)->format('d-m-Y') }}</span>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Name</div>
                                                    <div class="">{{ $trip->user->name }}</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Start Position</div>
                                                    <div class="">{{ $trip->start_pos }}</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">End Position</div>
                                                    <div class="">{{ $trip->end_pos }}</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Distance</div>
                                                    <div class="">{{ $trip->distance }} km</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Duration</div>
                                                    <div class="">{{ $trip->duration }}</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Waiting Time</div>
                                                    <div class="">{{ $trip->waiting_time }}</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Waiting Cost</div>
                                                    <div class="">{{ $trip->waiting_fee }} ks</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Normal Cost</div>
                                                    <div class="">{{ $trip->normal_fee }} ks</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Extra Cost</div>
                                                    <div class="">{{ $trip->extra_fee }} ks</div>
                                                </div>
                                            </li>
                                            <li class="list-group-item bg-secondary">
                                                <div class="d-flex justify-content-between">
                                                    <div class="">Total Cost</div>
                                                    <div class="">{{ $trip->total_cost }} ks</div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

				</tbody>
			</table>
			<div class="row m-0 justify-content-between">
				<div class="col-md-2 ps-0">
					<p class=" text-muted">Total: {{ $tripsCount }}</p>
				</div>

				<div class="col-md-2 pe-0">
					<nav class="row m-0">
						<ul class="pagination pagination-sm justify-content-end p-0">
							<li class="page-item {{ $trips->onFirstPage() ? 'disabled' : '' }}">
								<a class="page-link" id="pre-page-link" href="{{ $trips->previousPageUrl() }}" rel="prev"><</a>
							</li>


							@if ($trips->lastPage() > 1 && $trips->lastPage() <= 10)
                                    @for ($i = 1 ; $i <= $trips->lastPage() ; $i++)
                                        <li class="page-item {{ ($trips->currentPage() == $i)? 'active':'' }} ">
                                            <a class="page-link" id="next-page-link" href="{{ $trips->url($i) }}" rel="next">{{ $i }}</a>
                                        </li>
                                    @endfor
                            @elseif ($trips->lastPage() > 10 && $trips->lastPage() <= 40)
                                    @for ($i = 2 ; $i <= $trips->lastPage() ; $i=$i+2)
                                        <li class="page-item {{ ($trips->currentPage() == $i)? 'active':'' }} ">
                                            <a class="page-link" id="next-page-link" href="{{ $trips->url($i) }}" rel="next">{{ $i }}</a>
                                        </li>
                                        @if ($trips->currentPage()%2 != 0 && $i < $trips->currentPage() && ($i+2) > $trips->currentPage() )
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                    @endfor
                            @elseif ($trips->lastPage() > 20 && $trips->lastPage() <= 100)
                                    @for ($i = 5 ; $i <= $trips->lastPage() ; $i=$i+5)
                                        @if ($trips->currentPage() < 5 && ($i-5) < $trips->currentPage())
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                        <li class="page-item {{ ($trips->currentPage() == $i)? 'active':'' }} ">
                                            <a class="page-link" id="next-page-link" href="{{ $trips->url($i) }}" rel="next">{{ $i }}</a>
                                        </li>
                                        @if ($trips->currentPage()%5 != 0 && $i < $trips->currentPage() && ($i+5) > $trips->currentPage() )
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                    @endfor
                            @elseif ($trips->lastPage() > 50 && $trips->lastPage() <= 1000)
                                    @for ($i = 50 ; $i <= $trips->lastPage() ; $i=$i+50)
                                        @if ($trips->currentPage() < 50 && ($i-50) < $trips->currentPage())
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                        <li class="page-item {{ ($trips->currentPage() == $i)? 'active':'' }} ">
                                            <a class="page-link" id="next-page-link" href="{{ $trips->url($i) }}" rel="next">{{ $i }}</a>
                                        </li>
                                        @if ($trips->currentPage()%50 != 0 && $i < $trips->currentPage() && ($i+50) > $trips->currentPage() )
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                    @endfor
                            @elseif ($trips->lastPage() > 1000 && $trips->lastPage() <= 10000)
                                    @for ($i = 500 ; $i <= $trips->lastPage() ; $i=$i+500)
                                        @if ($trips->currentPage() < 500 && ($i-500) < $trips->currentPage())
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                        <li class="page-item {{ ($trips->currentPage() == $i)? 'active':'' }} ">
                                            <a class="page-link" id="next-page-link" href="{{ $trips->url($i) }}" rel="next">{{ $i }}</a>
                                        </li>
                                        @if ($trips->currentPage()%500 != 0 && $i < $trips->currentPage() && ($i+500) > $trips->currentPage() )
                                            <li class="page-item active ">
                                                <a class="page-link" id="next-page-link" href="{{ $trips->url($trips->currentPage()) }}" rel="next">{{ $trips->currentPage() }}</a>
                                            </li>
                                        @endif
                                    @endfor
                            @endif

							<li class="page-item {{ $trips->hasMorePages() ? '' : 'disabled' }}">
								<a class="page-link" id="next-page-link" href="{{ $trips->nextPageUrl() }}" rel="next">></a>
							</li>
						</ul>
					</nav>
				</div>

			</div>

		</div>

	</div>
@endsection
@push('script')
	<script></script>
@endpush
