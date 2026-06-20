<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register - Sistem Tiket</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 20px;
    font-family:'Segoe UI',sans-serif;
    position:relative;
    overflow-x:hidden;

    background:
        radial-gradient(circle at top left,#4f46e5 0%,transparent 30%),
        radial-gradient(circle at bottom right,#2563eb 0%,transparent 30%),
        linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);
}

body::before{
    content:"";
    position:fixed;
    inset:0;
    background-image:
        linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
    background-size:40px 40px;
    pointer-events:none;
}

.login-card{
    width:100%;
    max-width:580px;

    background:rgba(255,255,255,.96);

    backdrop-filter:blur(18px);

    border:1px solid rgba(255,255,255,.2);

    border-radius:32px;

    overflow:hidden;

    box-shadow:
        0 30px 60px rgba(0,0,0,.25),
        0 10px 25px rgba(37,99,235,.15);
}

/* ================= HEADER ================= */

.login-header{
    position:relative;
    text-align:center;
    padding:45px 30px;

    color:#fff;

    background:
        linear-gradient(
            135deg,
            #1e3a8a 0%,
            #2563eb 50%,
            #60a5fa 100%
        );

    overflow:hidden;
}

.login-header::before{
    content:"";
    position:absolute;
    width:350px;
    height:350px;
    background:rgba(255,255,255,.08);
    border-radius:50%;
    top:-180px;
    right:-120px;
}

.login-header::after{
    content:"";
    position:absolute;
    width:220px;
    height:220px;
    background:rgba(255,255,255,.06);
    border-radius:50%;
    bottom:-120px;
    left:-80px;
}

.login-logo{
    width:95px;
    height:95px;

    margin:0 auto 20px;

    border-radius:24px;

    background:rgba(255,255,255,.18);

    backdrop-filter:blur(12px);

    display:flex;
    align-items:center;
    justify-content:center;

    border:1px solid rgba(255,255,255,.25);

    position:relative;
    z-index:2;
}

.login-logo iconify-icon{
    font-size:48px;
    color:white;
}

.login-title{
    font-size:2rem;
    font-weight:800;
    margin-bottom:8px;
    position:relative;
    z-index:2;
}

.login-subtitle{
    font-size:.95rem;
    opacity:.95;
    position:relative;
    z-index:2;
}

/* ================= BODY ================= */

.login-body{
    padding:35px;
}

/* ================= INPUT ================= */

.floating-group{
    position:relative;
    margin-bottom:22px;
}

.floating-input{
    width:100%;
    height:64px;

    border:2px solid #e2e8f0;

    border-radius:18px;

    background:#f8fafc;

    padding:22px 55px 8px 18px;

    font-size:.95rem;
    font-weight:500;

    transition:.25s ease;
}

.floating-input:focus{
    outline:none;

    background:#fff;

    border-color:#2563eb;

    box-shadow:
        0 0 0 4px rgba(37,99,235,.12);
}

.floating-label{
    position:absolute;

    left:15px;
    top:50%;

    transform:translateY(-50%);

    background:#f8fafc;

    padding:0 8px;

    color:#64748b;

    transition:.25s ease;

    pointer-events:none;
}

.floating-input:focus + .floating-label,
.floating-input:not(:placeholder-shown) + .floating-label,
.floating-select + .floating-label{

    top:0;

    font-size:.78rem;

    font-weight:700;

    color:#2563eb;

    background:#fff;
}

.floating-select{
    appearance:none;
    cursor:pointer;
}

.select-icon{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    font-size:22px;
    color:#64748b;
}

/* ================= PASSWORD ================= */

.password-toggle{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);

    background:none;
    border:none;

    cursor:pointer;

    color:#64748b;

    font-size:22px;

    transition:.2s;
}

.password-toggle:hover{
    color:#2563eb;
}

/* ================= ERROR ================= */

.field-error{
    display:block;
    margin-top:-12px;
    margin-bottom:15px;

    font-size:.85rem;

    color:#dc2626;

    font-weight:600;
}

/* ================= BUTTON ================= */

.btn-login{
    width:100%;
    height:60px;

    border:none;
    border-radius:18px;

    background:
        linear-gradient(
            135deg,
            #1e3a8a 0%,
            #2563eb 100%
        );

    color:#fff;

    font-weight:700;
    font-size:1rem;

    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;

    transition:.3s ease;
}

.btn-login:hover{
    transform:translateY(-3px);

    box-shadow:
        0 15px 35px rgba(37,99,235,.35);
}

.btn-login iconify-icon{
    font-size:22px;
}

/* ================= FOOTER ================= */

.login-footer{
    text-align:center;

    padding:22px;

    background:#f8fafc;

    border-top:1px solid #e5e7eb;
}

.login-footer p{
    margin:0;
    color:#64748b;
}

.login-footer a{
    color:#2563eb;
    text-decoration:none;
    font-weight:700;
}

.login-footer a:hover{
    color:#1d4ed8;
}

/* ================= RESPONSIVE ================= */

@media(max-width:576px){

    .login-card{
        border-radius:24px;
    }

    .login-header{
        padding:35px 25px;
    }

    .login-body{
        padding:25px;
    }

    .login-logo{
        width:80px;
        height:80px;
    }

    .login-title{
        font-size:1.7rem;
    }
}
    </style>
