<!DOCTYPE html>
<html>
<head>
    <title>New Member Added</title>
</head>
<body>
    <h2>Hello, Gym Owner!</h2>
    <p>A new member has been added to your gym.</p>

    <h3>Member Details:</h3>
    <ul>
        <li><strong>Name:</strong> {{ $member->name }}</li>
        <li><strong>Phone:</strong> {{ $member->phone }}</li>
        <li><strong>Join Date:</strong> {{ $member->created_at->format('d M Y') }}</li>
    </ul>
    
    <p>Thank you!</p>
</body>
</html>
