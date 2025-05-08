<!DOCTYPE html>
<html>
<head>
    <title>New Gym Added</title>
</head>
<body>
    <h2>Congratulations!</h2>
    <p>Your gym has been successfully added to our system.</p>

    <h3>Gym Details:</h3>
    <ul>
        <li><strong>Name:</strong> {{ $gym->name }}</li>
        <li><strong>Registered On:</strong> {{ $gym->created_at->format('d M Y') }}</li>
    </ul>

    <p>Thank you!</p>
</body>
</html>
