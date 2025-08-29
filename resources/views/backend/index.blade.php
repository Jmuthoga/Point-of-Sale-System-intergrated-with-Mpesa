@extends('backend.master')

@section('title', 'Dashboard')

@section('content')
<section class="content">
    @can('dashboard_view')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Welcome back, {{ Auth::user()->name }} 👋 to {{ readConfig('site_name') }}</h4>
            <div class="text-muted" id="dashboard-clock"></div>
        </div>

        <div class="row">
            {{-- Sale SubTotal --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-1">
                        <i class="fas fa-cog" data-toggle="tooltip" title="Total amount before discount"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sale Total</span>
                        <span class="info-box-number" id="subTotalValue">
                            {{ currency()->symbol ?? '' }} {{ number_format($sub_total, 2, '.', ',') }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="subTotalProgress" style="width: 80%"></div>
                        </div>
                        <small id="subTotalLabel">80% of target</small>
                    </div>
                </div>
            </div>

            {{-- Sale Discount --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1">
                        <i class="fas fa-thumbs-up"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sale Discount</span>
                        <span class="info-box-number" id="discountValue">
                            {{ currency()->symbol ?? '' }} {{ number_format($discount, 2, '.', ',') }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-info" id="discountProgress" style="width: 60%"></div>
                        </div>
                        <small id="discountLabel">60% of target</small>
                    </div>
                </div>
            </div>

            {{-- Sale --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1">
                        <i class="fas fa-shopping-cart"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sale</span>
                        <span class="info-box-number" id="saleValue">
                            {{ currency()->symbol ?? '' }} {{ number_format($total, 2, '.', ',') }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" id="saleProgress" style="width: 90%"></div>
                        </div>
                        <small id="saleLabel">90% of target</small>
                    </div>
                </div>
            </div>

            {{-- Sale Due --}}
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sale Due</span>
                        <span class="info-box-number" id="dueValue">
                            {{ currency()->symbol ?? '' }} {{ number_format($due, 2, '.', ',') }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-danger" id="dueProgress" style="width: 50%"></div>
                        </div>
                        <small id="dueLabel">50% due collected</small>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Simulate live increment every few seconds
            function animateValue(id, start, end, duration) {
                const obj = document.getElementById(id);
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerText = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: '{{ currency()->code ?? 'USD' }}'
                    }).format(progress * (end - start) + start);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }

            function updateProgressBar(id, labelId, value, max) {
                let percent = Math.min((value / max) * 100, 100);
                document.getElementById(id).style.width = percent + '%';
                document.getElementById(labelId).innerText = `${Math.round(percent)}% of target`;
            }

            function updateRealTimeClock() {
                const clock = document.getElementById('dashboard-clock');
                if (clock) {
                    const now = new Date();
                    clock.textContent = now.toLocaleTimeString('en-GB', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                animateValue('subTotalValue', 0, {{ $sub_total }}, 1500);
                animateValue('discountValue', 0, {{ $discount }}, 1500);
                animateValue('saleValue', 0, {{ $total }}, 1500);
                animateValue('dueValue', 0, {{ $due }}, 1500);

                updateProgressBar('subTotalProgress', 'subTotalLabel', {{ $sub_total }}, 100000);
                updateProgressBar('discountProgress', 'discountLabel', {{ $discount }}, 100000);
                updateProgressBar('saleProgress', 'saleLabel', {{ $total }}, 100000);
                updateProgressBar('dueProgress', 'dueLabel', {{ $due }}, 100000);

                updateRealTimeClock();
                setInterval(updateRealTimeClock, 1000);
            });
        </script>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $total_customer }}</h3>
                        <p>Customers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('backend.admin.customers.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $total_product }}</h3>
                        <p>Products</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('backend.admin.products.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3>{{ $total_order }}</h3>
                        <p>Sale</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('backend.admin.orders.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $total_sale_item }}</h3>
                        <p>Sale Item</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('backend.admin.orders.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daily Total Sales <small>{{ $dateRange }}</small></h5>
                        <div class="input-group w-auto">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="reservation" style="width: 180px;">
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="dailySaleLineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Total Sales <small>for {{ $currentYear }}</small></h5>
                    </div>
                    <div class="card-body">
                        <canvas id="barChartYear"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endcan
</section>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.min.js"></script>
<script>
    const dailySaleChart = document.getElementById('dailySaleLineChart');
    const barChartYear = document.getElementById('barChartYear');

    new Chart(dailySaleChart, {
        type: 'line',
        data: {
            labels: @json($dates),
            datasets: [{ label: 'Sales', data: @json($totalAmounts), borderWidth: 1 }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    new Chart(barChartYear, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [{ label: 'Sales', data: @json($totalAmountMonth), borderWidth: 1 }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.info-box-number, .small-box .inner h3').forEach(function (el) {
            let val = parseFloat(el.textContent.replace(/[^0-9.-]+/g, ""));
            if (!isNaN(val)) {
                const countUp = new CountUp(el, val, { duration: 2 });
                countUp.start();
            }
        });

        setInterval(function () {
            const now = new Date();
            document.getElementById('dashboard-clock').innerText = now.toLocaleString();
        }, 1000);

        $('[data-toggle="tooltip"]').tooltip();
    });

    $(function () {
        $('#reservation').daterangepicker().on('apply.daterangepicker', function (e, picker) {
            let selectedDateRange = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
            let url = new URL(window.location.href);
            url.searchParams.set('daterange', selectedDateRange);
            window.location.href = url.toString();
        });
    });
</script>
@endpush