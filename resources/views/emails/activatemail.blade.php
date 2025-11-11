<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Account Activated</title>
  <style>
    /* Gmail-safe inline CSS */
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid #e0e0e0;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .header {
      background-color: #2e7d32; /* green */
      color: #ffffff;
      text-align: center;
      padding: 18px;
      font-size: 20px;
      font-weight: bold;
    }
    .content {
      padding: 20px 28px;
      color: #333333;
      line-height: 1.45;
    }
    .info-box {
      background-color: #e9f7ec;
      border-left: 5px solid #2e7d32;
      padding: 14px;
      margin: 18px 0;
      border-radius: 5px;
    }
    .info-box p {
      margin: 6px 0;
      font-size: 15px;
    }
    .btn-wrap {
      text-align: center;
      margin: 18px 0;
    }
    /* Button styled inline as fallback — some clients strip external CSS for buttons */
    .footer {
      background-color: #323232;
      color: #ffffff;
      text-align: center;
      padding: 14px;
      font-size: 13px;
    }
    .muted { color: #6b6b6b; font-size: 13px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">✅ Account Activated</div>

    <div class="content">
      <p>Hello <strong>{{ $data['fullname'] }}</strong>,</p>

      <p>
        Good news — your account has been <strong>activated</strong> and is now ready to use.
      </p>

      <div class="info-box">
        <p><strong>👤 Full Name:</strong> {{ $data['fullname'] }}</p>
        <p><strong>📧 Email:</strong> {{ $data['email'] }}</p>
        <p><strong>🔖 Username:</strong> {{ $data['username'] }}</p>
      </div>

      @if(!empty($data['message']))
        <p><strong>📩 Message from Admin:</strong></p>
        <p style="font-style: italic;">"{{ $data['message'] }}"</p>
      @endif

      <p>
        We're happy to have you back. If you need help, reply to this message or contact our support team.
      </p>

      <p>Warm regards,<br><strong>Craftify Admin Team</strong></p>
    </div>

    <div class="footer">
      ©2025 Craftify — All rights reserved.
    </div>
  </div>
</body>
</html>
