<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .info p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>New Contact Form Submission</h2>
        <p>You have received a new message from your website's contact form. Here are the details:</p>

        <div class="info">
            <p><strong>Name:</strong> {{$details['contact']['first_name']}} {{$details['contact']['last_name']}} </p>
            <p><strong>Email:</strong> {{$details['contact']['email']}}</p>
            <p><strong>Phone:</strong> {{$details['contact']['phone']}}</p>
            <p><strong>Message:</strong> {{$details['contact']['message']}}</p>
            <p></p>
        </div>

        <p>If you need to respond, please reply to this email.</p>

        <div class="footer">
            <p>&copy;  Car . All Rights Reserved.</p>
        </div>
    </div>

</body>
</html>
