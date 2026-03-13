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
            width: 205mm;
            height: 260mm;
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
            top: 40%;
            padding-left: 40px;
            padding-right: 40px;
            text-align: center;
            /* border: 5px solid palegreen; */
        }

        .heading {
            font-size: 50px;
            font-weight: bold;
            color: #ffffff;
            height: 100px;
            text-align: center;
            line-height: 1.2;
            font-family: 'Khand';
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

        .photo-container{
            position:absolute;
            top:150%;
            width:600px;
            left:50%;
            transform:translateX(-50%);
            /* border:4px solid palegreen; */

            display:grid;
            grid-template-columns: repeat(2, 1fr);
            gap:10px;
        }

        .photo{
            width:100%;
            height:250px;
        }

        .photo img{
            width:100%;
            height:100%;
            object-fit:contain;
        }

        /* If there are exactly 3 images, make the 3rd one full width */
        .photo-container .photo:last-child:nth-child(odd){
            grid-column: span 2;
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

        {{-- <div class="text">
            {{ $description }}
        </div> --}}

        @if(!empty($photoPaths))
        <div class="photo-container">

            @foreach($photoPaths as $photo)
                <div class="photo">
                    <img src="{{ $photo }}" alt="news photo">
                </div>
            @endforeach

        </div>
        @endif

    </div>

</div>

</body>
</html>
