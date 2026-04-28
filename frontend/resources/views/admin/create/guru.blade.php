@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Guru</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('guru') }}">Data Guru</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Guru
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tambah Data Guru</h4>
                    <form class="forms-sample" method="post" action="{{ route('guru.store') }}"
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
                            <label for="nama_guru">Nama Guru</label>
                            <input type="text" class="form-control" id="nama_guru" name="nama_guru"
                                placeholder="Nama Guru">
                        </div>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder ="NIP">
                        </div>
                        <div class="form-group">
                            <label for="jam_mingguan">Jam Mingguan</label>
                            <input type="number" class="form-control" id="jam_mingguan" name="jam_mingguan"
                                placeholder ="Jam Mingguan">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="mapel">Mata Pelajaran</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mapel" id="mapel">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <option value="Matematika">Matematika</option>
                                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                    <option value="IPA">IPA</option>
                                    <option value="IPS">IPS</option>
                                    <option value="Bahasa Inggris">Bahasa Inggris</option>
                                    <option value="PAI">PAI</option>
                                    <option value="Pendidikan Pancasila">Pendidikan Pancasila</option>
                                    <option value="PJOK">PJOK</option>
                                    <option value="Seni Budaya dan Prakarya">Seni Budaya dan Prakarya</option>
                                    <option value="Informatika">Informatika</option>
                                    <option value="BK">BK</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('guru') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
