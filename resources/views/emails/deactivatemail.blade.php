<!DOCTYPE html>
<html>
<head>
  <style>
    /* Gmail-safe inline CSS style */
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
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .header {
      background-color: #323232;
      color: #ffffff;
      text-align: center;
      padding: 20px;
      font-size: 22px;
      font-weight: bold;
    }

    .content {
      padding: 20px 30px;
      color: #333333;
    }

    .info-box {
      background-color: #f9f3ef;
      border-left: 5px solid #c0673e;
      padding: 15px;
      margin: 20px 0;
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
      padding: 15px;
      font-size: 13px;
    }

    .highlight {
      color: #c0673e;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">🚫 Account Deactivation Notice</div>

    <div class="content">
      <p>Hello <strong>{{ $data['fullname'] }}</strong>,</p>

      <p>
        We regret to inform you that your account has been <span class="highlight">deactivated</span> by the admin team due to certain policy or behavior-related concerns.
      </p>

      <div class="info-box">
        <p><strong>👤 Full Name:</strong> {{ $data['fullname'] }} </p>
        <p><strong>📧 Email:</strong> {{ $data['email'] }}</p>
        <p><strong>🔖 Username:</strong> {{ $data['username'] }}</p>
      </div>

      @if(!empty($data['message']))
        <p><strong>📩 Message from Admin:</strong></p>
        <p style="font-style: italic;">"{{ $data['message'] }}"</p>
      @endif

      <p>
        Your account is currently inactive and cannot be accessed or used for transactions.  
        If you believe this deactivation was a mistake or would like to appeal, please reply to this message or contact our support team.
      </p>

      <p>
        Thank you for your understanding,<br>
        <strong>Craftify Admin Team</strong>
      </p>
    </div>

    <div class="footer">
      ©2025 Craftify — All rights reserved.
    </div>
  </div>
</body>
</html>
