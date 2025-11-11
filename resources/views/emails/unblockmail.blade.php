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
      background-color: #2e7d32; /* green */
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
      background-color: #e9f7ec;
      border-left: 5px solid #2e7d32;
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
      color: #2e7d32;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">✅ Product Unblocked Notification</div>

    <div class="content">
      <p>Hello <strong>{{ $data->seller_name }}</strong>,</p>
      <p>
        Great news! Your product has been <span class="highlight">unblocked</span> and is now visible to buyers again.
      </p>

      <div class="info-box">
        <p><strong>🆔 Product ID:</strong> {{ $data->product_id }}</p>
        <p><strong>📦 Product Name:</strong> {{ $data->product_name }}</p>
        <p><strong>👤 Seller Name:</strong> {{ $data->seller_name }}</p>
        <p><strong>🏪 Shop Name:</strong> {{ $data->shop_name }}</p>
      </div>

      @if(!empty($data->message))
      <p>Message from Admin:</p>
      <p>"{{ $data->message }}"</p>
      @endif

      <p>Your product is now back in the marketplace and available for buyers to view and purchase.</p>

      <p>Thank you for your cooperation and for being part of the <strong>Craftify</strong> community!</p>

      <p>Warm regards,<br><strong>Craftify Admin Team</strong></p>
    </div>

    <div class="footer">
      ©2025 Craftify — All rights reserved.
    </div>
  </div>
</body>
</html>
