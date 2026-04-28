<div id="navigation" class="navbar-light bg-faded site-navigation">
    <div class="container">
        <div class="row">
            <div class="col-20 align-self-center">
                <div class="site-logo">
                    <a href="{{ route('landing') }}">
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="EduSchedule">
                    </a>
                </div>
            </div><!--- END Col -->

            <div class="col-60 d-flex">
                <nav id="main-menu">
                    <ul>
                        <li><a href="{{ route('landing') }}">Beranda</a></li>
                        <li><a href="{{ route('tentang') }}">Tentang</a></li>
                        <li><a href="{{ route('fitur') }}">Fitur</a></li>
                        <li><a href="{{ route('jadwal.preview') }}">Jadwal</a></li>
                        <li><a href="{{ route('statistik') }}">Statistik</a></li>
                        <li><a href="{{ route('kontak') }}">Kontak</a></li>
                    </ul>
                </nav>
            </div><!--- END Col -->

            <div class="col-30 d-none d-xl-block text-end align-self-center">
                <div class="call_to_action">
                    <a class="btn_two" href="{{ route('login') }}">Login <i class="fa-solid fa-arrow-right"></i></a>
                </div><!--- END SOCIAL PROFILE -->
            </div><!--- END Col -->
        </div><!--- END ROW -->
    </div><!--- END CONTAINER -->
</div>
