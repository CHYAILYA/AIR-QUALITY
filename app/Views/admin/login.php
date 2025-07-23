<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
        }
        
        .auth-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
        }
        
        .input-group-icon {
            position: absolute;
            z-index: 3;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: #666;
        }
        
        .form-control {
            padding-left: 45px;
            height: 45px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="https://via.placeholder.com/80" alt="Logo" class="mb-3 rounded-circle">
                            <h2 class="fw-bold mb-2">Welcome Back</h2>
                            <p class="text-muted">Please sign in to continue</p>
                        </div>

                        <form action="<?= site_url('Login') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3 position-relative">
                                <span class="input-group-icon">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="Email Address" required>
                            </div>

                            <div class="mb-4 position-relative">
                                <span class="input-group-icon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Password" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="<?= site_url('register') ?>" class="text-decoration-none">
                                    Don't have an account? Create account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '<?= session()->getFlashdata('error') ?>',
            timer: 3000
        });
    </script>
    <?php endif; ?>
</body>
</html>