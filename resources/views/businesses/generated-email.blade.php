<!DOCTYPE html>
<html>
<head>
    <title>Generated Email</title>
</head>
<body>
    <h1>Generated Outreach Email</h1>

    <h2>{{ $business->name }}</h2>

    <p><strong>Subject:</strong></p>
    <p>{{ $subject }}</p>

    <p><strong>Body:</strong></p>
    <textarea rows="18" cols="80">{{ $body }}</textarea>

    <p><a href="/businesses/{{ $business->id }}">Back to Business</a></p>
</body>
</html>
