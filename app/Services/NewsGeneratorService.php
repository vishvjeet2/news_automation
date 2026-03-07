<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsOutput;
use App\Models\NewsMedia;
use App\Models\Template;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;

/*
|--------------------------------------------------------------------------
| NewsGeneratorService
|--------------------------------------------------------------------------
|
| WHY THIS SERVICE EXISTS
|
| Previously, all generation logic (image, video, text, TTS, FFmpeg,
| database saving, file handling) was placed inside NewsController.
|
| That approach caused the controller to become very large and tightly
| coupled to business logic. This creates long-term maintenance problems:
|
| 1. Code duplication when admin and user both need same logic.
| 2. Difficult debugging due to mixed responsibilities.
| 3. Hard to scale when adding new templates.
| 4. Risky modifications because everything is interconnected.
|
| WHAT WE CHANGED
|
| - Moved all generation logic into this service class.
| - Controllers now only handle request flow.
| - Business logic is centralized here.
| - Ownership (admin/user) is handled in one place.
| - Reduced duplication.
| - Prepared structure for production.
|
| RESULT
|
| - Clean MVC separation.
| - Shared generation engine for admin and user.
| - Easier to maintain.
| - Safer for production.
|
*/

class NewsGeneratorService
{
    /*
    |--------------------------------------------------------------------------
    | Entry Point
    |--------------------------------------------------------------------------
    |
    | This method receives the request and decides which generation
    | method should run based on template_type.
    |
    | Previously this decision logic was inside controller.
    | Now controller simply calls this service.
    |
    */

