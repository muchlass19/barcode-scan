@extends('layouts.auth')
@section('content')
<div class="col-12 align-self-center">
    <div class="card-body">
    <div class="row">
        <div class="col-lg-4 mx-auto">
        <div class="card">
            <div
            class="card-body p-0 rounded-top"
            >
            </div>
            <div class="card-body pt-0">
            <form class="my-4" action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="form-group mb-2">
                <label class="form-label" for="username"
                    >NISN/NIP</label
                >
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="NISN untuk Siswa, NIP untuk Guru"
                />
                </div>
                <!--end form-group-->

                <div class="form-group">
                <label class="form-label" for="userpassword"
                    >Kata Sandi</label
                >
                <input
                    type="password"
                    class="form-control"
                    name="password"
                    id="userpassword"
                    placeholder="Masukkan Kata Sandi"
                />
                </div>
                <!--end form-group-->

                <div class="form-group mb-0 row">
                <div class="col-12">
                    <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary" type="button">
                        Masuk <i class="fas fa-sign-in-alt ms-1"></i>
                    </button>
                    </div>
                </div>
                <!--end col-->
                </div>
                <!--end form-group-->
            </form>
            <!--end form-->
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    </div>
    <!--end card-body-->
</div>
<!--end col-->
</div>
@endsection