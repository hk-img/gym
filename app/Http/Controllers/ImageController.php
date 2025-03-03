<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageController extends Controller
{
    public function destroy($id)
    {
        $media = Media::find($id);

        if ($media) {
            $media->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
    }
}