    public function generate(Request $request)
    {
        $template = Template::findOrFail($request->template_type);

        switch ($template->type) {

            case 'image':
                return $this->generateImage($request, $template);

            case 'video':
                return $this->generateVideo($request, $template);

            case 'text':
                return $this->generateText($request, $template);

            default:
                abort(400, 'Invalid template type');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Image Generation
    |--------------------------------------------------------------------------
    |
    | Previously inside controller method generateNewswithImage().
    |
    | Enhancements made:
    | - Fixed nullable image extension bug.
    | - Proper ownership handling (admin/user).
    | - Explicit news_type value.
    | - Status automatically set to processed.
    | - Separated media saving logic clearly.
    |
    */

    protected function generateImage(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:70',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:15',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $heading      = $request->heading;
        $description  = $request->description;
        $location     = $request->city;
        $hashtag      = $request->hashtag;
        $category_id  = $request->category_id;

        $catogry_name = Category::where('id', $category_id)->value('name');

        $photoPath = null;
        $stored = null;

        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('temp', 'public');

            $photoFullPath = storage_path('app/public/' . $stored);
            $photoPath = 'data:image/jpeg;base64,' .
                base64_encode(file_get_contents($photoFullPath));
        }

        $extension = $request->hasFile('image')
            ? $request->file('image')->extension()
            : null;

        $templatePath = storage_path('app/public/templates/news_frame.jpeg');
        $template = 'data:image/jpeg;base64,' .
            base64_encode(file_get_contents($templatePath));

        $html = view('news.newsimage', compact(
            'description',
            'heading',
            'template',
            'photoPath',
            'location',
            'hashtag'
        ))->render();

        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'news_' . time() . '.png';
        $absolutePath = $directory . '/' . $filename;

        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($absolutePath);

        $newsData = [
            'category_id' => $category_id,
            'template_id' => $request->template_type,
            'description' => $description,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'category' => $catogry_name,
            'news_type' => 'image',
            'status' => 'draft'
        ];

        $this->assignOwnership($newsData);

        $news = News::create($newsData);

        $relativePath = 'images/' . $filename;

        NewsOutput::create([
            'news_id' => $news->id,
            'output_type' => 'image',
            'file_path' => $relativePath,
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

    /*
    |--------------------------------------------------------------------------
    | Video Generation
    |--------------------------------------------------------------------------
    |
    | Previously inside createvideo() controller method.
    |
    | Enhancements:
    | - Extracted background generator.
    | - Extracted TTS.
    | - Removed hard dependency from controller.
    | - Ownership separated.
    | - Status auto set.
    |
    */

    protected function generateVideo(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:70',
            'description' => 'required|string|max:300',
            'city' => 'required|string|max:20',
            'video' => 'required|file|mimes:mp4,mov,avi,webm|max:20480',
        ]);

        $heading     = $request->heading;
        $description = $request->description;
        $location    = $request->city;
        $hashtag     = $request->hashtag;
        $category_id = $request->category_id;

        $catogry_name = Category::where('id', $category_id)->value('name');

        $imagePath = $this->generateVideoBackground(
            $heading,
            $description,
            $location,
            $hashtag
        );

        $audioPath = $this->generateTTS($description);

        if (!$audioPath || !file_exists($audioPath)) {
            abort(500, 'Audio not generated');
        }

        $storedVideo = $request->file('video')->store('temp_videos', 'public');
        $videoFullPath = storage_path('app/public/' . $storedVideo);
        $extension = $request->file('video')->extension();

        $outputDir = storage_path('app/public/videos');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'output_' . time() . '.mp4';
        $outputFullPath = $outputDir . '/' . $filename;

        $ffmpeg = config('media.ffmpeg_path');

        $command = [
            $ffmpeg,
            '-y',
            '-loop',
            '1',
            '-i',
            $imagePath,
            '-i',
            $videoFullPath,
            '-i',
            $audioPath,
            '-filter_complex',
            "[1:v]scale=480:300[vid];[0:v][vid]overlay=(main_w-overlay_w)/2:main_h-overlay_h-110",
            '-map',
            '0:v',
            '-map',
            '2:a',
            '-t',
            '20',
            '-c:v',
            'libx264',
            '-pix_fmt',
            'yuv420p',
            '-preset',
            'veryfast',
            $outputFullPath
        ];

        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            abort(500, $process->getErrorOutput());
        }

        $newsData = [
            'category_id' => $category_id,
            'template_id' => $request->template_type,
            'description' => $description,
            'heading' => $heading,
            'hashtag' => $hashtag,
            'place' => $location,
            'category' => $catogry_name,
            'news_type' => 'video',
            'status' => 'draft'
        ];

        $this->assignOwnership($newsData);

        $news = News::create($newsData);

        $relativePath = 'videos/' . $filename;

        NewsOutput::create([
            'news_id' => $news->id,
            'output_type' => 'video',
            'file_path' => $relativePath,
        ]);

        NewsMedia::create([
            'news_id' => $news->id,
            'file_path' => $storedVideo,
            'file_type' => $extension
        ]);

        return view('news.download', [
            'image' => asset('storage/' . $relativePath)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Background Generator For Video
    |--------------------------------------------------------------------------
    */

    protected function generateVideoBackground($heading, $description, $location, $hashtag)
    {
        $templatePath = storage_path('app/public/templates/news_frame.jpeg');

        $template = 'data:image/jpeg;base64,' .
            base64_encode(file_get_contents($templatePath));

        $html = view('news.newsvideo', [
            'data' => $description,
            'heading' => $heading,
            'template' => $template,
            'location' => $location,
            'hashtag' => $hashtag
        ])->render();

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

    /*
    |--------------------------------------------------------------------------
    | TTS Generator
    |--------------------------------------------------------------------------
    */

    protected function generateTTS($text)
    {
        if (!$text) {
            return null;
        }

        $savePath = storage_path('app/public/hindi_' . time() . '.mp3');

        $escapedText = escapeshellarg($text);

        $command = "python tts.py $escapedText \"$savePath\"";
        exec($command);

        return file_exists($savePath) ? $savePath : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Ownership Assignment
    |--------------------------------------------------------------------------
    |
    | Ensures only one of user_id or admin_id is filled.
    |
    */

    protected function assignOwnership(array &$newsData)
    {
        if (session()->has('admin_id')) {
            $newsData['admin_id'] = session('admin_id');
            $newsData['user_id'] = null;
        } else {
            $newsData['user_id'] = session('user_id');
            $newsData['admin_id'] = null;
        }
    }

    /*
|--------------------------------------------------------------------------
| Text-Based Poster Generation
|--------------------------------------------------------------------------
|
| Previously this logic was inside NewsController::generateNewstext().
|
| Improvements made:
| - Moved validation here to keep controller thin.
| - Removed category_name misuse in news_type.
| - Added proper ownership handling.
| - Status automatically set to processed.
| - Removed user_id from NewsOutput (not needed).
| - Standardized file path handling.
|
*/

    protected function generateText(Request $request)
    {
        if (empty($request->description)) {
            $request->validate([
                'heading' => 'required|string|max:355',
                'city' => 'required|string|max:100',
            ]);
        } else {
            $request->validate([
                'heading' => 'required|string|max:70',
                'description' => 'nullable|string|max:400',
                'city' => 'required|string|max:100',
            ]);
        }

        $heading      = $request->heading;
        $description  = $request->description;
        $location     = $request->city;
        $hashtag      = $request->hashtag;
        $category_id  = $request->category_id;

        $catogry_name = Category::where('id', $category_id)->value('name');

        $templatePath = storage_path('app/public/templates/news_frame.jpeg');

        $html = view('news.newstext', [
            'data'     => $description,
            'heading'  => $heading,
            'template' => $templatePath,
            'location' => $location,
            'hashtag'  => $hashtag
        ])->render();

        $directory = storage_path('app/public/images');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'news_' . time() . '.jpeg';
        $absolutePath = $directory . '/' . $filename;

        Browsershot::html($html)
            ->windowSize(800, 1000)
            ->save($absolutePath);

        $newsData = [
            'category_id' => $category_id,
            'template_id' => $request->template_type,
            'description' => $description,
            'heading'     => $heading,
            'hashtag'     => $hashtag,
            'place'       => $location,
            'category'   => $catogry_name,
            'news_type'  => 'text',
            'status'      => 'draft'
        ];

        $this->assignOwnership($newsData);

        $news = News::create($newsData);

        $relativePath = 'images/' . $filename;

        NewsOutput::create([
            'news_id'     => $news->id,
            'output_type' => 'image',
            'file_path'   => $relativePath
        ]);

        return view('news.download', [
            'image'  => asset('storage/' . $relativePath),
            'newsID' => $news->id
        ]);
    }
}
