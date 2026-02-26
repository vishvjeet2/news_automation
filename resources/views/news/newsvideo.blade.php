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

        .heading {
            font-size: 40px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 10px;
            margin-bottom: 20px;
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
            font-size: 25px;
            line-height: 35px;
            color: #ffffff;
            margin-top: 10px;
            margin-bottom: 35px;
            text-align: center;
            letter-spacing: 1.5px;
            padding: 0 30px;
            font-family: 'Anek Devanagari';
            word-wrap: break-word;
            overflow: hidden;
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


    </div>

</div>

</body>
</html>