<?php

namespace App\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use Intervention\Image\Facades\Image;

trait FileHandlerTrait
{
    /**
     * Process User Image
     *
     * @param Request $request
     * @param $key
     * @param $path
     * @param $width
     * @param $height
     * @param $thumbDetails
     *
     * @param $crop_resize = false -> nothing will happen
     * @param $crop_resize = crop -> will crop only
     * @param $crop_resize = resize -> will resize only
     * @param $crop_resize = resize -> will resize only
     * @param $crop_resize = both -> will do both
     *
     * @return null|string
     */
    protected function processImage(Request $request, $key, $path, $width, $height, $thumbDetails = [], $crop_resize = false)
    {
        try {
            $fileName = null;
            if ($request->hasFile($key)) {
                $fileName = uniqid() . Str::random(5) . time() . '.' . $request->file($key)->getClientOriginalExtension();

                if (!$this->isReallyImage($request->file($key)->getClientOriginalExtension())) {
                    // throw new \Exception("NOT AN IMAGE!");
                    return null;
                }

                if ($crop_resize) {
                    if ($crop_resize === 'crop') {
                        $image_thumb = Image::make($request->file($key))->crop($width, $height);
                    } elseif ($crop_resize === 'resize') {
                        $image_thumb = Image::make($request->file($key))->resize($width, $height);
                    } elseif ($crop_resize === 'both') {
                        $image_thumb = Image::make($request->file($key))
                            ->crop($width, $height)
                            ->resize($width, $height);
                    } else {
                        throw new \Exception('Wrong parameter for resize and crop action.');
                    }

                } else {
                    $image_thumb = Image::make($request->file($key));
                }

                $image_thumb = $image_thumb->stream();

                Storage::disk('public')->put($path . $fileName, $image_thumb->__toString(), 'public');

                if (count($thumbDetails) > 0) {
                    foreach ($thumbDetails as $thumb) {
                        if ($thumb['crop_resize']) {
                            if ($thumb['crop_resize'] === 'crop') {
                                $thumbFile = Image::make($request->file($key))->crop($thumb['width'], $thumb['height']);

                            } elseif ($thumb['crop_resize'] === 'resize') {
                                $thumbFile = Image::make($request->file($key))->resize($thumb['width'], $thumb['height']);

                            } elseif ($thumb['crop_resize'] === 'both') {

                                $thumbFile = Image::make($request->file($key))
                                    ->crop($thumb['width'], $thumb['height'])
                                    ->resize($thumb['width'], $thumb['height']);
                            } else {
                                throw new \Exception('Wrong parameter in thumbnail for resize and crop action.');
                            }

                        } else {
                            $thumbFile = Image::make($request->file($key));
                        }
                        $thumbFile = $thumbFile->stream();

                        Storage::disk('public')->put($path . $thumb['path'] . $fileName, $thumbFile->__toString(), 'public');
                    }
                }
                //return config('filesystems.disks.public.url') . $path . $fileName;
                return $path . $fileName;
            }
            return null;

        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

    protected function isReallyImage($extension)
    {
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {

            return true;
        }

        return false;
    }
}
