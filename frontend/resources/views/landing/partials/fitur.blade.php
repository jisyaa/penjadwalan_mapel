@extends('landing.index')

@section('section-top')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>Fitur Unggulan</h1>
                    <ul>
                        <li><a href="{{ route('landing') }}">Home</a></li>
                        <li> / Fitur</li>
                    </ul>
                </div><!-- //.HERO-TEXT -->
            </div><!--- END COL -->
        </div><!--- END CONTAINER -->
    </section>
@endsection

@section('content')
    <!-- START FEATURES -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title text-center">
                <h1>Fitur Canggih Sistem Penjadwalan</h1>
                <p>Berbagai fitur unggulan untuk memudahkan pengelolaan jadwal pelajaran</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <h3>Algoritma Genetika</h3>
                        <p>Penjadwalan otomatis menggunakan algoritma genetika canggih untuk menghasilkan jadwal optimal dengan fitness terbaik.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <h3>Bebas Bentrok</h3>
                        <p>Sistem menjamin tidak ada bentrok guru di jam yang sama untuk kelas yang berbeda, memastikan jadwal berjalan lancar.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3>Analisis Beban Guru</h3>
                        <p>Pantau beban mengajar guru secara real-time dan identifikasi guru overload atau underload untuk pemerataan tugas.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <h3>Edit Manual</h3>
                        <p>Ubah jadwal secara manual dengan dropdown interaktif jika diperlukan penyesuaian. Perubahan langsung tersimpan.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-history"></i>
                        </div>
                        <h3>History Jadwal</h3>
                        <p>Simpan dan lihat riwayat jadwal yang pernah digenerate beserta nilai fitness. Kapan saja bisa diakses kembali.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-xs-12 mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.6s" data-wow-offset="0">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h3>Dashboard Analitik</h3>
                        <p>Visualisasi data untuk memantau efektivitas penjadwalan, termasuk distribusi beban guru dan tingkat bentrok.</p>
                        <a href="#" class="learn-more">Pelajari <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END FEATURES -->

    <!-- START WHY CHOOSE US -->
    <section class="marketing_content_area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="ab_content">
                        <h4>Keunggulan Sistem</h4>
                        <h2>Mengapa Memilih EduSchedule?</h2>
                        <p>EduSchedule hadir sebagai solusi penjadwalan yang dirancang khusus untuk memenuhi kebutuhan sekolah dalam mengelola jadwal pelajaran secara efisien dan efektif.</p>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="abmv">
                                <i class="fa-solid fa-clock"></i>
                                <h4>Hemat Waktu</h4>
                                <p>Penjadwalan otomatis mengurangi waktu penyusunan jadwal dari mingguan menjadi menit.</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="abmv">
                                <i class="fa-solid fa-brain"></i>
                                <h4>Optimal</h4>
                                <p>Algoritma genetika memastikan jadwal yang dihasilkan optimal dan bebas konflik.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="abmv">
                                <i class="fa-solid fa-mobile-alt"></i>
                                <h4>User Friendly</h4>
                                <p>Antarmuka intuitif memudahkan admin dan guru dalam mengakses dan mengelola jadwal.</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="abmv">
                                <i class="fa-solid fa-headset"></i>
                                <h4>Support 24/7</h4>
                                <p>Tim support siap membantu kapan saja jika ada kendala dalam penggunaan sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="ab_img">
                        <img src="{{ asset('landing/images/all-img/why-choose-us.png') }}" class="img-fluid" alt="Keunggulan Sistem">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END WHY CHOOSE US -->

    <!-- START HOW IT WORKS -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="section-title text-center">
                <h4>Panduan</h4>
                <h1>Bagaimana Cara Kerjanya?</h1>
                <p>Ikuti langkah mudah berikut untuk mulai menggunakan sistem</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12 text-center mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <h4>Input Data</h4>
                        <p>Masukkan data guru, mata pelajaran, kelas, dan waktu yang tersedia.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 text-center mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <h4>Generate Jadwal</h4>
                        <p>Sistem akan memproses data menggunakan algoritma genetika.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 text-center mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <h4>Review & Edit</h4>
                        <p>Lihat hasil jadwal, lakukan penyesuaian manual jika diperlukan.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 text-center mb-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" data-wow-offset="0">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <i class="fa-solid fa-save"></i>
                        </div>
                        <h4>Simpan & Gunakan</h4>
                        <p>Simpan jadwal dan gunakan untuk kegiatan belajar mengajar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END HOW IT WORKS -->

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

    <style>
        /* Feature Card Style */
        .feature-card {
            background: white;
            padding: 35px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .feature-icon i {
            font-size: 40px;
            color: white;
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .learn-more {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: gap 0.3s;
        }

        .learn-more:hover {
            gap: 12px;
            color: #764ba2;
        }

        /* Step Card Style */
        .step-card {
            background: white;
            padding: 30px 20px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .step-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 20px;
        }

        .step-card h4 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        .step-card p {
            color: #666;
            font-size: 14px;
        }

        /* Abmv Style */
        .abmv i {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .bg-light {
            background-color: #f8fafc;
        }
    </style>
@endsection
