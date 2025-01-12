@extends('layouts.app')

@section('content')
<div class="container p-3">
    <div class="main-body">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card mb-3 h-100">
                    <div class="card-body position-relative">
                        <div class="d-flex flex-column justify-content-center align-items-center text-center overflow-hidden">
                            @if ($user->userImage && $user->userImage->profile_image)
                                <div class="">
                                    <img src="{{ asset('uploads/images/profiles/' . $user->userImage->profile_image) }}" alt="User"
                                        style="width:100%;height:20rem; object-fit:cover;object-position: center;">
                                </div>
                            @else
                                <img class="" src="{{ asset('assets/logo/user.png') }}" alt="User"
                                    style="width: 10rem;height: 10rem; object-fit: cover; object-position: center;">
                            @endif

                            <div class="mt-3">
                                <h3>{{ $user->name }}</h3>
                            </div>
                        </div>
                        <hr class="my-4">
                            <div class="d-flex justify-content-center w-100">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Online</h6>
                                        @if ($user->onTrip)
                                            <span class="bg-success opacity-75 py-1 px-2 rounded text-white">online</span>
                                        @else
                                            <span class="bg-danger opacity-75 py-1 px-2 rounded text-white">offline</span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Driver ID</h6>
                                        <span class="text-muted">{{ $user->driver_id }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ">
                                        <h6 class="mb-0">Phone</h6>
                                        <span class="text-muted"  id="phone_copy">
                                            {{ $user->phone }}
                                            <i class="btn fa-regular fa-clipboard cursor-pointer position-absolute p-0 border-0" style="right: -0.25rem;top:0.75rem;" id="copy_btn" onclick="copyToClipboard('phone_copy')"></i>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Birth Date</h6>
                                        <span class="text-muted">{{ Carbon\Carbon::parse($user->birth_date)->format('F j,Y') }}</span>
                                    </li>
                                </ul>
                            </div>
                        <div class="p-3">
                            <a class=" form-control btn btn-outline-success mb-4" href="{{ route('users.edit', ['user' => $user]) }}">
                                Edit
                            </a>
                            <form class="d-block" action="{{ route('users.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class=" form-control btn btn-outline-danger" type="submit">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-3">
                <div class="card mb-3 h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-center w-100">
                                    <ul class="list-group list-group-flush w-100">
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Nrc Number</h6>
                                            <span class="text-muted">{{ $user->nrc_no }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Drivinng License</h6>
                                            <span class="text-muted">{{ $user->driving_license }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Taxi Model</h6>
                                            <span class="text-muted">{{ $user->vehicle->vehicle_model }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Plate Number</h6>
                                            <span class="text-muted">{{ $user->vehicle->vehicle_plate_no }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                                            <div class="col-md-4">
                                                <h6 class="mb-0">Address</h6>
                                            </div>
                                            <div class="col-md-8 text-end">
                                                <span class="text-muted ms-auto">{{ $user->address }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex flex-row gap-2">
                                        <div class=" col-8 bg-primary text-white p-2 rounded text-center">{{ "Balance : ".$user->balance }}</div>
                                        <div class=" col-8 bg-info text-white p-2 rounded text-center">{{ "Trips : ".$user->trips->count() }}</div>
                                    </div>
                                    <div class="">
                                        <button class="btn btn-sm btn-secondary" id="user_day" onclick="userChart('day',{{ $user->id }})">D</button>
                                        <button class="btn btn-sm btn-secondary" id="user_week" onclick="userChart('week',{{ $user->id }})">W</button>
                                        <button class="btn btn-sm btn-secondary" id="user_month" onclick="userChart('month',{{ $user->id }})">M</button>
                                        <button class="btn btn-sm btn-secondary" id="user_year" onclick="userChart('year',{{ $user->id }})">Y</button>
                                    </div>
                                </div>
                                <div style="width: 100%;"><canvas id="userChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row align-items-stretch">
                <div class="col-md-6">
                    <div class="card mb-3 h-100">
                        <div class="card-body">
                            <span class=""><i class="fa-solid fa-wallet me-2"></i> TOPUP HISTORY</span>
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">NO</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $key => $transaction)
                                            <tr>
                                                <td class="text-muted">{{ $loop->index + 1 }}</td>
                                                <td class="text-muted">{{ $transaction->amount }}</td>
                                                <td class="text-muted">{{ Carbon\Carbon::parse($transaction->created_at)->format('F j,Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body position-relative p-3">
                            <nav class="row m-0 position-absolute bottom-0 end-0 me-3">
                                <ul class="pagination pagination-sm justify-content-end p-0 small">
                                    <li class="page-item {{ $transactions->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $transactions->previousPageUrl() }}" rel="prev">&laquo;</a>
                                    </li>

                                    <li class="page-item active">
                                        <a class="page-link"
                                            href="{{ $transactions->url($transactions->currentPage()) }}">{{ $transactions->currentPage() }}</a>
                                    </li>

                                    <li class="page-item {{ $transactions->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $transactions->nextPageUrl() }}" rel="next">&raquo;</a>
                                    </li>
                                </ul>
                            </nav>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3 h-100">
                        <div class="card-body">
                            <span class=""><i class="fa-sharp fa-solid fa-location-dot me-2"></i> TRIP HISTORY</span>
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">NO</th>
                                            <th scope="col">Distance (Km)</th>
                                            <th scope="col">Total Cost (Ks)</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($trips as $key => $trip)
                                            <tr>
                                                <td class="text-muted">{{ $loop->index + 1 }}</td>
                                                <td class="text-muted">{{ $trip->distance }}</td>
                                                <td class="text-muted">{{ $trip->total_cost }}</td>
                                                <td class="text-muted">{{ Carbon\Carbon::parse($trip->created_at)->format('F j,Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body">

                            <nav class="d-flex flex-row m-0 me-3 justify-content-between align-items-center">
                                <div class="">
                                Total Trips :{{ $tripsCount }}
                                </div>
                                <div class="">
                                    <ul class="pagination pagination-sm justify-content-end m-0 p-0 small">
                                        <li class="page-item {{ $trips->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $trips->previousPageUrl() }}" rel="prev">&laquo;</a>
                                        </li>

                                        <li class="page-item active">
                                            <a class="page-link" href="{{ $trips->url($trips->currentPage()) }}">{{ $trips->currentPage() }}</a>
                                        </li>

                                        <li class="page-item {{ $trips->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $trips->nextPageUrl() }}" rel="next">&raquo;</a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
        const domain = window.location.href;
        let chartInstance = null;

        async function userChart(range,userId) {
            await axios.get(`${domain}/chart/${range}`)
                .then((response) => {
                    const data = response.data.transactions;
                    let arr = Object.keys(data).map((key) => {
                        return {
                            date: data[key].date,
                            topup: data[key].topup,
                            commission: data[key].commission,
                        };
                    });
                    if (!chartInstance) {
                        // Create the chart instance if it doesn't exist
                        const chartElement = document.getElementById("userChart");
                        chartInstance = new Chart(chartElement, {
                            type: 'line',
                            data: {
                                labels: arr.map((row) => row.date),
                                datasets: [
                                    {
                                        label: `Topup per ${range}`,
                                        data: arr.map((row) => row.topup),
                                        borderColor: '#ffc107',
                                        backgroundColor: '#ffc107',
                                        tension: 0.2,
                                    },
                                    {
                                        label: `Commission per ${range}`,
                                        data: arr.map((row) => row.commission),
                                        borderColor: '#00C4FF',
                                        backgroundColor: '#30A2FF',
                                        tension: 0.2,
                                    },
                                ],
                            },
                        });
                    } else {
                        // Update the existing chart instance with new data
                        chartInstance.data.labels = arr.map((row) => row.date);
                        chartInstance.data.datasets[0].label = `Topup per ${range}`;
                        chartInstance.data.datasets[1].label = `Commission per ${range}`;
                        chartInstance.data.datasets[0].data = arr.map((row) => row.topup);
                        chartInstance.data.datasets[1].data = arr.map((row) => row.commission);

                        chartInstance.update();
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
                const ranges = ['day', 'week', 'month', 'year'];
                    ranges.forEach((rag) => {
                        document.querySelector(`#user_${rag}`).classList.remove('btn-dark');
                        document.querySelector(`#user_${rag}`).classList.add('btn-secondary');
                    });

                    document.querySelector(`#user_${range}`).classList.add('btn-dark');
                    document.querySelector(`#user_${range}`).classList.remove('btn-secondary');
        }

        window.onload = function () {
            userChart('month',{{ $user->id }});

        };

        function copyToClipboard(id) {
            const text = document.querySelector(`#${id}`).innerText;
            navigator.clipboard.writeText(text)
                .then(() => {
                console.log('Text copied to clipboard',text);
                 document.querySelector('#copy_btn').classList.remove('fa-clipboard');
                 document.querySelector('#copy_btn').classList.add('fa-copy');
                 setTimeout(() => {
                    document.querySelector('#copy_btn').classList.add('fa-clipboard');
                    document.querySelector('#copy_btn').classList.remove('fa-copy');
                 }, 5000);
                })
                .catch((error) => {
                console.error('Error copying text to clipboard:', error);
                });
        }
    </script>
@endpush
