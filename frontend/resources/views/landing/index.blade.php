<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Penjadwalan SMPN 1 Enam LIngkung">
    <meta name="keywords"
        content="theme_ocean, college, course, e-learning, education, high school, kids, learning, online, online courses, school, student, teacher, tutor, university">
    <meta name="author" content="theme_ocean">
    <!-- SITE TITLE -->
    <title>Penjadwalan SMPN 1 Enam LIngkung</title>
    <!-- Latest Bootstrap min CSS -->
    <link rel="stylesheet" href="{{ asset('landing/bootstrap/css/bootstrap.min.css') }}">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="{{ asset('landing/webfonts/themify-icons.css') }}">
    <!-- All Min Css -->
    <link rel="stylesheet" href="{{ asset('landing/css/all.min.css') }}">
    <!--- owl carousel Css-->
    <link rel="stylesheet" href="{{ asset('landing/owlcarousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/owlcarousel/css/owl.theme.css') }}">
    <!--slicknav Css-->
    <link rel="stylesheet" href="{{ asset('landing/css/slicknav.css') }}">
    <!-- MAGNIFIC CSS -->
    <link rel="stylesheet" href="{{ asset('landing/css/magnific-popup.css') }}">
    <!--jquery-simple-mobilemenu Css-->
    <link rel="stylesheet" href="{{ asset('landing/css/jquery-simple-mobilemenu.css') }}">
    <!-- animate CSS -->
    <link rel="stylesheet" href="{{ asset('landing/css/animate.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}" />

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>

    <!-- START PRELOADER -->
    <div class="preloaders">
        <span class="loader">Loading</span>
    </div>
    <!-- END PRELOADER -->

    <!-- START TOP HEADER CLASS -->
    <div class="top_header_banner2">
        <!-- START LOGO WITH CONTACT -->
        @include('landing.tools.logo-contact')
        <!-- END LOGO WITH CONTACT -->

        <!-- START NAVBAR -->
        @include('landing.tools.navbar')
        <!-- END NAVBAR -->

        <!-- START SECTION TOP -->
        @yield('section-top')

        <!-- END  SECTION TOP -->
    </div>
    <!-- END  TOP HEADER CLASS -->

    @yield('content')

    <!-- START FOOTER -->
    @include('landing.tools.footer')
    <!-- END FOOTER -->

    <!-- Latest jQuery -->
    <script src="{{ asset('landing/js/jquery-1.12.4.min.js') }}"></script>
    <!-- Latest compiled and minified Bootstrap -->
    <script src="{{ asset('landing/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- owl-carousel min js  -->
    <script src="{{ asset('landing/owlcarousel/js/owl.carousel.min.js') }}"></script>
    <!-- jquery-simple-mobilemenu.min -->
    <script src="{{ asset('landing/js/jquery-simple-mobilemenu.js') }}"></script>
    <!-- magnific-popup js -->
    <script src="{{ asset('landing/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- jquery mixitup min js -->
    <script src="{{ asset('landing/js/jquery.mixitup.js') }}"></script>
    <!-- GSAP AND LOCOMOTIV JS-->
    <script src="{{ asset('landing/js/gsap.min.js') }}"></script>
    <script src="{{ asset('landing/js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('landing/js/lenis.js') }}"></script>
    <!-- scrolltopcontrol js -->
    <script src="{{ asset('landing/js/scrolltopcontrol.js') }}"></script>
    <!-- jquery inview js -->
    <script src="{{ asset('landing/js/jquery.inview.min.js') }}"></script>
    <!-- WOW - Reveal Animations When You Scroll -->
    <script src="{{ asset('landing/js/wow.min.js') }}"></script>
    <!-- scripts js -->
    <script src="{{ asset('landing/js/scripts.js') }}"></script>
</body>

</html>
