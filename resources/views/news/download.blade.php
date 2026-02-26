<!DOCTYPE html>
<html>
<head>
    <title>Generated File</title>
    <style>
        body { font-family: Arial; text-align: center; margin-top: 40px; }
        img { max-width: 400px; border-radius: 10px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h2>Your File is Ready ✅</h2>

<img src="{{ asset($image) }}" alt="Generated Image">

<br>

<a href="{{ asset($image) }}" download class="btn">
    ⬇ Download 
</a>

<br><br>

<a href="{{ route('news.create') }}">← Create Another</a>

</body>
</html>