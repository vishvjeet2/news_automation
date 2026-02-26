<?php

namespace App\Http\Controllers\News;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use FluentVox\Speech;
use App\Models\Category;
use App\Models\News;
use App\Models\NewsMedia;
use App\Models\Template;


class NewsController extends Controller
{

    public function newstype(Request $request){

        
        $type = $request->template_type;

        // redirecting as per selected news type
        if ($type == '2') {
            
            return $this->generateNewswithImage($request);
        }
    
        if ($type == '4') {

            return $this->createvideo($request);
        }
        if ($type == '3') {
            
            return $this->generateNewstext($request);
        }
        
    }

    
    public function createvideo(Request $request){
        $request->validate([
            'heading' => 'required|string|max:50',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:20480',
        ]);

        $heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;
        $catogry_id = $request->category_id;    // carogary 
        $catogry_name = Category::where('id', $catogry_id)->value('name'); // name of the catagory

        
    
        // 1️⃣ generate image
        $imagePath = $this->generateNewsvideo($request);

        // generating audio from description.
        $audioPath = $this->hindiTTS($request);

        if (!$audioPath || !file_exists($audioPath)) {
            dd('Audio not generated');
        }
    
        // 2️⃣ store uploaded video
        $videoFile = $request->file('video')->store('temp_videos', 'public');
        $extension = $request->file('video')->extension();
    
        $videoPath = storage_path('app/public/' . $videoFile);
        $outputPath = storage_path('app/public/videos/output.mp4');
    
        if (!file_exists($videoPath)) {
            dd('Video missing');
        }
    
        // 3️⃣ overlay video on image
        $ffmpeg = 'C:\\ffmpeg\\bin\\ffmpeg.exe';
    
        $command = [
            $ffmpeg,
            '-y',
            '-loop', '1',                 // loop image as video background
            '-i', $imagePath,
            '-i', $videoPath,
            '-i', $audioPath,
            '-filter_complex',
            "[1:v]scale=480:300[vid];[0:v][vid]overlay=(main_w-overlay_w)/2:main_h-overlay_h-110",

            '-map', '0:v',
            '-map', '2:a',   // ✅ map custom audio

            '-t', '20',                   // duration (seconds)
            '-c:v', 'libx264',
            '-pix_fmt', 'yuv420p',
            '-preset', 'veryfast',
            $outputPath
        ];
    
        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();
    
        if (!$process->isSuccessful()) {
            dd($process->getErrorOutput());
        }

        $news = News::create([
            'category_id' => $catogry_id,
            'template_id' => $request->template_type,
            'description' => $data,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'news_type' => $catogry_name,
        ]);  
        
        NewsMedia::create([
            'news_id' => $news->id,
            'file_path' => $outputPath,
            'file_type' => $extension,
            'is_primary' => 1
        ]);
    
        return view('news.download', [
            'image' => 'storage/videos/' . basename($outputPath)
        ]);
    }

    
    public function generateNewswithImage(Request $request){
        $request->validate([
            'heading' => 'required|string|max:60',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;
        $catogry_id = $request->category_id;    // carogary 
        $catogry_name = Category::where('id', $catogry_id)->value('name'); // name of the catagory

        $extension = $request->file('image')->extension();

        // store uploaded image
        $photoPath = null;
        if ($request->hasFile('image')) {
            $photoPath = $request->file('image')->store('temp', 'public');
            $photoPath = storage_path('app/public/' . $photoPath);
        }

        $template = storage_path('app/private/news_frame.jpeg');

        // dd($photoPath);
        $html = view('news.newsimage', compact('data','heading','template','photoPath','location','hashtag'))->render();


        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $imagePath = $directory . '/news_' . time() . '.jpeg';

        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($imagePath);
        
         
        $news = News::create([
            'category_id' => $catogry_id,
            'template_id' => $request->template_type,
            'description' => $data,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'news_type' => $catogry_name,
        ]);  
        
        NewsMedia::create([
            'news_id' => $news->id,
            'file_path' => $imagePath,
            'file_type' => $extension,
            'is_primary' => 1
        ]);
        
        return view('news.download', [
            'image' => 'storage/images/' . basename($imagePath)
        ]);
    }



    public function generateNewstext(Request $request){
        if (empty($request->description)) {
            $request->validate([
                'heading' => 'required|string|max:355',
                'city' => 'required|string|max:100',
            ]);
        }
        else{
            $request->validate([
                'heading' => 'required|string|max:70',
                'description' => 'nullable|string|max:400',
                'city' => 'required|string|max:100',
            ]);
        }
        

        $heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;


        $template = storage_path('app/private/news_frame.jpeg');

        // dd($photoPath);
        $html = view('news.newstext', compact('data','heading','template','location','hashtag'))->render();


        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $imagePath = $directory . '/news_' . time() . '.jpeg';


        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($imagePath);

        return view('news.download', [
            'image' => 'storage/images/' . basename($imagePath)
        ]);
    }


    public function generateNewsvideo(Request $request){
        
        $heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;


        $template = storage_path('app/private/news_frame.jpeg');

        // dd($photoPath);
        $html = view('news.newsvideo', compact('data','heading','template','location','hashtag'))->render();


        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $imagePath = $directory . '/news_' . time() . '.jpeg';


        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($imagePath);

        return $imagePath;
    }


    public function hindiTTS(Request $request){
        $text = $request->input('text', $request->description);

        if (!$text) {
            return null;
        }

        // save inside storage/app/public
        $savePath = storage_path('app/public/hindi.mp3');

        $escapedText = escapeshellarg($text);

        $command = "python tts.py $escapedText \"$savePath\"";
        exec($command);

        return $savePath;   // ✅ return real file path
    }

    public function fetch_data()
    {
        $categories = Category::all();   // fetch categories
        $templetName = Template::all(); // templet name

        return view('news.news', compact('categories', 'templetName'));
    }
    
}
?>