<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;


class Company extends Model
{
    protected $fillable = [
        'ru_title', 'uz_title', 'en_title',
        'ru_description', 'uz_description', 'en_description',
        'url', 'user_id', 'active', 'phone_number', 'geo_location', 'logo_url', 'category_id',
        'need_id', 'advantages', 'telegram_link', 'facebook_link', 'instagram_link'
    ];

    const UPLOAD_DIRECTORY = 'uploads/companies/';

    const UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY = 'uploads/bad_quality_companies/';

    public function category()
    {
        return $this->hasOne(HandbookCategory::class, 'id', 'category_id');
    }

    /**
     * User clicks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userClicks()
    {
        return $this->hasMany(UserClick::class, 'company_id', 'id');
    }

    /**
     * Creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Need type for this company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function needType()
    {
        return $this->hasOne(NeedType::class, 'id', 'need_id');
    }

    /**
     * Company's services
    */
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * Check if company has service
     *
     * @param int $serviceId
     * @return boolean
    */
    public function hasService(int $serviceId)
    {
        return null !== $this->services()->find($serviceId);
    }

    /**
     * Upload an image and save it in file storage
     *
     * @param $image
     */
    public function uploadImage($image)
    {
        if (!$image) return;

        $this->removeImages();
        $filename = $this->generateFileName($image->extension());
        $badImagefilename = $this->generateFileName($image->extension());
        $image->storeAs(self::UPLOAD_DIRECTORY, $filename);
        $this->createDirectory();
        $img = Image::make(public_path() . '/' . self::UPLOAD_DIRECTORY . $filename);
        $img->save(public_path() . '/' . self::UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY . $badImagefilename, 10);
        $this->saveImageName($filename);
        $this->savePoorImageName($badImagefilename);
    }

    public function createDirectory()
    {
        if(!File::exists(public_path() . '/' . self::UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY))
        {
            File::makeDirectory(public_path() . '/' . self::UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY, 0777, true, true);
        }
    }

    /**
     * Generate filename for image
     *
     * @param string $imageName
     * @return string
     */
    private function generateFileName(string $imageName)
    {
        return str_random(20) . '.' .  $imageName;
    }

    /**
     * Get image filename
     *
     * @return string
     */
    public function getImage()
    {
        if ($this->image)
            return '/' . self::UPLOAD_DIRECTORY . $this->image;
        else
            return '';
    }

    /**
     * Get poor image
     *
     * @return string
     */
    public function getPoorImage()
    {
        if ($this->bad_quality_image)
            return '/' . self::UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY . $this->bad_quality_image;
        else
            return '';
    }

    /**
     * Remove an image
     *
     * @return void
     */
    public function removeImages()
    {
        if ($this->bad_quality_image) {
            Storage::delete(self::UPLOAD_BAD_QUALITY_IMAGE_DIRECTORY . $this->bad_quality_image);
            $this->savePoorImageName('');
        }
        if ($this->image) {
            Storage::delete(self::UPLOAD_DIRECTORY . $this->image);
            $this->saveImageName('');
        }
    }

        /**
     * Save an image name to the database
     *
     * @param string $imageName
     * @return void
     */
    private function saveImageName(string $imageName)
    {
        $this->image = $imageName;
        $this->save();
    }

    /**
     * Save poor quality image name
     *
     * @param string $imageName
     */
    private function savePoorImageName(string $imageName)
    {
        $this->bad_quality_image = $imageName;
        $this->save();
    }

    /**
     * Override delete method to delete image too
     *
     * @return void
     * @throws \Exception
     */
    public function delete()
    {
        $this->removeImages();
        parent::delete();
    }

    /**
     * Get cleand title
     *
     * @return string
     */
    public function getTitle()
    {
        return strip_tags($this->ru_title);
    }

    /**
     * Check if company has advantages or any social link
     *
     * @return boolean
     */
    public function hasAdvantagesOrAnySocialLink()
    {
        return !empty($this->advantages) or (!empty($this->telegram_link) or !empty($this->instagram_link) or !empty($this->facebook_link));
    }

    /**
     * Check if company has advantages
     *
     * @return  boolean
     */
    public function hasAdvatages()
    {
        return !empty($this->advantages);
    }

    /**
     * Check if company has concrete social link
     *
     * @param string $socialName Name of social web
     *
     * @return boolean
     */
    public function hasSocialLink(string $socialName)
    {
        if ($socialName == 'telegram')
            return !empty($this->telegram_link);
        if ($socialName == 'instagram')
            return !empty($this->instagram_link);
        if ($socialName == 'facebook')
            return !empty($this->facebook_link);
    }

    /**
     * Check if company has geolocation
     *
     * @return boolean
     */
    public function hasGeolocation()
    {
        return !empty($this->geo_position_x) and !empty($this->geo_position_y);
    }

    /**
     * Check if company has address
     *
     * @return boolean
     */
    public function hasAddress()
    {
        return !empty($this->address);
    }

    /**
     * Check if company has phone number
     *
     * @return boolean
     */
    public function hasPhoneNumber()
    {
        return !empty($this->phone_number);
    }

    /**
     * Check if company has url
     *
     * @return boolean
     */
    public function hasUrl()
    {
        return !empty($this->url);
    }

    /**
     * Get advantages as array
     *
     * @return array Array of advantages
     */
    public function advantagesAsArray()
    {
        return explode(', ', $this->advantages);
    }
}
