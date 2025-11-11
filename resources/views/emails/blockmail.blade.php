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
    <div class="header">🚫 Product Blocked Notification</div>

    <div class="content">
      <p>Hello <strong>{{ $data->seller_name }}</strong>,</p>
      <p>
        We would like to inform you that your product has been <span class="highlight">blocked</span> due to policy or review issues.
      </p>

      <div class="info-box">
        <p><strong>🆔 Product ID:</strong> {{ $data->product_id }}</p>
        <p><strong>📦 Product Name:</strong> {{ $data->product_name }}</p>
        <p><strong>👤 Seller Name:</strong> {{ $data->seller_name }}</p>
        <p><strong>🏪 Shop Name:</strong> {{ $data->shop_name }}</p>
      </div>
      
      <p>Message from admin: </p>
      
      <p>
        "{{$data->message}}"
      </p>

      <p>This action will cause your product to be hidden from all buyers but still be viewed in your account.</p>

      <p>If you believe this was a mistake, please reply to this message. Thank you.</p>

      <p>Thank you for understanding,<br><strong>FROM CRAFTIFY Admin Team</strong></p>
    </div>

    <div class="footer">
      ©2025 FROM CRAFTIFY — All rights reserved.
    </div>
  </div>
</body>
</html>
