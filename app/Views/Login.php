<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphya - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #007bff; /* Standard Bootstrap blue */
            --dolphya-blue-dark: #2F39C2; /* Darker blue from blob */
            --dolphya-blue-light: #5A7DFF; /* Lighter blue from blob */
            --text-dark: #2d3748;
            --text-muted: #718096;
            --input-bg: #edf2f7; /* Very light grey */
            --input-border: #cbd5e0;
            --card-bg: #ffffff;
            --body-bg: #f7fafc; /* A soft, nearly white background */
            --shadow-light: 0 5px 15px rgba(0,0,0,0.08);
            --shadow-medium: 0 15px 30px rgba(0,0,0,0.15);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden; /* Prevent scrollbars */
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 400px; /* Width similar to the Dolphya design */
            margin: 20px;
            z-index: 1;
            box-shadow: var(--shadow-medium); /* Shadow applied to the whole container to match Dolphya */
            border-radius: 20px;
            overflow: hidden; /* Ensures blob doesn't spill out */
            background-color: var(--card-bg); /* Card background for the whole container */
        }

        .header-blob {
            position: relative;
            background: linear-gradient(135deg, var(--dolphya-blue-light) 0%, var(--dolphya-blue-dark) 100%);
            padding: 40px 20px 80px 20px; /* Top, sides, and bottom padding for content and blob curve */
            color: #fff;
            text-align: center;
            border-radius: 20px; /* Initial roundness */
            overflow: hidden;
            z-index: 1;
        }

        /* The actual blob shape using pseudo-elements */
        .header-blob::after {
            content: '';
            position: absolute;
            bottom: -50px; /* Adjust this to control the curve depth */
            left: -10%;
            width: 120%;
            height: 100px; /* Height of the curved part */
            background: var(--card-bg); /* Matches the card background */
            border-radius: 50% 50% 0 0 / 100% 100% 0 0; /* Creates the curve */
            transform: scaleY(0.8); /* Adjust curve flatness */
            z-index: 2; /* Above the header content if needed */
        }

        .header-blob h2 {
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 5px;
            color: #fff; /* Ensure text is white */
            position: relative;
            z-index: 3; /* Ensure text is above pseudo-element */
        }
        .header-blob p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 3;
        }


        .card-body-custom {
            padding: 2.5rem;
            text-align: left; /* Align text to left for labels */
            position: relative;
            z-index: 4; /* Ensure body content is on top */
            background-color: var(--card-bg); /* Explicitly set for overlap */
            margin-top: -60px; /* Pull content up to overlap blob */
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label-custom {
            font-size: 0.85rem;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-with-icon {
            position: relative;
        }

        .form-control {
            padding: 1rem 1rem 1rem 1.5rem; /* Standard padding, icon is on right */
            height: 50px;
            border-radius: 10px;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        }

        .input-icon-right {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none; /* Make sure it doesn't block input clicks */
        }

        .checkbox-forgot-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check {
            text-align: left;
            margin-bottom: 0; /* Remove default margin */
        }

        .form-check-input {
            margin-top: 0.25em;
            margin-left: -1.25em;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .forgot-password {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-blue);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .forgot-password:hover {
            color: var(--text-dark);
            text-decoration: underline;
        }

        .btn-main {
            background: linear-gradient(45deg, var(--dolphya-blue-light) 0%, var(--dolphya-blue-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 0;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(47, 57, 194, 0.3); /* Shadow matching Dolphya blue */
            width: 100%;
        }

        .btn-main:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 57, 194, 0.4);
        }
        
        .signup-link {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 2rem;
            text-align: center; /* Center the sign up link */
        }
        .signup-link a {
            font-weight: 600;
            color: var(--primary-blue);
        }
        .signup-link a:hover {
            color: var(--text-dark);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-container {
                max-width: 380px;
            }
            .header-blob {
                padding: 30px 15px 60px 15px;
            }
            .header-blob h2 {
                font-size: 2rem;
            }
            .card-body-custom {
                padding: 2rem 1.5rem;
                margin-top: -50px;
            }
            .form-control {
                height: 48px;
            }
            .btn-main {
                padding: 12px 0;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            body {
                align-items: flex-start;
                padding-top: 30px;
                overflow-y: auto;
            }
            .login-container {
                max-width: 90%;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .header-blob {
                padding: 25px 10px 50px 10px;
            }
            .header-blob h2 {
                font-size: 1.8rem;
            }
            .card-body-custom {
                padding: 1.5rem;
                margin-top: -40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-blob">
            <h2>Welcome Back,</h2>
            <p>Log In!</p>
        </div>

        <div class="card-body-custom">
            <?php if(session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul style="margin-bottom:0;">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <form id="authForm" action="<?= site_url('/Login') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- Login Fields -->
                <div id="loginFields">
                    <div class="form-group">
                        <label for="email" class="form-label-custom">EMAIL ADDRESS</label>
                        <div class="input-with-icon">
                            <input type="email" name="email" id="email" class="form-control" 
                                    placeholder="jacob@gmail.com" required>
                            <span class="input-icon-right">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label-custom">PASSWORD</label>
                        <div class="input-with-icon">
                            <input type="password" name="password" id="password" class="form-control" 
                                    placeholder="********" required>
                            <span class="input-icon-right">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Register Fields (hidden by default) -->
                <div id="registerFields" style="display:none;">
                    <div class="form-group">
                        <label for="reg_name" class="form-label-custom">FULL NAME</label>
                        <div class="input-with-icon">
                            <input type="text" name="reg_name" id="reg_name" class="form-control" placeholder="Your Name" required>
                            <span class="input-icon-right">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reg_email" class="form-label-custom">EMAIL ADDRESS</label>
                        <div class="input-with-icon">
                            <input type="email" name="reg_email" id="reg_email" class="form-control" placeholder="your@email.com" required>
                            <span class="input-icon-right">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reg_password" class="form-label-custom">PASSWORD</label>
                        <div class="input-with-icon">
                            <input type="password" name="reg_password" id="reg_password" class="form-control" placeholder="********" required>
                            <span class="input-icon-right">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reg_password_confirm" class="form-label-custom">CONFIRM PASSWORD</label>
                        <div class="input-with-icon">
                            <input type="password" name="reg_password_confirm" id="reg_password_confirm" class="form-control" placeholder="********" required>
                            <span class="input-icon-right">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="checkbox-forgot-container" id="loginOptions">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-main" id="loginBtn">Log In</button>
                    <button type="button" class="btn btn-main" id="registerBtn" style="display:none;">
                        Register
                    </button>
                </div>

                <div class="d-grid mb-3">
                    <a href="<?= site_url('auth/google') ?>" class="btn btn-danger" style="background:#db4437;border:none;">
                        <i class="fab fa-google"></i> Login with Google
                    </a>
                </div>

                <p class="signup-link" id="toRegister">Don't have an account? <a href="#" onclick="showRegister(event)">Sign Up</a></p>
                <p class="signup-link" id="toLogin" style="display:none;">Already have an account? <a href="#" onclick="showLogin(event)">Log In</a></p>
            </form>
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

    <script>
        function setRegisterFieldsRequired(required) {
            document.getElementById('reg_name').required = required;
            document.getElementById('reg_email').required = required;
            document.getElementById('reg_password').required = required;
            document.getElementById('reg_password_confirm').required = required;
        }

        function setLoginFieldsRequired(required) {
            document.getElementById('email').required = required;
            document.getElementById('password').required = required;
        }

        function showRegister(event) {
            event.preventDefault();
            document.getElementById('loginFields').style.display = 'none';
            document.getElementById('registerFields').style.display = 'block';
            document.getElementById('loginOptions').style.display = 'none';
            document.getElementById('loginBtn').style.display = 'none';
            document.getElementById('registerBtn').style.display = 'block';
            document.getElementById('toRegister').style.display = 'none';
            document.getElementById('toLogin').style.display = 'block';
            setRegisterFieldsRequired(true);
            setLoginFieldsRequired(false);
        }

        function showLogin(event) {
            event.preventDefault();
            document.getElementById('loginFields').style.display = 'block';
            document.getElementById('registerFields').style.display = 'none';
            document.getElementById('loginOptions').style.display = 'flex';
            document.getElementById('loginBtn').style.display = 'block';
            document.getElementById('registerBtn').style.display = 'none';
            document.getElementById('toRegister').style.display = 'block';
            document.getElementById('toLogin').style.display = 'none';
            setRegisterFieldsRequired(false);
            setLoginFieldsRequired(true);
        }

        // Set default required on load
        window.onload = function() {
            setRegisterFieldsRequired(false);
            setLoginFieldsRequired(true);
        }
    </script>
</body>
</html>