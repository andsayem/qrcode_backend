@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')

@section('content')

<style>
    .pro-card { 
        border-radius: 12px; 
        padding: 12px 16px; 
        color: #fff; 
        position: relative; 
        overflow: hidden; 
        box-shadow: 0 6px 18px rgba(0,0,0,0.12); 
        transition: 0.25s ease-in-out; 
        min-height: 120px; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
    }
    .pro-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.15); 
    }
    .pro-icon { 
        font-size: 36px; 
        opacity: 0.15; 
        position: absolute; 
        right: 15px; 
        bottom: 12px; 
    }
    .pro-title { 
        font-size: 14px; 
        font-weight: 600; 
        opacity: 0.85; 
        margin-bottom: 4px; 
    }
    .pro-value { 
        font-size: 20px; 
        font-weight: 700; 
        line-height: 24px; 
        display: flex; 
        flex-direction: column; 
        align-items: flex-start; 
    }
    .pro-value span { margin-bottom: 2px; }
    .table-small th, .table-small td { padding: 6px 8px; font-size: 13px; }
    .tk-badge { font-size: 13px; color: #fff; background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 6px; }
    /* simple fade-in animation */
    .fade-in { animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
</style>

<div class="container-fluid">
    <h2 style="font-weight:700;color:#A8237F;margin-bottom:15px;">Dashboard</h2>

    {{-- Top Cards --}}

     <div class="row">
        {{-- Technicians --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #6A11CB, #A8237F);">
                <div class="pro-title">Technicians</div>
                <div class="pro-value">
                    <span>{{ number_format($activeTechniciansCount) }}</span>
                    <small style="font-size:12px;">(Pending: {{ number_format($pendingTechniciansCount) }})</small>
                </div>
                <i class="icon-users pro-icon"></i>
            </div>
        </div>
        
        {{-- Redeem Process --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #1FA2FF, #A8237F);">
                <div class="pro-title">Screened points</div>
                <div class="pro-value">
                    <span>{{ number_format($totalScreenedpoints) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($totalScreenedpoints / 4)) }}</span>
                </div>
                <i class="icon-barcode pro-icon"></i>
            </div>
        </div>

        {{-- Current Point --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #38EF7D, #A8237F);">
                <div class="pro-title">Total Disbursement Point</div>
                <div class="pro-value">
                    <span>{{ number_format($totalDisbursementPoint) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($totalDisbursementPoint / 4)) }}</span>
                </div>
                <i class="icon-trophy pro-icon"></i>
            </div>
        </div>

        {{-- Redeem Pending --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #FF6A00, #A8237F);">
                <div class="pro-title">Not Screened points</div>
                <div class="pro-value">
                    <span>{{ number_format($notScreenedpoints) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($notScreenedpoints / 4)) }}</span>
                </div>
                <i class="icon-credit-card pro-icon"></i>
            </div>
        </div>

    </div>
    <div class="row">
       
            {{-- Current Point --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #38EF7D, #A8237F);">
                <div class="pro-title">Current Point</div>
                <div class="pro-value">
                    <span>{{ number_format($currentPoints) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($currentPoints / 4)) }}</span>
                </div>
                <i class="icon-trophy pro-icon"></i>
            </div>
        </div>
        {{-- Redeem Pending --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #FF6A00, #A8237F);">
                <div class="pro-title">Redeem Pending</div>
                <div class="pro-value">
                    <span>{{ number_format($redeemPendingPoints) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($redeemPendingPoints / 4)) }}</span>
                </div>
                <i class="icon-credit-card pro-icon"></i>
            </div>
        </div>
       

        {{-- Redeem Process --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #1FA2FF, #A8237F);">
                <div class="pro-title">Redeem Process</div>
                <div class="pro-value">
                    <span>{{ number_format($redeemProcessPoints) }}</span>
                    <span class="tk-badge">৳{{ number_format(intval($redeemProcessPoints / 4)) }}</span>
                </div>
                <i class="icon-barcode pro-icon"></i>
            </div>
        </div>

     

         {{-- Technicians --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="pro-card" style="background: linear-gradient(135deg, #6A11CB, #A8237F);">
                <div class="pro-title">Products</div>
                <div class="pro-value">
                    <span>{{ number_format($products) }}</span>
                    <small style="font-size:12px;">(Inactive: {{ number_format($inactiveProduct) }})</small>
                </div>
                <i class="icon-users pro-icon"></i>
            </div>
        </div>
    </div>

    {{-- Top Tables --}}
    <div class="row mt-2 fade-in">
        {{-- Top Technicians --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="pro-card" style="background:#fff;color:#333;">
                <div class="pro-title" style="color:#A8237F;">Top Technicians</div>
                <table class="table table-sm table-small mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Point Name</th>
                            <th>Point Code</th>
                            <th>Points</th>
                            <th>Redeem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topTechnicians as $tech)
                        <tr onclick="openPanel({{ $tech->user_id }})" style="cursor:pointer;"> 
                            <td>{{ $tech->name }}  </td>
                           <td>{{ \Illuminate\Support\Str::limit($tech->point_name, 20, ' ...') }}</td>
                            <td>{{ $tech->point_code }}</td>
                            <td>{{ number_format($tech->points) }}</td>
                            <td>{{ number_format($tech->redeem) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="pro-card" style="background:#fff;color:#333;">
                <div class="pro-title" style="color:#A8237F;">Top Products</div>
                <table class="table table-sm table-small mb-0 mt-1">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Total Scans</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ number_format($product->total_scans) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Monthly Charts --}}
    <div class="row mt-3 fade-in">
        {{-- Monthly Redeem Points Chart --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="pro-card" style="background:#fff;color:#333;">
                <div class="pro-title" style="color:#A8237F;">Monthly Disbursement Points</div>
                <canvas id="redeemChart" height="200"></canvas>

                {{-- Optional Table Under Chart --}}
                <table class="table table-sm table-small mt-3 mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Points</th>
                            <th>৳ Taka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyRedeem as $month => $points)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ number_format($points) }}</td>
                            <td>৳{{ number_format(intval($points / 4)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monthly Scanned Codes Chart --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="pro-card" style="background:#fff;color:#333;">
                <div class="pro-title" style="color:#A8237F;">Monthly Scanned Codes</div>
                <canvas id="scannedChart" height="200"></canvas>

                {{-- Optional Table Under Chart --}}
                <table class="table table-sm table-small mt-3 mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Scans</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyScanned as $month => $scans)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ number_format($scans) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side Popup Panel -->
<div id="sidePanel" class="side-panel">
    <div class="panel-header">
        <h4>Technician Details</h4>
        <button class="close-btn" onclick="closePanel()">×</button>
    </div>
    <div id="panelContent" class="panel-body">
        Loading...
    </div>
</div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyRedeem = @json($monthlyRedeem);
    const monthlyScanned = @json($monthlyScanned);

    const redeemLabels = Object.keys(monthlyRedeem);
    const redeemData = Object.values(monthlyRedeem);

    const scannedLabels = Object.keys(monthlyScanned);
    const scannedData = Object.values(monthlyScanned);

    // Redeem Points Chart
    new Chart(document.getElementById('redeemChart'), {
        type: 'bar',
        data: {
            labels: redeemLabels,
            datasets: [{
                label: 'Redeem Points',
                data: redeemData,
                backgroundColor: 'rgba(168,35,127,0.7)',
                borderColor: 'rgba(168,35,127,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Monthly Scanned Codes Chart
    new Chart(document.getElementById('scannedChart'), {
        type: 'bar',
        data: {
            labels: scannedLabels,
            datasets: [{
                label: 'Total Scans',
                data: scannedData,
                backgroundColor: 'rgba(26,162,255,0.7)',
                borderColor: 'rgba(26,162,255,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

function openPanel(user_id) {
    const panel = document.getElementById("sidePanel");
    const content = document.getElementById("panelContent");

    panel.style.right = "0px"; // slide in
    content.innerHTML = "Loading...";

    // AJAX request to fetch technician data
    $.ajax({
        url: `/api/technician/details/${user_id}`,
        method: "GET",
        success: function(response) {
            // Access the `data` object from API response
            const tech = response.data || {};

            content.innerHTML = `
                <div style="padding: 20px; font-family: Arial, sans-serif; color: #333;">
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #6A11CB, #A8237F); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 24px; margin-right: 15px;">
                            ${tech.name ? tech.name.charAt(0).toUpperCase() : '-'}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 20px; color: #A8237F;">${tech.name || '-'}</h3>
                            <small style="color: #777;">User ID: ${tech.user_id || '-'}</small>
                            <small style="color: #777;">Phone: ${tech.email || '-'}</small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Point Name</strong>
                            <p style="margin: 5px 0 0;">${tech.point_name || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Point Code</strong>
                            <p style="margin: 5px 0 0;">${tech.point_code || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Total Points</strong>
                            <p style="margin: 5px 0 0;">${tech.total_point?.toLocaleString() || '0'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Redeem Points</strong>
                            <p style="margin: 5px 0 0;">${tech.total_redeem_value?.toLocaleString() || '0'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Current Points</strong>
                            <p style="margin: 5px 0 0;">${tech.current_point?.toLocaleString() || '0'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Pending Points</strong>
                            <p style="margin: 5px 0 0;">${tech.pending_point?.toLocaleString() || '0'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Gateway Number</strong>
                            <p style="margin: 5px 0 0;">${tech.gatway_number || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Father Name</strong>
                            <p style="margin: 5px 0 0;">${tech.father_name || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Permanent Address</strong>
                            <p style="margin: 5px 0 0;">${tech.permanent_address || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Current Address</strong>
                            <p style="margin: 5px 0 0;">${tech.current_address || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>Birthday</strong>
                            <p style="margin: 5px 0 0;">${tech.birthday || '-'}</p>
                        </div>
                        <div style="background: #f7f7f7; padding: 10px; border-radius: 8px;">
                            <strong>NID Number</strong>
                            <p style="margin: 5px 0 0;">${tech.nid_number || '-'}</p>
                        </div>
                    </div>

                    <button onclick="closePanel()" style="margin-top: 20px; width: 100%; padding: 10px; background: #A8237F; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        Close
                    </button>
                </div>
            `;
        },
        error: function() {
            content.innerHTML = "<p style='color:red;'>Failed to load data.</p>";
        }
    });
}

    function closePanel() {
        document.getElementById("sidePanel").style.right = "-450px";
    }
</script>

@endsection
@extends('backend.layouts.footer')
<style>
    .side-panel {
    position: fixed;
    top: 0;
    right: -450px; /* initially hidden */
    width: 450px;
    height: 100%;
    background-color: #fff;
    box-shadow: -4px 0 12px rgba(0,0,0,0.25);
    transition: right 0.3s ease;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    border-left: 1px solid #ddd;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    background-color: #f7f7f7;
}

.panel-header h4 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.close-btn {
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    transition: color 0.2s;
}

.close-btn:hover {
    color: #ff0000;
}

.panel-body {
    padding: 20px;
    font-size: 14px;
    color: #555;
}

.panel-body p {
    margin: 10px 0;
}
    </style>