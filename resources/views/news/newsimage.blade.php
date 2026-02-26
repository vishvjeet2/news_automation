<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>News PDF</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Devanagari:wght@100..800&family=Noto+Sans+Devanagari:wght@100..900&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Anek+Devanagari:wght@500&family=Khand:wght@500;600;700&family=Noto+Sans+Devanagari:wght@100..900&display=swap');
        @page { 
            margin: 0; 
            size: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans Devanagari', 'Anek Devanagari', sans-serif;
        }

        .container {
            position: relative;
            width: 215mm;
            height: 265mm;
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

        .heading {
            font-size: 38px;
            font-weight: bold;
            color: #ffffff;
            height: 100px;
            text-align: center;
            line-height: 1.2;
            font-family: 'Anek Devanagari';
            text-decoration: underline;
        }
        .location {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 25px;
            text-align: center;
            line-height: 1.2;
            font-family: 'Anek Devanagari';
            text-decoration: underline;
        }

        .text {
            font-size: 26px;
            line-height: 38px;
            font-weight: 550;
            color: #ffffff;
            height: 150;
            text-align: center;
            letter-spacing: 1.5px;
            font-family: "Khand", sans-serif;
            overflow: hidden;
            word-wrap: break-word;
        }

        .photo {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 102%;   /* 🔥 move image downward */
            width: 400px;
            height: 300px;
            /* border: 5px solid #ffffff; */
            box-sizing: border-box;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: contain;   /* ✅ shows full image */
            background: transparent;   /* ✅ transparent */
        }

        .top-left-text {
            position: absolute;
            top: 85px;        /* Adjust vertical position */
            left: 25px;       /* Adjust horizontal position */
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            z-index: 20;
            font-family: 'Anek Devanagari';
        }

        .top-left-text-hashtag {
            position: absolute;
            top: 20px;        /* Adjust vertical position */
            left: 25px;       /* Adjust horizontal position */
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            z-index: 20;
            font-family: 'Anek Devanagari';
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
    <div class="content">

        <div class="heading">
            {{ $heading }}
        </div>

        <div class="text">
            {{ $data }}
        </div>

        <div class="photo">
            <img src="{{ $photoPath }}" alt="news photo">
        </div>

    </div>

</div>

</body>
</html>