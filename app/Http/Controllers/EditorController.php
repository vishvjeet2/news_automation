<?php
// app/Http/Controllers/EditorController.php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Template;
use App\Models\UploadedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditorController extends Controller
{
    /**
     * Show list of all designs
     */
    public function list()
    {
        $designs = Design::orderBy('updated_at', 'desc')->paginate(12);
        return view('Editor.list', compact('designs'));
    }

    /**
     * Show the editor (new design)
     */
    public function index()
    {
        $uploadedImages = UploadedImage::orderBy('created_at', 'desc')->get();
        $fonts = $this->getAvailableFonts();
        
        return view('Editor.index', [
            'design' => null,
            'uploadedImages' => $uploadedImages,
            'fonts' => $fonts
        ]);
    }

    /**
     * Edit existing design
     */
    public function edit(Design $design)
    {
        $uploadedImages = UploadedImage::orderBy('created_at', 'desc')->get();
        $templets = Template::all();
        $fonts = $this->getAvailableFonts();
        
        return view('Editor.index', [
            'templates' => $templets,
            'design' => $design,
            'uploadedImages' => $uploadedImages,
            'fonts' => $fonts
        ]);
    }

    /**
     * Save design
     */
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'design_data' => 'required',
            'width' => 'required|integer',
            'height' => 'required|integer',
            'thumbnail' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'design_data' => $request->design_data,
            'width' => $request->width,
            'height' => $request->height,
        ];

        // Save thumbnail if provided
        if ($request->thumbnail) {
            $thumbnailData = $request->thumbnail;
            $thumbnailData = str_replace('data:image/png;base64,', '', $thumbnailData);
            $thumbnailData = str_replace(' ', '+', $thumbnailData);
            $thumbnailName = 'thumbnails/' . Str::uuid() . '.png';
            Storage::disk('public')->put($thumbnailName, base64_decode($thumbnailData));
            $data['thumbnail'] = $thumbnailName;
        }

        if ($request->design_id) {
            $design = Design::findOrFail($request->design_id);
            // Delete old thumbnail
            if ($design->thumbnail && $request->thumbnail) {
                Storage::disk('public')->delete($design->thumbnail);
            }
            $design->update($data);
        } else {
            $design = Design::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Design saved successfully!',
            'design_id' => $design->id
        ]);
    }

    /**
     * Upload image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240'
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        $uploadedImage = UploadedImage::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        return response()->json([
            'success' => true,
            'image' => [
                'id' => $uploadedImage->id,
                'url' => Storage::url($path),
                'filename' => $uploadedImage->filename
            ]
        ]);
    }

    /**
     * Delete uploaded image
     */
    public function deleteImage(UploadedImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);
    }

    /**
     * Delete design
     */
    public function delete(Design $design)
    {
        if ($design->thumbnail) {
            Storage::disk('public')->delete($design->thumbnail);
        }
        $design->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully!'
        ]);
    }

    /**
     * Export design as image
     */
    public function export(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
            'format' => 'required|in:png,jpg',
            'filename' => 'required|string'
        ]);

        $imageData = $request->image_data;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        $filename = $request->filename . '.' . $request->format;

        return response(base64_decode($imageData))
            ->header('Content-Type', $request->format === 'png' ? 'image/png' : 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get available fonts
     */
    private function getAvailableFonts()
    {
        return [
            'Arial',
            'Arial Black',
            'Helvetica',
            'Times New Roman',
            'Georgia',
            'Verdana',
            'Courier New',
            'Comic Sans MS',
            'Impact',
            'Trebuchet MS',
            'Palatino Linotype',
            'Lucida Sans Unicode',
            'Tahoma',
            'Century Gothic',
            'Bookman Old Style',
            'Garamond',
            'Brush Script MT'
        ];
    }
}