@extends('admin.index')

@include('admin.generate-jadwal.partials.styles')

@section('content')
    @include('admin.generate-jadwal.partials.header')

    @if (isset($jadwal) && !empty($jadwal))
        <form method="POST" action="{{ route('generate-jadwal.simpan') }}">
            @csrf

            @include('admin.generate-jadwal.partials.form')

            <div class="row">
                <div class="grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Hasil Generate Baru</h4>
                            </div>

                            @include('admin.generate-jadwal.partials.table-jadwal')

                            <div class="tab-container">
                                <div class="tab-buttons">
                                    <button class="tab-btn active" onclick="switchTab('tab-bentrok')">
                                        <i class="mdi mdi-alert"></i> Informasi Bentrok Guru
                                    </button>
                                    <button class="tab-btn" onclick="switchTab('tab-jam-mapel')">
                                        <i class="mdi mdi-chart-bar"></i> Analisis Jam Mapel
                                    </button>
                                    <button class="tab-btn" onclick="switchTab('tab-beban-guru')">
                                        <i class="mdi mdi-account"></i> Beban Guru
                                    </button>
                                </div>

                                @include('admin.generate-jadwal.partials.tab-bentrok')
                                @include('admin.generate-jadwal.partials.tab-jam-mapel')
                                @include('admin.generate-jadwal.partials.tab-beban-guru')

                                <div class="alert alert-warning mt-2">
                                    <strong>💡 Catatan:</strong> Beban guru idealnya 20-24 jam per minggu.
                                    Jika ada guru dengan beban terlalu tinggi atau rendah, sesuaikan data guru_mapel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <button class="btn btn-success">
                <i class="mdi mdi-content-save"></i> Simpan Jadwal
            </button>
        </form>

        @if (isset($fitness_history) && count($fitness_history) > 0)
            @include('admin.generate-jadwal.partials.chart-script')
        @endif
    @else
        <div class="text-center mt-5">
            <div class="alert alert-info">
                <h4>Belum Ada Jadwal</h4>
                <p>Klik tombol "Generate Jadwal Baru" di atas untuk menghasilkan jadwal menggunakan algoritma genetika.</p>
            </div>
        </div>
    @endif
@endsection

@include('admin.generate-jadwal.partials.scripts')
