<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="page-title mb-0">Generate Jadwal</h3>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="mb-3">
    @if (empty($jadwal))
        <a href="{{ route('generate-jadwal.run') }}" class="btn btn-success">
            <i class="mdi mdi-play"></i> Generate Jadwal
        </a>
    @else
        <a href="{{ route('generate-jadwal.run') }}" class="btn btn-primary"
            onclick="return confirm('Generate ulang jadwal? Data jadwal saat ini akan hilang.')">
            <i class="mdi mdi-refresh"></i> Generate Ulang Jadwal
        </a>
    @endif
</div>
