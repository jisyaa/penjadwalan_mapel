@extends('landing.index')

@section('section-top')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>Hubungi Kami</h1>
                    <ul>
                        <li><a href="{{ route('landing') }}">Home</a></li>
                        <li> / Kontak</li>
                    </ul>
                </div><!-- //.HERO-TEXT -->
            </div><!--- END COL -->
        </div><!--- END CONTAINER -->
    </section>
@endsection

@section('content')
    <!-- START ADDRESS -->
    <section class="address_area  section-padding">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s"
                    data-wow-offset="0">
                    <div class="single_address">
                        <i class="ti-map"></i>
                        <h4>Lokasi SMPN 1 Enam Lingkung</h4>
                        <p>97J5+5XG, Pakandangan, Kec. Enam Lingkung<br /> Kabupaten Padang Pariaman 25584</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="single_address">
                        <i class="ti-mobile"></i>
                        <h4>Telpon</h4>
                        <p>0751675550</p>
                    </div>
                </div><!-- END COL -->
                <div class="col-lg-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s"
                    data-wow-offset="0">
                    <div class="single_address">
                        <i class="ti-email"></i>
                        <h4>Kirim email</h4>
                        <p>smpn1el@gmail.com</p>
                    </div>
                </div><!-- END COL -->
            </div><!--- END ROW -->
        </div><!--- END CONTAINER -->
    </section>
    <!-- END ADDRESS -->

    <!-- CONTACT -->
    <div id="contact" class="contact_area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="section-title-two">
                        <h2>Kirim Pesan</h2>
                    </div>
                    <div class="contact">
                        <form class="form" name="enq" method="post" action="contact.php"
                            onsubmit="return validation();">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input type="text" name="name" class="form-control" required="required"
                                        placeholder="Nama Anda">
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="email" name="email" class="form-control" required="required"
                                        placeholder="Email Anda">
                                </div>
                                <div class="form-group col-md-12">
                                    <input type="text" name="subject" class="form-control" required="required"
                                        placeholder="Subjek">
                                </div>
                                <div class="form-group col-md-12">
                                    <textarea rows="6" name="message" class="form-control" required="required" placeholder="Pesan"></textarea>
                                </div>
                                <div class="col-md-12 text-center">
                                    <a href="#" class="cta"><span>Kirim Pesan</span>
                                        <svg width="13px" height="10px" viewBox="0 0 13 10">
                                            <path d="M1,5 L11,5"></path>
                                            <polyline points="8 1 12 5 8 9"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- END COL  -->
                <div class="col-lg-5 col-sm-12 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s"
                    data-wow-offset="0">
                    <div class="map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.5850565739242!2d100.25737167349669!3d-0.6195649352577938!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4e077fba393f3%3A0xb88f9b9d0892bae2!2sSMP%20Negeri%201%20Enam%20Lingkung!5e0!3m2!1sid!2sid!4v1777040800320!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div><!-- END COL  -->
            </div><!-- END ROW -->
        </div><!--- END CONTAINER -->
    </div>
    <!-- END CONTACT -->
@endsection
