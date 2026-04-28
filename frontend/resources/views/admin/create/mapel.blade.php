@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Mapel</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('mapel') }}">Data Mapel</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Mapel
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tambah Data Mapel</h4>
                    <form class="forms-sample" method="post" action="{{ route('mapel.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="nama_mapel">Nama Mapel</label>
                            <input type="text" class="form-control" id="nama_mapel" name="nama_mapel"
                                placeholder="Nama Mapel">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="jam_per_minggu">Jam Per Minggu</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="jam_per_minggu" id="jam_per_minggu">
                                    <option value="">-- Pilih Per Minggu --</option>
                                    <option value="1">1</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="kategori">Kategori</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="kategori" id="kategori">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Praktek">Praktek</option>
                                    <option value="Teori">Teori</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('mapel') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
