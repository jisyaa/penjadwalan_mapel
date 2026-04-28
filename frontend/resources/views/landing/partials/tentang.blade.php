@extends('landing.index')

@section('section-top')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>Tentang Kami</h1>
                    <ul>
                        <li><a href="{{ route('landing') }}">Home</a></li>
                        <li> / Tentang</li>
                    </ul>
                </div><!-- //.HERO-TEXT -->
            </div><!--- END COL -->
        </div><!--- END CONTAINER -->
    </section>
@endsection

@section('content')
    <!-- START ABOUT US -->
    <section class="ab_one section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="ab_content">
                        <h2>Sistem Penjadwalan Mata Pelajaran SMPN 1 Enam Lingkung</h2>
                        <p>EduSchedule adalah sistem penjadwalan mata pelajaran berbasis algoritma genetika yang dirancang khusus untuk membantu sekolah dalam menyusun jadwal pelajaran secara otomatis, efisien, dan bebas konflik.</p>
                        <p>Sistem ini dikembangkan untuk mengatasi permasalahan bentrok jadwal, distribusi beban guru yang tidak merata, dan kesulitan dalam mengelola perubahan jadwal.</p>
                    </div>
                    <div class="abmv">
                        <img src="{{ asset('landing/images/all-img/light.svg') }}" alt="" />
                        <h4>Penjadwalan Otomatis</h4>
                        <p>Menggunakan algoritma genetika untuk menghasilkan jadwal optimal tanpa bentrok.</p>
                    </div>
                    <div class="abmv">
                        <img src="{{ asset('landing/images/all-img/target.svg') }}" alt="" />
                        <h4>Monitoring Real-time</h4>
                        <p>Pantau beban guru, bentrok jadwal, dan pemenuhan jam mapel.</p>
                    </div>
                    <div class="cta_two">
                        <a href="{{ route('fitur') }}" class="cta"><span>Lihat Fitur</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </a>
                    </div>
                </div><!--- END COL -->
                <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="ab_img">
                        <img src="{{ asset('landing/images/all-img/about-schedule.png') }}" class="img-fluid" alt="Sistem Penjadwalan">
                    </div>
                </div><!--- END COL -->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
    <!-- END ABOUT US -->

    <!-- START VISI MISI -->
    <section class="marketing_content_area section-padding">
        <div class="container">
            <div class="section-title">
                <h4>Visi & Misi</h4>
                <h1>Tujuan Pengembangan Sistem</h1>
            </div>
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="single_feature_one">
                        <div class="sf_top">
                            <i class="fa-solid fa-eye"></i>
                            <h2>Visi</h2>
                        </div>
                        <p>Menjadi sistem penjadwalan terdepan yang membantu institusi pendidikan mengelola jadwal dengan mudah, cepat, dan efisien.</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-6 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="single_feature_one">
                        <div class="sf_top">
                            <i class="fa-solid fa-flag-checkered"></i>
                            <h2>Misi</h2>
                        </div>
                        <p>Menyediakan sistem penjadwalan otomatis yang akurat, meminimalisir bentrok jadwal guru, dan memudahkan admin dalam mengelola perubahan jadwal.</p>
                    </div>
                </div><!-- END COL -->
            </div><!-- END ROW -->
        </div><!-- END CONTAINER -->
    </section>
    <!-- END VISI MISI -->

    <!-- START VIDEO DEMO -->
    <section class="vid_area va2" style="background-image: url('{{ asset('landing/images/banner/video.jpg') }}'); background-size:cover; background-position: center center;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 vp_top wow fadeInUDown" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="video-area2">
                        <a href="https://www.youtube.com/watch?v=RXv_uIN6e-Y" class="magnific_popup video-button"><i class="fa fa-play"></i></a>
                    </div>
                </div><!--- END COL -->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
    <!-- END VIDEO DEMO -->

    <!-- START COUNTER (STATISTIK SISTEM) -->
    <section class="count_area counter_feature">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_one">
                        <span class="ti-folder sc_one"></span>
                        <h2 class="counter-num">19</h2>
                        <p>Total Kelas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_two">
                        <span class="ti-medall-alt sc_two"></span>
                        <h2 class="counter-num">30</h2>
                        <p>Total Guru</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_three">
                        <span class="ti-id-badge sc_three"></span>
                        <h2 class="counter-num">11</h2>
                        <p>Mata Pelajaran</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="single-counter count_four">
                        <span class="ti-user sc_four"></span>
                        <h2 class="counter-num">40</h2>
                        <p>Jam/Minggu</p>
                    </div>
                </div><!-- END COL -->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
    <!-- END COUNTER -->
@endsection
