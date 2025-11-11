<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | E-Commerce Dashboard</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body {
      background: linear-gradient(135deg, #32323290, #DDD0C8);
      display:flex; justify-content:center; align-items:center; height:100vh; color:#222;
    }
    .login-container {
      background:#fff; padding:36px; border-radius:16px; width:420px;
      box-shadow:0 8px 25px rgba(0,0,0,0.18); animation:fadeIn .8s ease-in-out;
    }
    @keyframes fadeIn { from{opacity:0; transform:translateY(10px)} to{opacity:1; transform:translateY(0)} }
    .login-container h2 { text-align:center; margin-bottom:12px; color:#323232; font-size:26px; }
    .login-container p.note {
      text-align:center; font-size:13px; color:#b22222; margin-bottom:18px; line-height:1.35;
      background: #fff7f7; padding:10px; border-radius:8px; border:1px solid #f2dede;
    }
    .form-group { margin-bottom:18px; }
    label { display:block; margin-bottom:6px; color:#555; font-weight:500; }
    input { width:100%; padding:12px; border:1px solid #ccc; border-radius:8px; font-size:14px; }
    input:focus { border-color:#32323290; outline:none; }
    button {
      width:100%; background:#323232; color:#fff; border:none; padding:12px; border-radius:8px;
      font-size:15px; font-weight:600; cursor:pointer;
    }
    button:hover { background:#555; }
    .footer { text-align:center; margin-top:20px; font-size:13px; color:#777; }

    /* MODAL STYLES */
    .modal-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      animation: fadeIn .3s ease-in-out;
    }
    .modal {
      background: #fff;
      padding: 28px;
      border-radius: 12px;
      width: 360px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.25);
      text-align: center;
      animation: fadeInUp .4s ease-in-out;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .modal h3 {
      margin-bottom: 10px;
      font-size: 22px;
      color: #333;
    }
    .modal p {
      font-size: 14px;
      color: #555;
      margin-bottom: 18px;
    }
    .otp-input {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      text-align: center;
      letter-spacing: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .modal button {
      width: 100%;
      background: #323232;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
    }
    .modal button:hover { background: #555; }
  </style>
</head>
<body>

  <!-- IF PASSWORD EXIST THE OTP MODAL WILL SHOW UP -->
  @if(session('response'))
  <div class="modal-overlay" id="otpModal">
    <div class="modal">
      <h3>Enter OTP</h3>
      <p>We sent a One-Time Password to your registered email ({{session()->get('email')}}).</p>

      <form onsubmit="verify_otp(event)">
        <input type="text" name="otp_code" maxlength="6" id="otp_code" class="otp-input" placeholder="------" required />
        <button type="submit">Verify OTP</button>
      </form>
    </div>
  </div>
  @endif

  <div class="login-container">

    @if(session('message'))
        <label style="text-align: center; margin-bottom: 10px; color: red;">{{ session('message') }}</label>
    @endif

    <h2>Admin Login</h2>

    <!-- UPDATED LEGAL NOTE -->
    <p class="note">
      ⚠️ <strong>Authorized Admin Only.</strong><br>
      Unauthorized access or attempting to log in without authorization is a criminal offense under Philippine law.
      Under the Cybercrime Prevention Act (R.A. No. 10175), <strong>illegal access</strong> may be punished with
      <em>prision mayor</em> (typically <strong>6 years and 1 day up to 12 years</strong>) and/or substantial fines.
      Other laws (such as R.A. No. 8792) may also apply. Violators may be prosecuted to the full extent of the law.
    </p>

    <form method="post" action="{{ route('admin_check') }}" onsubmit="return login(event)">
      @csrf
      <div class="form-group">
        <label for="username">Admin Email</label>
        <input type="text" id="username" name="username" placeholder="Enter username" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required />
      </div>

      <button type="submit">Login</button>
    </form>

    <div class="footer">
      <p>© 2025 Craftify Admin Portal</p>
    </div>
  </div>

  <script>
    function login(event) {

      const email = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value.trim();
      if (!email || !password) {
        alert("Please fill in all fields.");
        return false;
      }
      return true;
    }

    async function verify_otp(event) {

        event.preventDefault();

        const code = document.getElementById('otp_code').value;
        const email = "{{ session('email') }}";

        const data_pass = {'email': email, 'code': code};

        const res = await fetch('/admin/verify-otp', {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data_pass)
        });

        const data = await res.json();

        if(!data.ok){
            console.log(data);
        }

        if(data.message === 'success'){
            window.location.href = "/admin/home";
        }
        else{
          window.alert("OTP NOT EXIST. PLEASE TRY AGAIN");
        }
    }
  </script>
</body>
</html>
