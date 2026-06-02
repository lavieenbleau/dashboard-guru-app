<x-guest-layout>
    <style>
        /* Liquid Glass Login Theme */
        body {
            background-color: #f4f6ff !important;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(230,232,255,0.9) 0%, rgba(255,255,255,0) 65%);
            top: -200px;
            right: -200px;
            z-index: -1;
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(230,232,255,0.7) 0%, rgba(255,255,255,0) 65%);
            bottom: -200px;
            left: -100px;
            z-index: -1;
            border-radius: 50%;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.9) !important;
            border-radius: 32px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.04) !important;
            padding: 2rem 1.5rem;
        }

        .login-card .form-control, .login-card .input-group-text {
            background: rgba(255,255,255,0.8);
            border-color: rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        .login-card .form-control:focus {
            background: #ffffff;
            border-color: #5c60f5;
            box-shadow: 0 0 0 0.25rem rgba(92, 96, 245, 0.1);
        }
        
        .btn-premium {
            background: linear-gradient(135deg, #696CFF 0%, #5f61e6 100%);
            color: #FFFFFF !important;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(105, 108, 255, 0.3);
        }
    </style>

    <!-- Login Card -->
    <div class="card login-card">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-4 text-center">
                <a href="/" class="app-brand-link gap-2 d-inline-block">
                    <img src="{{ asset('images/logo-sci.png') }}" alt="SCI Media" width="140">
                </a>
            </div>
            <!-- /Logo -->

            <p class="text-center text-muted mb-4">Silakan masuk ke akun Anda</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" autofocus required />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label fw-bold" for="password">Password</label>
                    </div>
                    <div class="position-relative">
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required style="padding-right: 40px;" />
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" id="togglePassword" style="z-index: 10;">
                            <i class="bx bx-hide fs-5" id="toggleIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                        <label class="form-check-label" for="remember_me"> Ingat Saya </label>
                    </div>
                </div>

                <div class="mb-3">
                    <button class="btn btn-premium d-grid w-100" type="submit">Masuk ke Dashboard</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Login Card -->

    <!-- Peek Password Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const password = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            if (togglePassword && password) {
                togglePassword.addEventListener("click", function () {
                    // Toggle the type attribute
                    const type = password.getAttribute("type") === "password" ? "text" : "password";
                    password.setAttribute("type", type);
                    
                    // Toggle the icon
                    if (type === "text") {
                        toggleIcon.classList.remove("bx-hide");
                        toggleIcon.classList.add("bx-show");
                    } else {
                        toggleIcon.classList.remove("bx-show");
                        toggleIcon.classList.add("bx-hide");
                    }
                });
            }
        });
    </script>
</x-guest-layout>
