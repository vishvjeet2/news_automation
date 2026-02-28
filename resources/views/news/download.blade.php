<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generated File</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: #f5f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .card {
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 420px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }

        img {
            width: 100%;
            max-width: 320px;
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 22px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .btn:hover {
            background: #333;
        }

        .secondary-link {
            display: inline-block;
            margin-top: 18px;
            color: #555;
            text-decoration: none;
            font-size: 14px;
        }

        .secondary-link:hover {
            text-decoration: underline;
        }

        .success {
            color: #28a745;
            font-size: 22px;
        }
    </style>
</head>
<body>

<div class="card">

    <h2>Your File is Ready <span class="success">✓</span></h2>

    <img src="{{ asset($image) }}" alt="Generated Image">

    <br>

    <a href="{{ asset($image) }}" download class="btn">
        ⬇ Download
    </a>

    <br>

    <a href="{{ route('dashboard') }}" class="secondary-link">
        ← Create Another
    </a>

</div>

</body>
</html>