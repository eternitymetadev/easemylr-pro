<!DOCTYPE html>
<html lang="en">

<head>
    <title>Eternity Forwarders</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->

    <link rel="stylesheet" href="{{ asset('assets/css/jquery.toast.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <style>
        @media only screen and (max-width: 479px) and (min-width: 0px) {
            .nn {
                background-image: none !important;
                background-color: aliceblue;
                height: 870px !important;
            }

            .dd {
                margin-top: 118px;
                display: block;
                margin-left: auto;
                margin-right: auto;
            }

        }

        body {
            background-image: url(/assets/hero-1.png);
            background-size: 60vw;
            background-repeat: no-repeat;
            min-height: 100vh;
            background-position: bottom left;
        }

        .EE {
            margin-top: 21px;
            max-width: 230px;
        }

        .dd {
            margin-top: 74px;

            /* margin-top: 118px;
            display: block;
            margin-left: auto;
            margin-right: auto; */
        }

        .main-container {
            margin-top: 118px;
            display: block;
            margin-left: auto;
            height: 360px;
            width: 361px;
            background-color: #989b9e21;
            border-radius: 39px;
        }

        .form-group {
            padding: 3px;
            width: 100%;
            max-width: 300px;
        }

        .form-control {
            border-radius: 12px !important;
        }

        button.jj {
            border: none;
            background: #febe15;
            padding: 4px;
            width: 100%;
            max-width: 294px;
            margin: 1rem 0;
            border-radius: 12px;
            height: 36px;
        }

        .jj:hover {
            background-color: #008CBA;
            color: white;
        }

        i#togglePassword {
            position: absolute;
            left: calc(100% - 32px);
            top: 50%;
            transform: translateY(-60%);
        }

        form {
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>

    <style>
        .submitButton {
            border-radius: 12px;
            background: #f7931e;
            color: #333333;
            width: 100%;
        }

        .loginContent .loginBlock {
            padding: 2rem 1rem 3rem;
            display: block;
            min-height: 360px;
            width: 361px;
            background-color: #fff;
            border-radius: 39px;
        }

        .invalid-feedback {
            margin-top: 0;
            margin-left: 8px;
            font-size: 12px;
            line-height: 12px;
        }

        .inputGroup {
            min-height: 54px;
            position: relative;
        }

        .typeToggler {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            position: absolute;
            top: 12px;
            right: 46px;
            font-size: 10px;
            cursor: pointer;
            text-transform: uppercase;
            color: #767676;
            font-weight: 600;
        }

        #login_id-error, #pwd-error{
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="main nn">
        <div class="container ">
            <div class="row">
                <div class="col-sm-6">
                    <div class=" container">
                        <img src="{{ asset('assets/img/logo_2.png') }}" width="250px" height="110px" class="dd">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="main-container">
                        <div class="container text-center">
                            <a><img src="{{ asset('assets/ship-rider-logo.svg') }}" class="EE"> </a>
                        </div>
                        <div class="container">
                            <p style="text-align:center; margin: 2rem 0 0.5rem">Please login into your account.</p>

                            <form class="row g-3 needs-validation px-4" method="POST" action="{{ route('login') }}"
                                id="loginform" autocomplete="off" novalidate>
                                @csrf
                                <div class="inputGroup col-md-12 mt-0">
                                    <input type="text" class="form-control" id="login_id" name="login_id"
                                        value="{{ old('login_id') }}" autocomplete="login_id" placeholder="Login Id"
                                        required autofocus />
                                    <div class="invalid-feedback">Username is required</div>
                                </div>
                                <div class="inputGroup col-md-12 mt-0">
                                    <input type="password" class="form-control" name="password" id="pwd"
                                        value="{{ old('password') }}" autocomplete="password" placeholder="********"
                                        autofocus required />
                                    <span class="typeToggler show" onclick="togglePasswordType()">
                                        show
                                    </span>
                                    <span class="typeToggler hide" onclick="togglePasswordType()" style="display: none">
                                        hide
                                    </span>
                                    <div class="invalid-feedback">Invalid password</div>
                                </div>

                                <div class="col-12">
                                    <button class="btn submitButton" type="submit">Login</button>
                                </div>
                            </form>


                            {{-- <form method="POST" action="{{ route('login') }}" id="loginform" autocomplete="off"
                                class="text-left">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="login_id" id="login_id" class="form-control"
                                        value="{{ old('login_id') }}" autocomplete="login_id" autofocus
                                        placeholder="Login Id">
                                </div>
                                <div class="form-group" style="position: relative; margin-top: 8px">
                                    <i class="bi bi-eye-slash hidePass" id="togglePassword"></i>
                                    <input type="password" name="password" class="form-control" id="pwd"
                                        value="{{ old('password') }}" autocomplete="password" autofocus
                                        placeholder="********">
                                </div>
                                <button type="submit" class="jj">Login</button>
                            </form> --}}

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
         const togglePasswordType = () => {
            if ($('#pwd').attr('type') == 'password')
                $('#pwd').attr('type', 'text')
            else $('#pwd').attr('type', 'password');

            var toggler = document.querySelectorAll('.typeToggler');
            toggler.forEach((x) => {
                if (x.style.display === 'none') {
                    x.style.display = 'block';
                } else {
                    x.style.display = 'none';
                }
            });
        };

        (function() {
            'use strict';

            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                
                form.addEventListener(
                    'submit',
                    function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                        
                    },
                    false
                );
            });
        })();


        // var APP_URL = {
        //     !!json_encode(url('/')) !!
        // };

        $(document).ready(function() {
            App.init();
        });

        // $(document).on('click', '#togglePassword', function() {
        //     if ($(this).hasClass('hidePass')) {
        //         $(this).removeClass('bi-eye-slash, hidePass');
        //         $(this).addClass('bi-eye');
        //         $('#pwd').attr('type', 'text');
        //     } else {
        //         $(this).removeClass('bi-eye');
        //         $(this).addClass('bi-eye-slash, hidePass');
        //         $('#pwd').attr('type', 'password');
        //     }

        // });
        
    </script>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>

    <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('assets/js/customjquery.validate.min.js') }}"></script>
    <script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom-validation.js') }}"></script>
    <!-- multi select -->
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/custom-select2.js') }}"></script>

    <!-- sweet alert -->
    <script src="{{ asset('plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalerts/custom-sweetalert.js') }}"></script>


    <script>
        
      
    </script>

    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{ asset('plugins/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dash_1.js') }}"></script>

    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.toast.js') }}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

</body>

</html>
