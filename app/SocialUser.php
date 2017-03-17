<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model
{
    protected $guarded = [];

    public function groups() {
        return $this->belongsToMany(SocialGroup::class, 'group_has_group', 'user_id', 'group_id');
    }

    public function friends() {
        return $this->belongsToMany(SocialUser::class, 'user_has_user', 'user_id', 'friend_id');
    }
}
