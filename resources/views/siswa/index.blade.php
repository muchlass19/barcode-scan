@extends('layouts.layout')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Dashboard Siswa</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Siswa</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div id="persentaseKehadiran" class="apex-charts"></div>
            <div class="row">
                <div class="col-6">
                    <div class="text-center">
                    <p
                        class="text-muted text-uppercase mb-0 fw-medium fs-13"
                    >
                        Hadir
                    </p>
                    <h5 class="mt-1 mb-0 fw-medium">{{ $persentaseKehadiran }}%</h5>
                    </div>
                </div>
                <!--end col-->
                <div class="col-6">
                    <div class="text-center">
                    <p
                        class="text-muted text-uppercase mb-0 fw-medium fs-13"
                    >
                        Tidak Hadir
                    </p>
                    <h5 class="mt-1 mb-0 fw-medium">{{ $persentaseAbsen }}%</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
          series: [{{ $persentaseKehadiran }}],
          chart: {
            type: 'radialBar',
            offsetY: -20,
            sparkline: {
                enabled: true
            }
          },
          plotOptions: {
            radialBar: {
              startAngle: -90,
              endAngle: 90,
              hollow: {
                size: '75%',
                position: 'front',
              },
              track: {
                background: ["rgba(42, 118, 244, .18)"],
                strokeWidth: '80%',
                opacity: 0.5,
                margin: 5,
                lineCap: 'round'
              },
              dataLabels: {
                name: { show: false },
                value: {
                    offsetY: -2,
                    fontSize: '20px'
                }
              }
            }
          },
          stroke: { lineCap: 'round' },
          colors: ["var(--bs-primary)"],
          grid: { padding: { top: -10 } },
          labels: ['Average Results'],
        };
    
        var chart = new ApexCharts(document.querySelector("#persentaseKehadiran"), options);
        chart.render();
    })
</script>

@endpush

@endsection