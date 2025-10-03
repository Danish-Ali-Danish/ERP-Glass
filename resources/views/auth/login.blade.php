<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>ERP Login | Expert Power Glass Ind</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Expert Power Glass Ind ERP System" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />`
    <link rel="shortcut icon" href="{{asset('assets/images/Favicon-01 (2).png')}}">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container-xxl">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mx-auto">
                        <div class="card">
                            <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                <div class="text-center p-3">
                                    <a href="#" class="logo logo-admin">
                                        <img src="{{asset('assets/images/Favicon-01 (2).png')}}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Let's Get Started ERP</h4>   
                                    <p class="text-muted fw-medium mb-0">Sign in to continue to ERP.</p>  
                                </div>
                            </div>

                            <div class="card-body pt-0">                                    
                                <form class="my-4" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>                                            
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-sm-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-end">
                                            <a href="#" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot password?</a>                                    
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="submit">Log In <i class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!--<div class="text-center  mb-2">-->
                                <!--    <p class="text-muted">Don't have an account ?  <a href="#" class="text-primary ms-2">Free Register</a></p>-->
                                <!--</div>-->
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end col-->
    </div><!--end row-->                                        
</div><!-- container -->
</body>
</html>
