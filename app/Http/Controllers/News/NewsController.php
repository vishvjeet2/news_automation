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
use App\Models\NewsOutput;
use App\Models\Template;
use Carbon\Traits\Cast;


class NewsController extends Controller
{

    public function newstype(Request $request){

        
        $type = $request->template_type;

        // redirecting as per selected news type
        if ($type == '1') {
            
            return $this->generateNewswithImage($request);
        }
    
        if ($type == '2') {

            return $this->createvideo($request);
        }
        if ($type == '3') {
            
            return $this->generateNewstext($request);
        }
        
    }

    
    public function createvideo(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:70',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:20',
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:20480',
        ]);

        

        $heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;
        $category_id = $request->category_id;

        $category_name = Category::where('id', $category_id)->value('name');

        // 1️⃣ generate background image
        $imagePath = $this->generateNewsvideo($request);

        // 2️⃣ generate audio
        $audioPath = $this->hindiTTS($request);


        if (!$audioPath || !file_exists($audioPath)) {
            abort(500, 'Audio not generated');
        }

        // 3️⃣ store uploaded video
        $storedVideo = $request->file('video')->store('temp_videos', 'public');
        $videoFullPath = storage_path('app/public/' . $storedVideo);
        $extension = $request->file('video')->extension();

        if (!file_exists($videoFullPath)) {
            abort(500, 'Video missing');
        }

        // ensure output directory exists
        $outputDir = storage_path('app/public/videos');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'output_' . time() . '.mp4';
        $outputFullPath = $outputDir . '/' . $filename;

        // ffmpeg path
        $ffmpeg = 'C:\\ffmpeg\\bin\\ffmpeg.exe';

        $command = [
            $ffmpeg,
            '-y',
            '-loop', '1',
            '-i', $imagePath,
            '-i', $videoFullPath,
            '-i', $audioPath,
            '-filter_complex',
            "[1:v]scale=480:300[vid];[0:v][vid]overlay=(main_w-overlay_w)/2:main_h-overlay_h-110",
            '-map', '0:v',
            '-map', '2:a',
            '-t', '20',
            '-c:v', 'libx264',
            '-pix_fmt', 'yuv420p',
            '-preset', 'veryfast',
            $outputFullPath
        ];

        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            abort(500, $process->getErrorOutput());
        }

        // save news
        $news = News::create([
            'category_id' => $category_id,
            'user_id' => session('user_id'),
            'template_id' => $request->template_type,
            'description' => $data,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'news_type' => $category_name,
        ]);

        // store relative path (production safe)
        $relativePath = 'videos/' . $filename;

        //stores output video
        NewsOutput::create([
            'news_id' => $news->id,
            'user_id' => session('user_id'),
            'output_type' => 'video',
            'file_path' => $relativePath,
            'is_primary' => 1
        ]);

        // stored user given video
        NewsMedia::create([
            'news_id' => $news->id,
            'file_path' => $storedVideo,
            'file_type' => $extension
        ]);

        return view('news.download', [
            'image' => asset('storage/' . $relativePath)
        ]);
    }

    
    public function generateNewswithImage(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:60',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $heading   = $request->heading;
        $data      = $request->description;
        $location  = $request->city;
        $hashtag   = $request->hashtag;
        $category_id = $request->category_id;

        $category_name = Category::where('id', $category_id)->value('name');

        // upload user image (optional)
        $photoPath = null;
        $stored = null;
        

        if ($request->hasFile('image')) {
            // the file uploded by user stored in temp folder. 
            $stored = $request->file('image')->store('temp', 'public');

            $photoFullPath = storage_path('app/public/' . $stored);
            $photoPath = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoFullPath));
        }
        // extention of file uploded by user.
        $extension = $request->file('image')->extension();


        $templatePath = storage_path('app/public/templates/news_frame.jpeg');
        $template = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($templatePath));

        $html = view('news.newsimage', compact(
            'data','heading','template','photoPath','location','hashtag'
        ))->render();

        // ensure storage folder exists
        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'news_' . time() . '.png';

        // absolute path for saving file
        $absolutePath = $directory . '/' . $filename;

        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($absolutePath);

        
        $news = News::create([
            'category_id' => $category_id,
            'user_id' => session('user_id'),
            'template_id' => $request->template_type,
            'description' => $data,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'news_type' => $category_name,
        ]);

        // store relative path (production safe)
        $relativePath = 'images/' . $filename;

        // output image stored in this table
        NewsOutput::create([
            'user_id' => session('user_id'),
            'news_id' => $news->id,
            'output_type' => 'image',
            'file_path' => $relativePath,
            'is_primary' => 1
        ]);

        NewsMedia::create([
            'news_id' => $news->id,
            'file_path' => $stored,
            'file_type' => $extension
        ]);



        return view('news.download', [
            'image' => asset('storage/' . $relativePath),
            'newsID' => $news->id
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
        

        $$heading = $request->heading;
        $data = $request->description;
        $location = $request->city;
        $hashtag = $request->hashtag;
        $catogry_id = $request->category_id;    // carogary 
        $catogry_name = Category::where('id', $catogry_id)->value('name'); // name of the catagory


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

        News::create([
            'category_id' => $catogry_id,
            'user_id' => $user_id,
            'template_id' => $request->template_type,
            'description' => $data,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'news_type' => $catogry_name,
        ]); 

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

        $command = "python tts.py $escapedText";
        exec($command);

        return $savePath;   // ✅ return real file path
    }

    public function fetch_data(){
        $categories = Category::all();   // fetch categories
        $templetName = Template::all(); // templet name

        return view('news.news', compact('categories', 'templetName'));
    }
    
    public function insertnewscatogarytype(Request $request){
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string'
        ]);

        $exists = Category::where('name', $request->name)
                    ->orWhere('slug', $request->slug)
                    ->exists();

        if ($exists) {
            return back()->with('error_msg', 'This category already exists');
        }

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        return back()->with('success', 'Category added successfully');
    }

    public function download($id)
    {
        $media = NewsOutput::where('news_id', $id)->first();

        if (!$media) {
            abort(404, 'Media not found in DB');
        }

        $filePath = storage_path('app/public/' . $media->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File missing on server');
        }

        return response()->download($filePath);
    }
    
}
?>