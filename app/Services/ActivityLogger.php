<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $description, string $icon = null, $subject = null): void
    {
        Activity::create([
            'description' => $description,
            'icon' => $icon,
            'user_id' => Auth::id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
        ]);
    }
}