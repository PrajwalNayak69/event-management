<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'location', 'start_time', 'end_time', 'capacity'];

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    // 🔹 Mutators: Ensure all times are stored in UTC
    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = Carbon::parse($value, 'Asia/Kolkata')->utc();
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = Carbon::parse($value, 'Asia/Kolkata')->utc();
    }

    // 🔹 Accessors: Convert UTC to requested timezone dynamically
    public function getStartTimeAttribute($value)
    {
        $tz = request()->query('timezone', 'Asia/Kolkata'); // default IST
        return Carbon::parse($value)->setTimezone($tz)->toDateTimeString();
    }

    public function getEndTimeAttribute($value)
    {
        $tz = request()->query('timezone', 'Asia/Kolkata'); // default IST
        return Carbon::parse($value)->setTimezone($tz)->toDateTimeString();
    }
}
