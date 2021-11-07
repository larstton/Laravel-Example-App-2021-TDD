<?php

namespace App\Http\Loophole\Controllers\StatusPage;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use Intervention\Image\Facades\Image;

class StatusPageImageController extends Controller
{
    public function __invoke(StatusPage $statusPage)
    {
        if (blank($statusPage->image)) {
            $this->errorNotFound();
        }

        return Image::make($statusPage->image)->response($statusPage->image_content_type);
    }
}
