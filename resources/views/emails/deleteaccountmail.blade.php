<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Account Deleted</title>
  <style>
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
      background-color: #b71c1c; /* deep red */
      color: #ffffff;
      text-align: center;
      padding: 18px;
      font-size: 20px;
      font-weight: bold;
    }
    .content {
      padding: 20px 28px;
      color: #333333;
      line-height: 1.5;
    }
    .info-box {
      background-color: #fdecea;
      border-left: 5px solid #c62828;
      padding: 14px;
      margin: 18px 0;
      border-radius: 5px;
    }
    .info-box p {
      margin: 6px 0;
      font-size: 15px;
    }
    .footer {
      background-color: #323232;
      color: #ffffff;
      text-align: center;
      padding: 14px;
      font-size: 13px;
    }
    .highlight {
      color: #c62828;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">⚠️ Account Deletion Notice</div>

    <div class="content">
      <p>Hello <strong>{{ $data['username'] }}</strong>,</p>

      <p>
        We regret to inform you that your account has been <span class="highlight">permanently deleted</span> from our system.
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
        Your account information and associated data have been permanently removed and cannot be recovered.
        If you believe this was a mistake or wish to appeal, please contact our support team immediately.
      </p>

      <p>Thank you for being part of our community,<br><strong>Craftify Admin Team</strong></p>
    </div>

    <div class="footer">
      ©2025 Craftify — All rights reserved.
    </div>
  </div>
</body>
</html>
