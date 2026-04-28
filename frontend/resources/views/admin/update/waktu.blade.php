@extends('admin.index')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Waktu</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('waktu') }}">Data Waktu</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Form Waktu
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Edit Data Waktu</h4>
                    <form class="forms-sample" method="post"
                        action="{{ route('waktu.update', ['id' => $data_waktu->id_waktu]) }}" enctype="multipart/form-data">
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
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="hari">Hari</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="hari" id="hari">
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" {{ $data_waktu->hari == 'Senin' ? 'selected' : '' }}>
                                        Senin
                                    </option>
                                    <option value="Selasa" {{ $data_waktu->hari == 'Selasa' ? 'selected' : '' }}>
                                        Selasa
                                    </option>
                                    <option value="Rabu" {{ $data_waktu->hari == 'Rabu' ? 'selected' : '' }}>
                                        Rabu
                                    </option>
                                    <option value="Kamis" {{ $data_waktu->hari == 'Kamis' ? 'selected' : '' }}>
                                        Kamis
                                    </option>
                                    <option value="Jumat" {{ $data_waktu->hari == 'Jumat' ? 'selected' : '' }}>
                                        Jumat
                                    </option>
                                    <option value="Sabtu" {{ $data_waktu->hari == 'Sabtu' ? 'selected' : '' }}>
                                        Sabtu
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="jam_ke">Jam Ke</label>
                            <input type="number" class="form-control" id="jam_ke" name="jam_ke"
                                placeholder ="Jam Ke" value="{{ $data_waktu->jam_ke }}">
                        </div>
                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai</label>
                            <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai"
                                placeholder ="Waktu Mulai" value="{{ $data_waktu->waktu_mulai }}">
                        </div>
                        <div class="form-group">
                            <label for="waktu_selesai">Waktu Selesai</label>
                            <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai"
                                placeholder ="Waktu Selesai" value="{{ $data_waktu->waktu_selesai }}">
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan"
                                placeholder ="Keterangan" value="{{ $data_waktu->keterangan }}">
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <a href="{{ route('waktu') }}" class="btn btn-dark">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    @endsection
