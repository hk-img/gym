<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['subject'] }}</title>
</head>
<body>
    {{-- <p>{!! nl2br(e($details['description'])) !!}</p> --}}
    <p>{!! $details['description'] !!}</p>
    <!-- You can include more dynamic content here based on the $template variable or any other logic -->
</body>
</html>
