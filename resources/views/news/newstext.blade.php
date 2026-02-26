<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>News PDF</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Khand:wght@400;600;700&family=Anek+Devanagari:wght@100..800&family=Noto+Sans+Devanagari:wght@100..900&display=swap" rel="stylesheet">

    <style>
        @page { 
            margin: 0; 
            size: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Khand', sans-serif;
        }

        .container {
            position: relative;
            width: 215mm;
            height: 255mm;
            overflow: hidden;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .content {
            position: relative;
            
            z-index: 10;
            padding-top: 330px;
            padding-left: 40px;
            padding-right: 40px;
            text-align: center;
        }

        /* when description is missing */
        .no-description {
            padding-top: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .heading {
            font-size: 40px;
            font-weight: 700;
            color: #ffffff;
            margin-top: 20px;
            margin-bottom: 40px;
            text-align: center;
            line-height: 1.2;
            font-family: 'Khand', sans-serif;
            text-decoration: underline;
        }

        /* heading expands when no description */
        .full-heading {
            padding-top: 90px;
            height: 350px;
            border: 2px solid black;
            font-size: 56px;
            overflow: hidden;
            line-height: 1.3;
            margin: 0;
        }

        .location {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 25px;
            text-align: center;
            font-family: 'Khand';
            text-decoration: underline;
        }

        .text {
            font-size: 35px;
            /* line-height: 38px; */
            height: 350px;
            color: #ffffff;
            margin-top: 20px;
            margin-bottom: 35px;
            text-align: center;
            letter-spacing: 1.5px;
            padding: 0 30px;
            font-family: 'Khand', sans-serif;
            font-weight: 600;
            word-wrap: break-word;
            overflow: hidden;
        }

        .top-left-text {
            position: absolute;
            top: 85px;
            left: 25px;
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            z-index: 20;
            font-family: 'Khand';
        }

        .top-left-text-hashtag {
            position: absolute;
            top: 20px;
            left: 25px;
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            z-index: 20;
            font-family: 'Khand';
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Background Image -->
    <img src="{{ $template }}" class="background" alt="background">

    <div class="top-left-text-hashtag">
        {{ $hashtag }}
    </div>

    <div class="top-left-text">
        {{ $location }}
    </div>

    <!-- Content Overlay -->
    <div class="content {{ empty($data) ? 'no-description' : '' }}">

        <div class="heading {{ empty($data) ? 'full-heading' : '' }}">
            {{ $heading }}
        </div>

        @if(!empty($data))
        <div class="text">
            {{ $data }}
        </div>
        @endif

    </div>

</div>

</body>
</html>