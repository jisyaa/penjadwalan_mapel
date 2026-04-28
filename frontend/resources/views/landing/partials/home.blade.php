@extends('landing.index')

@section('section-top')
    <section id="home" class="home_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xs-12">
                    <div class="home_content">
                        <h1>Penjadwalan Mata Pelajaran <br>SMPN 1 Enam Lingkung</h1>
                        <p>Solusi cerdas untuk mengelola jadwal pelajaran secara otomatis. Bebas bentrok, efisien, dan mudah digunakan oleh guru dan admin sekolah.</p>
                    </div>
                    <div class="home_btn">
                        <a href="{{ route('fitur') }}" class="cta me-2"><span>Jelajahi Fitur</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                        <a href="{{ route('jadwal.preview') }}" class="cta-outline">Lihat Demo</a>
                    </div>
                </div><!-- END COL-->
                <div class="col-lg-6 col-sm-6 col-xs-12">
                    <div class="home_me_img">
                        <img src="{{ asset('landing/images/all-img/home-image.png') }}" class="img-fluid" alt="" />
                        <div class="home_ps">
                            <img src="{{ asset('landing/images/icon/user2.svg') }}" alt="" />
                            <h2>19</h2>
                            <span>Kelas</span>
                        </div>
                        <div class="home_ps2">
                            <img src="{{ asset('landing/images/icon/file2.svg') }}" alt="" />
                            <h2>11</h2>
                            <span>Mata Pelajaran</span>
                        </div>
                    </div>
                </div><!-- END COL-->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
@endsection

@section('content')
    <!-- START COUNTER -->
    <section class="count_area counter_feature">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_one">
                        <span class="ti-folder sc_one"></span>
                        <h2 class="counter-num">{{ $totalKelas }}</h2>
                        <p>Total Kelas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_two">
                        <span class="ti-medall-alt sc_two"></span>
                        <h2 class="counter-num">{{ $totalGuru }}</h2>
                        <p>Total Guru</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_three">
                        <span class="ti-id-badge sc_three"></span>
                        <h2 class="counter-num">{{ $totalMapel }}</h2>
                        <p>Mata Pelajaran</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_four">
                        <span class="ti-user sc_four"></span>
                        <h2 class="counter-num">{{ $totalJadwal }}</h2>
                        <p>Jadwal Tersusun</p>
                    </div>
                </div><!-- END COL -->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
    <!-- END COUNTER -->

    <!-- START PREVIEW JADWAL SINGKAT -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title text-center">
                <h4>Preview Jadwal</h4>
                <h1>Contoh Jadwal Terbaru</h1>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previewJadwal as $item)
                                    <tr>
                                        <td>{{ $item->nama_kelas }}</td>
                                        <td>{{ $item->hari }}</td>
                                        <td>{{ $item->jam_ke }}</td>
                                        <td>{{ $item->nama_mapel }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Belum ada jadwal yang digenerate</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('jadwal.preview') }}" class="cta">Lihat Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END PREVIEW JADWAL -->

    <!-- START FITUR UTAMA -->
    <section class="tp_feature section-padding">
        <div class="container">
            <div class="section-title text-center">
                <h4>Fitur Unggulan</h4>
                <h1>Yang Membuat Sistem Ini Istimewa</h1>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                    <div class="single_tp st_two">
                        <h3>Penjadwalan Otomatis</h3>
                        <i class="fa-solid fa-robot"></i>
                        <p>Algoritma genetika menghasilkan jadwal optimal bebas bentrok.</p>
                        <a href="{{ route('fitur') }}" class="cta"><span>Learn More</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                    <div class="single_tp st_one">
                        <h3>Analisis Beban Guru</h3>
                        <i class="fa-solid fa-chart-line"></i>
                        <p>Pantau beban mengajar guru agar tetap ideal dan merata.</p>
                        <a href="{{ route('fitur') }}" class="cta"><span>Learn More</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                    <div class="single_tp st_two">
                        <h3>Edit Manual</h3>
                        <i class="fa-solid fa-pen-to-square"></i>
                        <p>Ubah jadwal secara manual dengan antarmuka interaktif.</p>
                        <a href="{{ route('fitur') }}" class="cta"><span>Learn More</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                    <div class="single_tp st_three">
                        <h3>History Jadwal</h3>
                        <i class="fa-solid fa-history"></i>
                        <p>Simpan dan lihat riwayat jadwal yang pernah digenerate.</p>
                        <a href="{{ route('fitur') }}" class="cta"><span>Learn More</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END FITUR UTAMA -->

    <!-- START CTA -->
    <section class="insfreecourse section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="single_ins">
                        <div class="single_ins_content">
                            <h1>Sistem Penjadwalan Internal Sekolah</h1>
                            <p>Digunakan untuk pengelolaan jadwal secara terstruktur oleh administrator dan staf terkait.</p>
                            <a href="{{ route('login') }}" class="cta"><span>Akses Dashboard</span>
                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                    <path d="M1,5 L11,5"></path>
                                    <polyline points="8 1 12 5 8 9"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END CTA -->
@endsection
