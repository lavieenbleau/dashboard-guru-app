<x-guest-layout>
    <style>
        /* Liquid Glass Register Theme */
        body {
            background-color: #f4f6ff !important;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        body::before {
            content: '';
            position: fixed;
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
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(230,232,255,0.7) 0%, rgba(255,255,255,0) 65%);
            bottom: -200px;
            left: -100px;
            z-index: -1;
            border-radius: 50%;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.9) !important;
            border-radius: 32px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.04) !important;
            padding: 2.5rem 2rem;
            margin: 2rem auto;
            max-width: 500px;
        }

        .register-card .form-control, .register-card .input-group-text {
            background: rgba(255,255,255,0.8);
            border-color: rgba(0,0,0,0.08);
            border-radius: 12px;
            padding: 0.65rem 1rem;
            transition: all 0.3s ease;
        }
        .register-card .form-control:focus {
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
        .btn-premium:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(105, 108, 255, 0.3);
        }
        .btn-premium:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .strength-meter {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background-color: #e2e8f0;
            overflow: hidden;
            display: flex;
        }
        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
        .strength-text {
            font-size: 0.75rem;
            margin-top: 4px;
            font-weight: 500;
        }

        .text-weak { color: #ef4444; }
        .text-medium { color: #f59e0b; }
        .text-strong { color: #10b981; }
        
        .bg-weak { background-color: #ef4444; width: 33.33%; }
        .bg-medium { background-color: #f59e0b; width: 66.66%; }
        .bg-strong { background-color: #10b981; width: 100%; }

        .validation-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            display: none;
        }
        .validation-icon.success { color: #10b981; display: block; }
        .validation-icon.error { color: #ef4444; display: block; }
        .validation-icon.loading { color: #64748b; display: block; animation: spin 1s linear infinite; }

        @keyframes spin { 100% { transform: translateY(-50%) rotate(360deg); } }
    </style>

    <!-- Register Card -->
    <div class="card register-card">
        <div class="card-body p-0">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-4 text-center">
                <a href="/" class="app-brand-link gap-2 d-inline-block">
                    <img src="{{ asset('images/logo-sci.png') }}" alt="SCI Media" width="140">
                </a>
            </div>
            <!-- /Logo -->

            <h4 class="text-center fw-bold mb-2">Daftar Akun Guru</h4>
            <p class="text-center text-muted mb-4">Lengkapi form di bawah ini untuk bergabung</p>

            <form id="formRegistration" class="mb-3" action="{{ route('register') }}" method="POST">
                @csrf
                
                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required minlength="3" maxlength="100" autofocus />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <div class="position-relative">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email aktif" required />
                        <i class="bx bx-loader-alt validation-icon loading" id="emailLoading"></i>
                        <i class="bx bx-check-circle validation-icon success" id="emailSuccess"></i>
                        <i class="bx bx-x-circle validation-icon error" id="emailError"></i>
                    </div>
                    <div class="small mt-1" id="emailFeedback"></div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">Nomor WhatsApp</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required minlength="10" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3 form-password-toggle">
                    <label class="form-label fw-bold" for="password">Password</label>
                    <div class="position-relative">
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan password" required minlength="8" style="padding-right: 40px;" />
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" id="togglePassword" style="z-index: 10;">
                            <i class="bx bx-hide fs-5" id="toggleIcon"></i>
                        </span>
                    </div>
                    <!-- Password Meter -->
                    <div class="strength-meter">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text text-muted" id="strengthText">Kekuatan password</div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-3 form-password-toggle">
                    <label class="form-label fw-bold" for="password_confirmation">Konfirmasi Password</label>
                    <div class="position-relative">
                        <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Ulangi password" required minlength="8" style="padding-right: 40px;" />
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" id="toggleConfirmPassword" style="z-index: 10;">
                            <i class="bx bx-hide fs-5" id="toggleConfirmIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Checkbox -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
                        <label class="form-check-label text-muted" for="terms"> 
                            Saya menyetujui syarat dan ketentuan penggunaan sistem.
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mb-3">
                    <button class="btn btn-premium d-grid w-100" type="submit" id="btnSubmit">
                        <span class="d-flex align-items-center justify-content-center">
                            Daftar Sekarang
                        </span>
                    </button>
                </div>
                
                <p class="text-center">
                    <span class="text-muted">Sudah memiliki akun?</span>
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                        <span>Masuk sekarang</span>
                    </a>
                </p>
            </form>
        </div>
    </div>
    <!-- /Register Card -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Password Show/Hide
            function setupToggle(toggleId, inputId, iconId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (toggle && input) {
                    toggle.addEventListener("click", function () {
                        const type = input.getAttribute("type") === "password" ? "text" : "password";
                        input.setAttribute("type", type);
                        
                        if (type === "text") {
                            icon.classList.remove("bx-hide");
                            icon.classList.add("bx-show");
                        } else {
                            icon.classList.remove("bx-show");
                            icon.classList.add("bx-hide");
                        }
                    });
                }
            }
            setupToggle("togglePassword", "password", "toggleIcon");
            setupToggle("toggleConfirmPassword", "password_confirmation", "toggleConfirmIcon");

            // Password Strength Meter
            const password = document.getElementById("password");
            const strengthBar = document.getElementById("strengthBar");
            const strengthText = document.getElementById("strengthText");

            password.addEventListener("input", function() {
                const val = password.value;
                let strength = 0;
                
                if (val.length >= 8) strength += 1;
                if (val.match(/[a-z]+/)) strength += 1;
                if (val.match(/[A-Z]+/)) strength += 1;
                if (val.match(/[0-9]+/)) strength += 1;
                if (val.match(/[$@#&!]+/)) strength += 1;

                strengthBar.className = "strength-bar";
                
                if (val.length === 0) {
                    strengthText.textContent = "Kekuatan password";
                    strengthText.className = "strength-text text-muted";
                } else if (strength <= 2) {
                    strengthBar.classList.add("bg-weak");
                    strengthText.textContent = "Lemah";
                    strengthText.className = "strength-text text-weak";
                } else if (strength <= 4) {
                    strengthBar.classList.add("bg-medium");
                    strengthText.textContent = "Sedang";
                    strengthText.className = "strength-text text-medium";
                } else {
                    strengthBar.classList.add("bg-strong");
                    strengthText.textContent = "Kuat";
                    strengthText.className = "strength-text text-strong";
                }
            });

            // Email Realtime Validation
            const emailInput = document.getElementById("email");
            const emailLoading = document.getElementById("emailLoading");
            const emailSuccess = document.getElementById("emailSuccess");
            const emailError = document.getElementById("emailError");
            const emailFeedback = document.getElementById("emailFeedback");
            const btnSubmit = document.getElementById("btnSubmit");
            let emailTimeout = null;
            let isEmailValid = true;

            function resetEmailIcons() {
                emailLoading.style.display = "none";
                emailSuccess.style.display = "none";
                emailError.style.display = "none";
                emailFeedback.textContent = "";
                emailFeedback.className = "small mt-1";
                emailInput.classList.remove("is-invalid");
            }

            emailInput.addEventListener("input", function() {
                resetEmailIcons();
                clearTimeout(emailTimeout);
                
                const val = emailInput.value.trim();
                
                // Simple email regex
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!val || !emailPattern.test(val)) {
                    return; // Wait until valid format
                }

                emailLoading.style.display = "block";
                btnSubmit.disabled = true;

                emailTimeout = setTimeout(() => {
                    fetch('{{ route('check.email') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: val })
                    })
                    .then(response => response.json())
                    .then(data => {
                        emailLoading.style.display = "none";
                        if (data.available) {
                            emailSuccess.style.display = "block";
                            emailFeedback.textContent = "✓ Email tersedia";
                            emailFeedback.className = "small mt-1 text-success";
                            isEmailValid = true;
                            btnSubmit.disabled = false;
                        } else {
                            emailError.style.display = "block";
                            emailFeedback.textContent = "✕ Email sudah digunakan";
                            emailFeedback.className = "small mt-1 text-danger";
                            emailInput.classList.add("is-invalid");
                            isEmailValid = false;
                            btnSubmit.disabled = true;
                        }
                    })
                    .catch(() => {
                        emailLoading.style.display = "none";
                        btnSubmit.disabled = false;
                    });
                }, 800);
            });

            // Form Submit Loading State
            const form = document.getElementById("formRegistration");
            form.addEventListener("submit", function(e) {
                if (!isEmailValid) {
                    e.preventDefault();
                    emailInput.focus();
                    return;
                }
                
                const pass = document.getElementById("password").value;
                const conf = document.getElementById("password_confirmation").value;
                
                if (pass !== conf) {
                    e.preventDefault();
                    alert("Konfirmasi password tidak sesuai!");
                    return;
                }

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<i class="bx bx-loader-alt bx-spin me-2"></i> Membuat Akun...`;
            });
        });
    </script>
</x-guest-layout>
