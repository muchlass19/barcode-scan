@extends('layouts.layout')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                    <h4 class="page-title">Dashboard Admin</h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col">
                <div class="card bg-corner-img">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col-9">
                                <p class="text-muted text-uppercase mb-0 fw-normal fs-13">
                                    Total Siswa
                                </p>
                                <h4 class="mt-1 mb-0 fw-medium">{{ $data['siswa'] }}</h4>
                            </div>
                            <div class="col-3 align-self-center">
                                <div class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                    <i class="iconoir-user fs-22 align-self-center mb-0 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card bg-corner-img">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col-9">
                                <p class="text-muted text-uppercase mb-0 fw-normal fs-13">
                                    Total Guru
                                </p>
                                <h4 class="mt-1 mb-0 fw-medium">{{ $data['guru'] }}</h4>
                            </div>
                            <div class="col-3 align-self-center">
                                <div class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                    <i class="iconoir-user fs-22 align-self-center mb-0 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card bg-corner-img">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col-9">
                                <p class="text-muted text-uppercase mb-0 fw-normal fs-13">
                                    Total Admin
                                </p>
                                <h4 class="mt-1 mb-0 fw-medium">{{ $data['admin'] }}</h4>
                            </div>
                            <div class="col-3 align-self-center">
                                <div class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                    <i class="iconoir-user fs-22 align-self-center mb-0 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card bg-corner-img">
                    <div class="card-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col-9">
                                <p class="text-muted text-uppercase mb-0 fw-normal fs-13">
                                    Total Kelas
                                </p>
                                <h4 class="mt-1 mb-0 fw-medium">{{ $data['kelas'] }}</h4>
                            </div>
                            <div class="col-3 align-self-center">
                                <div class="d-flex justify-content-center align-items-center thumb-md border-dashed border-primary rounded mx-auto">
                                    <i class="iconoir-book fs-22 align-self-center mb-0 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection