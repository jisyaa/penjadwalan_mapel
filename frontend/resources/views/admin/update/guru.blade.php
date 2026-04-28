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
                    <h4 class="card-title">Form Edit Data Guru</h4>
                    <form class="forms-sample" method="post"
                        action="{{ route('guru.update', ['id' => $data_guru->id_guru]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                                placeholder="Nama Guru" value="{{ $data_guru->nama_guru }}">
                        </div>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder ="NIP"
                                value="{{ $data_guru->nip }}">
                        </div>
                        <div class="form-group">
                            <label for="jam_mingguan">Jam Mingguan</label>
                            <input type="number" class="form-control" id="jam_mingguan" name="jam_mingguan"
                                placeholder ="Jam Mingguan" value="{{ $data_guru->jam_mingguan }}">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="mapel">Mata Pelajaran</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="mapel" id="mapel">
                                    <option value="">-- Pilih Mata Pelajaran --</option>

                                    <option value="Matematika" {{ $data_guru->mapel == 'Matematika' ? 'selected' : '' }}>
                                        Matematika
                                    </option>

                                    <option value="Bahasa Indonesia"
                                        {{ $data_guru->mapel == 'Bahasa Indonesia' ? 'selected' : '' }}>
                                        Bahasa Indonesia
                                    </option>

                                    <option value="IPA" {{ $data_guru->mapel == 'IPA' ? 'selected' : '' }}>
                                        IPA
                                    </option>

                                    <option value="IPS" {{ $data_guru->mapel == 'IPS' ? 'selected' : '' }}>
                                        IPS
                                    </option>

                                    <option value="Bahasa Inggris"
                                        {{ $data_guru->mapel == 'Bahasa Inggris' ? 'selected' : '' }}>
                                        Bahasa Inggris
                                    </option>

                                    <option value="PAI" {{ $data_guru->mapel == 'PAI' ? 'selected' : '' }}>
                                        PAI
                                    </option>

                                    <option value="Pendidikan Pancasila"
                                        {{ $data_guru->mapel == 'Pendidikan Pancasila' ? 'selected' : '' }}>
                                        Pendidikan Pancasila
                                    </option>

                                    <option value="PJOK" {{ $data_guru->mapel == 'PJOK' ? 'selected' : '' }}>
                                        PJOK
                                    </option>

                                    <option value="Seni Budaya dan Prakarya"
                                        {{ $data_guru->mapel == 'Seni Budaya dan Prakarya' ? 'selected' : '' }}>
                                        Seni Budaya dan Prakarya
                                    </option>

                                    <option value="Informatika" {{ $data_guru->mapel == 'Informatika' ? 'selected' : '' }}>
                                        Informatika
                                    </option>

                                    <option value="BK" {{ $data_guru->mapel == 'BK' ? 'selected' : '' }}>
                                        BK
                                    </option>
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
