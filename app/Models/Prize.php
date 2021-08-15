<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
    ];

    protected $appends = [
        'image_url',
        'full_title',
    ];

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }
        return rtrim(env('APP_URL'), '/') . '/storage/' . $this->image;
    }

    /**
     * @return mixed|string
     */
    public function getFullTitleAttribute()
    {
        $fullTitle = $this->title;
        if ($this->subtitle) {
            $fullTitle .= " {$this->subtitle}";
        }
        return $fullTitle;
    }
}
