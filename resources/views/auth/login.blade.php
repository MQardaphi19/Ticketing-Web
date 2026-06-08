<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Login - Sistem Tiket</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

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
    padding:20px;
    font-family:'Segoe UI',sans-serif;

    background:
    radial-gradient(circle at top left,#6ea8fe33,transparent 35%),
    radial-gradient(circle at bottom right,#ffffff20,transparent 30%),
    linear-gradient(135deg,#1e1b4b 0%,#312e81 40%,#2563eb 100%);

    overflow:hidden;
    position:relative;
}

/* Background Effect */
body::before{
    content:"";
    position:absolute;
    width:400px;
    height:400px;
    background:rgba(255,255,255,.08);
    border-radius:50%;
    top:-120px;
    left:-120px;
    filter:blur(60px);
}

body::after{
    content:"";
    position:absolute;
    width:350px;
    height:350px;
    background:rgba(255,255,255,.05);
    border-radius:50%;
    bottom:-100px;
    right:-100px;
    filter:blur(60px);
}

/* LOGIN CARD */
.login-card{
    width:100%;
    max-width:450px;

    background:rgba(255,255,255,.95);

    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);

    border-radius:28px;

    overflow:hidden;

    border:1px solid rgba(255,255,255,.4);

    box-shadow:
    0 25px 60px rgba(0,0,0,.20);
}

/* HEADER */
.login-header{
    padding:45px 35px;
    text-align:center;
    position:relative;

    background:
    linear-gradient(135deg,#1e1b4b,#2563eb);

    color:white;
}

.login-header::before{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    top:-80px;
    right:-60px;
}

.login-logo{
    width:95px;
    height:95px;

    margin:auto auto 20px;

    border-radius:24px;

    background:rgba(255,255,255,.15);

    backdrop-filter:blur(15px);

    display:flex;
    align-items:center;
    justify-content:center;

    border:1px solid rgba(255,255,255,.2);
}

.login-logo iconify-icon{
    font-size:48px;
    color:white;
}

.login-title{
    font-size:2rem;
    font-weight:700;
    margin-bottom:5px;
}

.login-subtitle{
    opacity:.9;
    font-size:.95rem;
}

/* BODY */
.login-body{
    padding:35px;
}

/* INPUT */
.floating-group{
    position:relative;
    margin-bottom:24px;
}

.floating-input{
    width:100%;
    height:62px;

    border:1.5px solid #dbe2ea;
    border-radius:16px;

    background:#fff;

    padding:22px 55px 8px 18px;

    font-size:.95rem;

    transition:.3s;
}

.floating-input:focus{
    outline:none;

    border-color:#2563eb;

    box-shadow:
    0 0 0 4px rgba(37,99,235,.12);
}

.floating-label{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);

    background:white;

    padding:0 6px;

    color:#64748b;

    transition:.25s;

    pointer-events:none;
}

.floating-input:focus + .floating-label,
.floating-input:not(:placeholder-shown) + .floating-label{
    top:0;
    font-size:.75rem;
    color:#2563eb;
    font-weight:600;
}

.password-toggle{
    position:absolute;
    top:50%;
    right:18px;
    transform:translateY(-50%);

    border:none;
    background:none;

    color:#64748b;
    font-size:22px;

    cursor:pointer;
}

.password-toggle:hover{
    color:#2563eb;
}

/* BUTTON */
.btn-login{
    width:100%;
    height:58px;

    border:none;
    border-radius:16px;

    background:
    linear-gradient(135deg,#1e1b4b,#2563eb);

    color:white;

    font-size:1rem;
    font-weight:700;

    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;

    transition:.3s;
}

.btn-login:hover{
    transform:translateY(-3px);

    box-shadow:
    0 15px 35px rgba(37,99,235,.35);
}

/* FOOTER */
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
    text-decoration:none;
    color:#2563eb;
    font-weight:600;
}

.login-footer a:hover{
    color:#1d4ed8;
}

/* MOBILE */
@media(max-width:576px){

    .login-card{
        max-width:100%;
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
        Sistem Ticketing 
      </h1>

      <p class="login-subtitle">
        Layanan Diskominfo
      </p>

    </div>

    <!-- BODY -->
    <div class="login-body">

       <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- EMAIL -->
        <div class="floating-group">

          <input type="email"
                 id="email"
                 name="email"
                 class="floating-input"
                 placeholder=" "
                 required>

          <label for="email"
                 class="floating-label">
                 <iconify-icon icon="mdi:email-outline" class="me-1"></iconify-icon>
            Email/Username
          </label>

        </div>

        <!-- PASSWORD -->
        <div class="floating-group">

          <input type="password"
                 id="password"
                 name="password"
                 class="floating-input"
                 placeholder=" "
                 required>

          <label for="password"
                 class="floating-label">
                   <iconify-icon icon="mdi:lock-outline" class="me-1"></iconify-icon>
            Password
          </label>

          <button type="button"
                  class="password-toggle"
                  onclick="togglePassword()">

            <iconify-icon icon="mdi:eye"
                          id="toggleIcon"></iconify-icon>

          </button>

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="btn-login">

          <iconify-icon icon="mdi:login"></iconify-icon>

          Login

        </button>

      </form>

    </div>

    <!-- FOOTER -->
    <div class="login-footer">

      <p>
        Belum punya akun?
        <a href="{{ route('register') }}">
          Daftar sekarang
        </a>
      </p>

    </div>

  </div>


  <script>

    function togglePassword() {

      const passwordInput =
        document.getElementById('password');

      const toggleIcon =
        document.getElementById('toggleIcon');

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
