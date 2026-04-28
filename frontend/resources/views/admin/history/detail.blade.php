@extends('admin.index')

@include('admin.history.partials.styles')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title mb-0">Detail Jadwal</h3>
            <a href="{{ route('history.jadwal.index') }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @include('admin.history.partials.info-master')

    @if (isset($jadwal) && !empty($jadwal))
        @if ($isAktif)
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-success" id="btnSaveAllChanges" style="display: none;">
                        <i class="mdi mdi-content-save"></i> Simpan Semua Perubahan (<span id="changeCount">0</span>)
                    </button>
                    <button type="button" class="btn btn-warning ms-2" id="btnCancelAllChanges" style="display: none;">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                </div>
            </div>
        @endif

        @include('admin.history.partials.table-jadwal')

        <!-- Tab Container -->
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

            <div id="tab-bentrok" class="tab-content active">
                <div id="bentrok-content">
                    <div class="text-center">Memuat data bentrok...</div>
                </div>
            </div>

            <div id="tab-jam-mapel" class="tab-content">
                @include('admin.history.partials.tab-jam-mapel', ['jadwal' => $jadwal])
            </div>

            <div id="tab-beban-guru" class="tab-content">
                @include('admin.history.partials.tab-beban-guru', ['jadwal' => $jadwal])
            </div>
        </div>
    @endif
@endsection

@include('admin.history.partials.scripts')
