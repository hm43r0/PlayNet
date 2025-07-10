<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    /**
     * Determine if the given video can be updated by the user.
     */
    public function update(User $user, Video $video)
    {
        return $user->id === $video->user_id;
    }

    /**
     * Determine if the given video can be deleted by the user.
     */
    public function delete(User $user, Video $video)
    {
        return $user->id === $video->user_id;
    }
}
