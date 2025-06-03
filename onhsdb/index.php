<!DOCTYPE html>
<html lang="en">

<head>
    <title>Online Nurse Connekt | Home </title>
    
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!-- Custom Theme files -->
    <link href="css/bootstrap.css" type="text/css" rel="stylesheet" media="all">
    <link href="css/style.css" type="text/css" rel="stylesheet" media="all">
    <!-- font-awesome icons -->
    <link href="css/fontawesome-all.min.css" rel="stylesheet">
    <!-- //Custom Theme files -->
    <!-- online-fonts -->
    <link href="//fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
</head>

<body>
    <!-- banner -->
    <div class="banner" id="home">
        <!-- header -->
        <?php include_once("includes/navbar.php");?>
        <!-- //header -->
        <div class="container">
            <!-- banner-text -->
            <div class="banner-text">
                <div class="callbacks_container">
                    <ul class="rslides" id="slider3">
                        <li>
                            <div class="slider-info">
                                <span class="">providing total</span>
                                <h3>health care solution</h3>
                               
                            </div>
                        </li>
                        <li>
                            <div class="slider-info">
                                <span class="">providing total</span>
                                <h3>health care solution</h3>
                               
                            </div>
                        </li>
                        <li>
                            <div class="slider-info">
                                <span class="">providing total</span>
                                <h3>health care solution</h3>
                               
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- //container -->
    </div>
    <!-- //banner -->
    
    <!-- about -->
<div class="agileits-about py-md-5 py-5" id="services">
    <div class="container py-lg-5">
        <div class="title-section text-center pb-md-5">
            <h4>Online Nurse Connekt</h4>
            <h3 class="w3ls-title text-center text-capitalize">hospital that you can trust</h3>
        </div>
        <div class="agileits-about-row row text-center pt-md-0 pt-5">
            <div class="col-lg-4 col-sm-6 agileits-about-grids">
                <div class="p-md-5 p-sm-3">
                    <i class="fas fa-user-md"></i>
                    <h4 class="mt-2 mb-3">Therapist</h4>
                    <p>We provide trusted mental health support from experienced professionals.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 agileits-about-grids border-left border-right my-sm-0 my-5">
                <div class="p-md-5 p-sm-3">
                    <i class="fas fa-thermometer"></i>
                    <h4 class="mt-2 mb-3">Laboratory</h4>
                    <p>Our lab services offer accurate and timely diagnostics using advanced technology.</p>
                </div>
            </div>
            <div class="col-lg-4 agileits-about-grids">
                <div class="p-md-5 p-sm-3">
                    <i class="far fa-hospital"></i>
                    <h4 class="mt-2 mb-3">Surgery</h4>
                    <p>Expert surgical care in a safe and professional environment.</p>
                </div>
            </div>
        </div>
        <div class="agileits-about-row border-top row text-center pb-lg-5 pt-md-0 pt-5 mt-md-0 mt-5">
            <div class="col-lg-4 col-sm-6 agileits-about-grids">
                <div class="p-md-5 p-sm-3 col-label">
                    <i class="fas fa-hospital-symbol"></i>
                    <h4 class="mt-2 mb-3">Transplants</h4>
                    <p>Specialized in life-saving organ and tissue transplant procedures.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 agileits-about-grids mt-lg-0 mt-md-3 border-left border-right pt-sm-0 pt-5">
                <div class="p-md-5 p-sm-3 col-label">
                    <i class="fas fa-ambulance"></i>
                    <h4 class="mt-2 mb-3">Emergency Care</h4>
                    <p>24/7 emergency services ensuring fast and efficient medical response.</p>
                </div>
            </div>
            <div class="col-lg-4 agileits-about-grids pt-md-0 pt-5">
                <div class="p-md-5 p-sm-3 col-label">
                    <i class="fa fa-user-md"></i>
                    <h4 class="mt-2 mb-3">Oncology</h4>
                    <p>Comprehensive cancer care with compassionate treatment plans.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- //about -->
    
    <!-- footer -->
    <?php include_once("includes/footer.php");?>
    <!-- //footer -->
   
    <!-- js -->
    <script src="js/jquery-2.2.3.min.js"></script>
    <!-- //js -->
    <!-- Banner Responsiveslides -->
    <script src="js/responsiveslides.min.js"></script>
    <script>
        // You can also use "$(window).load(function() {"
        $(function () {
            // Slideshow 4
            $("#slider3").responsiveSlides({
                auto: false,
                pager: true,
                nav: false,
                speed: 500,
                namespace: "callbacks",
                before: function () {
                    $('.events').append("<li>before event fired.</li>");
                },
                after: function () {
                    $('.events').append("<li>after event fired.</li>");
                }
            });

        });
    </script>
    <!-- //banner responsive slides -->
    <!-- Flexslider-js for-testimonials -->
    <script src="js/jquery.flexisel.js"></script>
    <script>
        $(window).load(function () {
            $("#flexiselDemo1").flexisel({
                visibleItems: 1,
                animationSpeed: 1000,
                autoPlay: false,
                autoPlaySpeed: 3000,
                pauseOnHover: true,
                enableResponsiveBreakpoints: true,
                responsiveBreakpoints: {
                    portrait: {
                        changePoint: 480,
                        visibleItems: 1
                    },
                    landscape: {
                        changePoint: 640,
                        visibleItems: 1
                    },
                    tablet: {
                        changePoint: 768,
                        visibleItems: 1
                    }
                }
            });

        });
    </script>
    <!-- //Flexslider-js for-testimonials -->
    <!-- //fixed quick contact -->
    <script>
        $(function () {
            var hidden = true;
            $(".heading").click(function () {
                if (hidden) {
                    $(this).parent('.outer-col').animate({
                        bottom: "0"
                    }, 1200);
                } else {
                    $(this).parent('.outer-col').animate({
                        bottom: "-305px"
                    }, 1200);
                }
                hidden = !hidden;
            });
        });
    </script>
    <!-- //fixed quick contact -->
    <!-- start-smooth-scrolling -->
    <script src="js/easing.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            $(".scroll").click(function (event) {
                event.preventDefault();

                $('html,body').animate({
                    scrollTop: $(this.hash).offset().top
                }, 1000);
            });
        });
    </script>
    <script src="js/SmoothScroll.min.js"></script>
    <!-- //end-smooth-scrolling -->
    <!-- Bootstrap core JavaScript
================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/bootstrap.js"></script>
</body>

</html>