</head>

<body>

    <div class="login-card">

        <!-- HEADER -->
        <div class="login-header">

            <div class="login-logo">
                <iconify-icon icon="mdi:ticket"></iconify-icon>
            </div>

            <h1 class="login-title">
                Buat Akun
            </h1>

            <p class="login-subtitle">
                Sistem Tiket Layanan Diskominfo
            </p>

        </div>

        <!-- BODY -->
        <div class="login-body">

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- NAMA LENGKAP -->
                <div class="floating-group">

                    <input type="text" id="name" name="name" class="floating-input" placeholder=" "
                        value="{{ old('name') }}" required autofocus autocomplete="name">

                    <label for="name" class="floating-label">
                        <iconify-icon icon="mdi:account-outline" class="me-1"></iconify-icon>
                        Nama Lengkap
                    </label>

                </div>
                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- EMAIL -->
                <div class="floating-group">

                    <input type="email" id="email" name="email" class="floating-input" placeholder=" "
                        value="{{ old('email') }}" required autocomplete="username">

                    <label for="email" class="floating-label">
                        <iconify-icon icon="mdi:email-outline" class="me-1"></iconify-icon>
                        Email
                    </label>

                </div>
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
                
                <!-- Phome -->
                <div class="floating-group">

                    <input type="text" id="phone" name="phone" class="floating-input" placeholder=" "
                        value="{{ old('phone') }}" required autocomplete="username">

                    <label for="phone" class="floating-label">
                        <iconify-icon icon="mdi:phone-outline" class="me-1"></iconify-icon>
                        Nomor Telepon
                    </label>

                </div>
                @error('phone')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- NIP -->
                <div class="floating-group">

                    <input type="text" id="nip" name="nip" class="floating-input" placeholder=" "
                        value="{{ old('nip') }}" required autocomplete="off">

                    <label for="nip" class="floating-label">
                        <iconify-icon icon="mdi:card-account-details-outline" class="me-1"></iconify-icon>
                        Nip
                    </label>

                </div>
                @error('nip')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- DINAS -->
                <div class="floating-group">

                    <select id="department" name="department" class="floating-input floating-select" required>
                        <option value="Dinas Pendidikan">Dinas Pendidikan</option>
                        <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                        <option value="Dinas BKPSDM">Dinas BKPSDM</option>
                        <option value="Dinas Sosial">Dinas Sosial</option>
                        <option value="Dinas Dukcapil">Dinas Dukcapil</option>
                        <option value="Inspektorat">Inspektorat</option>
                        <option value="Dinas Perizinan">Dinas Perizinan</option>
                        <option value="Dinas Kecamatan">Dinas Kecamatan</option>
                        <option value="Dinas Bappelitbangda">Dinas Bappelitbangda</option>
                        <option value="Dinas Kominfo">Dinas Kominfo</option>
                    </select>

                    <label for="department" class="floating-label">
                        <iconify-icon icon="mdi:office-building-outline" class="me-1"></iconify-icon>
                        Dropdown Dinas
                    </label>

                    <iconify-icon icon="mdi:chevron-down" class="select-icon"></iconify-icon>

                </div>
                @error('department')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- PASSWORD -->
                <div class="floating-group">

                    <input type="password" id="password" name="password" class="floating-input" placeholder=" "
                        required autocomplete="new-password">

                    <label for="password" class="floating-label">
                        <iconify-icon icon="mdi:lock-outline" class="me-1"></iconify-icon>
                        Password
                    </label>

                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon')">

                        <iconify-icon icon="mdi:eye" id="toggleIcon"></iconify-icon>

                    </button>

                </div>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- CONFIRM PASSWORD -->
                <div class="floating-group">

                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="floating-input" placeholder=" " required autocomplete="new-password">

                    <label for="password_confirmation" class="floating-label">
                        <iconify-icon icon="mdi:lock-check-outline" class="me-1"></iconify-icon>
                        Confirm Password
                    </label>

                    <button type="button" class="password-toggle"
                        onclick="togglePassword('password_confirmation', 'toggleConfirmIcon')">

                        <iconify-icon icon="mdi:eye" id="toggleConfirmIcon"></iconify-icon>

                    </button>

                </div>
                @error('password_confirmation')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <!-- BUTTON -->
                <button type="submit" class="btn-login">

                    <iconify-icon icon="mdi:account-plus-outline"></iconify-icon>

                    Daftar

                </button>

            </form>

        </div>

        <!-- FOOTER -->
        <div class="login-footer">

            <p>
                Sudah punya akun?
                <a href="{{ route('login') }}">
                    Login
                </a>
            </p>

        </div>

    </div>


    <script>
        function togglePassword(inputId, iconId) {

            const passwordInput =
                document.getElementById(inputId);

            const toggleIcon =
                document.getElementById(iconId);

            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                toggleIcon.setAttribute(
                    'icon',
                    'mdi:eye-off'
                );

            } else {

                passwordInput.type = 'password';

                toggleIcon.setAttribute(
                    'icon',
                    'mdi:eye'
                );
            }
        }
    </script>

</body>

</html>
