@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- KPI Row --}}
<div class="row row-deck row-cards anim-stagger mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="subheader">Total Revenue</div>
                    <div class="ms-auto">
                        <span class="badge bg-green-lt">
                            <i class="ti ti-trending-up me-1"></i>+12%
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="h1 mb-0 me-2">Rp 284,5 Jt</div>
                </div>
                <div class="mt-2 text-muted small">vs bulan lalu: Rp 254,2 Jt</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="subheader">Sales Order</div>
                    <div class="ms-auto">
                        <span class="badge bg-green-lt">
                            <i class="ti ti-trending-up me-1"></i>+8%
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="h1 mb-0 me-2">132</div>
                    <div class="text-muted small">order</div>
                </div>
                <div class="mt-2 text-muted small">24 menunggu konfirmasi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="subheader">Invoice Outstanding</div>
                    <div class="ms-auto">
                        <span class="badge bg-red-lt">
                            <i class="ti ti-alert-circle me-1"></i>12 jatuh tempo
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="h1 mb-0 me-2">Rp 48,2 Jt</div>
                </div>
                <div class="mt-2 text-muted small">38 invoice belum dibayar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="subheader">Stok Menipis</div>
                    <div class="ms-auto">
                        <span class="badge bg-yellow-lt">
                            <i class="ti ti-alert-triangle me-1"></i>perlu reorder
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="h1 mb-0 me-2">7</div>
                    <div class="text-muted small">produk</div>
                </div>
                <div class="mt-2 text-muted small">Di bawah minimum stok</div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Activity --}}
<div class="row row-deck row-cards anim-stagger mb-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Revenue 6 Bulan Terakhir</h3>
            </div>
            <div class="card-body">
                <div id="chart-revenue" style="height: 220px"></div>
            </div>
        </div>
    </div>

    {{-- Sales by Category --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-chart-donut me-2"></i>Penjualan per Kategori</h3>
            </div>
            <div class="card-body">
                <div id="chart-category" style="height: 220px"></div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders + Low Stock --}}
<div class="row row-deck row-cards anim-stagger">
    {{-- Recent Orders --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-shopping-cart me-2"></i>Order Terbaru</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="anim-stagger">
                        @foreach([
                            ['SO-2026-0132', 'PT Maju Bersama', 'Rp 12.500.000', 'success', 'Selesai'],
                            ['SO-2026-0131', 'CV Karya Mandiri', 'Rp 8.750.000', 'warning', 'Diproses'],
                            ['SO-2026-0130', 'UD Sinar Jaya', 'Rp 4.200.000', 'warning', 'Diproses'],
                            ['SO-2026-0129', 'PT Global Tech', 'Rp 22.000.000', 'success', 'Selesai'],
                            ['SO-2026-0128', 'CV Bintang Mas', 'Rp 6.300.000', 'secondary', 'Draft'],
                        ] as $order)
                        <tr>
                            <td class="text-muted small">{{ $order[0] }}</td>
                            <td>{{ $order[1] }}</td>
                            <td class="fw-bold">{{ $order[2] }}</td>
                            <td><span class="badge bg-{{ $order[3] }}-lt">{{ $order[4] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Low Stock --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-alert-triangle me-2 text-warning"></i>Stok Menipis</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Stok</th>
                            <th>Min.</th>
                        </tr>
                    </thead>
                    <tbody class="anim-stagger">
                        @foreach([
                            ['Kertas HVS A4', 5, 20],
                            ['Tinta Printer Hitam', 2, 10],
                            ['Ballpoint Biru', 8, 50],
                            ['Map Plastik', 3, 25],
                            ['Amplop Coklat', 12, 100],
                            ['Stapler Besar', 1, 5],
                            ['Penggaris 30cm', 4, 15],
                        ] as $item)
                        <tr>
                            <td>{{ $item[0] }}</td>
                            <td>
                                <span class="fw-bold text-{{ $item[1] <= 5 ? 'danger' : 'warning' }}">
                                    {{ $item[1] }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $item[2] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Revenue Bar Chart
    new ApexCharts(document.getElementById('chart-revenue'), {
        series: [{ name: 'Revenue', data: [185, 210, 195, 240, 254, 284] }],
        chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'inherit' },
        colors: ['#206bc4'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'] },
        yaxis: { labels: { formatter: v => 'Rp ' + v + ' Jt' } },
        grid: { borderColor: '#e5e7eb' },
        tooltip: { y: { formatter: v => 'Rp ' + v + ' Juta' } },
    }).render();

    // Donut Chart
    new ApexCharts(document.getElementById('chart-category'), {
        series: [42, 28, 18, 12],
        chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
        labels: ['Elektronik', 'ATK', 'Furniture', 'Lainnya'],
        colors: ['#206bc4', '#4299e1', '#74c0fc', '#bdd7f5'],
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '12px' },
        plotOptions: { pie: { donut: { size: '65%' } } },
        tooltip: { y: { formatter: v => v + '%' } },
    }).render();
});
</script>
@endpush

@endsection
