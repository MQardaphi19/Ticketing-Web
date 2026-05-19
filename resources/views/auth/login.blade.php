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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;

      font-family: 'Segoe UI', sans-serif;

      background: linear-gradient(
        135deg,
        #272198 0%,
        
        #5a96f8 100%
      );
    }

    .login-card {
      width: 100%;
      max-width: 430px;

      background: #fff;
      border-radius: 24px;
      overflow: hidden;

      box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    }

    /* HEADER */

    .login-header {
      padding: 45px 30px;
      text-align: center;
      color: white;

      background: linear-gradient(
        135deg,
        #272198 0%,
        
        #5a96f8 100%
      );

      position: relative;
      overflow: hidden;
    }

    .login-header::before {
      content: "";
      position: absolute;
      top: -40%;
      right: -40%;
      width: 100%;
      height: 100%;

      background: radial-gradient(
        circle,
        rgba(255,255,255,0.15) 1px,
        transparent 1px
      );

      background-size: 16px 16px;
    }

    .login-logo {
      width: 80px;
      height: 80px;

      margin: 0 auto 20px;

      border-radius: 20px;

      background: rgba(255,255,255,0.18);

      display: flex;
      align-items: center;
      justify-content: center;

      backdrop-filter: blur(10px);

      position: relative;
      z-index: 2;
    }

    .login-logo iconify-icon {
      font-size: 40px;
      color: white;
    }

    .login-title {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 6px;

      position: relative;
      z-index: 2;
    }

    .login-subtitle {
      font-size: 0.95rem;
      opacity: 0.9;

      position: relative;
      z-index: 2;
    }

    /* BODY */

    .login-body {
      padding: 40px 32px;
    }

    /* FLOATING INPUT */

    .floating-group {
      position: relative;
      margin-bottom: 28px;
    }

    .floating-input {
      width: 100%;
      height: 70px;

      border: 3px solid #b5b5b5;
      border-radius: 22px;

      padding: 26px 60px 10px 20px;

      font-size: 1rem;
      font-weight: 500;

      background: #fff;
      color: #111;

      outline: none;

      transition: all 0.2s ease;
    }

    .floating-input:focus {
      border-color: #3b82f6;

      box-shadow:
        0 0 0 4px rgba(59,130,246,0.12);
    }

    .floating-label {
      position: absolute;

      left: 18px;
      top: 50%;

      transform: translateY(-50%);

      background: #fff;

      padding: 0 6px;

      color: #666;

      font-size: 1rem;
      font-weight: 500;

      pointer-events: none;

      transition: all 0.2s ease;
    }

    .floating-input:focus + .floating-label,
    .floating-input:not(:placeholder-shown) + .floating-label {

      top: 0;

      transform: translateY(-50%);

      font-size: 0.78rem;

      color: #3b82f6;

      font-weight: 700;
    }

    /* PASSWORD TOGGLE */

    .password-toggle {
      position: absolute;

      right: 20px;
      top: 50%;

      transform: translateY(-50%);

      background: transparent;
      border: none;

      display: flex;
      align-items: center;
      justify-content: center;

      cursor: pointer;

      color: #111;

      font-size: 22px;

      transition: 0.2s ease;
    }

    .password-toggle:hover {
      color: #3b82f6;
    }

    /* BUTTON */

    .btn-login {
      width: 100%;
      height: 60px;

      border: none;
      border-radius: 18px;

      background: linear-gradient(
        135deg,
        #272198 0%,
        
        #5a96f8 100%
      );

      color: white;

      font-size: 1.05rem;
      font-weight: 700;

      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;

      transition: all 0.25s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);

      box-shadow:
        0 12px 30px rgba(99,102,241,0.35);
    }

    .btn-login iconify-icon {
      font-size: 22px;
    }

    /* FOOTER */

    .login-footer {
      text-align: center;

      padding: 22px;

      background: #f9fafb;

      border-top: 1px solid #e5e7eb;
    }

    .login-footer p {
      margin: 0;
      color: #666;
      font-size: 0.95rem;
    }

    .login-footer a {
      text-decoration: none;
      color: #6366f1;
      font-weight: 700;
    }

    .login-footer a:hover {
      color: #4f46e5;
    }

    /* SSL */

    .ssl-text {
      margin-top: 18px;

      text-align: center;

      color: rgba(255,255,255,0.9);

      font-size: 0.85rem;

      display: flex;
      justify-content: center;
      align-items: center;
      gap: 6px;
    }

    /* RESPONSIVE */

    @media(max-width: 576px){

      .login-body {
        padding: 30px 22px;
      }

      .login-header {
        padding: 35px 24px;
      }

      .floating-input {
        height: 65px;
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
        Sistem Tiket
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
        <a href="#">
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