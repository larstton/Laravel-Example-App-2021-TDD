<?php

namespace App\Http\Controllers\StatusPage;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class StatusPageImageController extends Controller
{
    public function store(Request $request, StatusPage $statusPage)
    {
        $this->authorize($statusPage);

        $this->validate($request, [
            'image' => 'required|file|image|max:300',
        ]);

        try {
            $statusPage->update([
                'image'              => File::get($request->file('image')->getPathname()),
                'image_content_type' => $request->file('image')->getMimeType(),
            ]);
        } catch (Exception $exception) {
            $this->error($exception->getMessage(), $exception->getCode());
        }

        return $this->created();
    }

    public function show(StatusPage $statusPage)
    {
        $this->authorize($statusPage);

        if (blank($statusPage->image)) {
            $this->errorNotFound();
        }

        return Image::make($statusPage->image)->response($statusPage->image_content_type);
    }

    public function destroy(StatusPage $statusPage)
    {
        $this->authorize($statusPage);

        $statusPage->update([
            'image'              => null,
            'image_content_type' => null,
        ]);

        return $this->noContent();
    }
}
