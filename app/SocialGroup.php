<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialGroup extends Model
{
    protected $guarded = [];

    public function users() {
        return $this->belongsToMany(SocialUser::class, 'group_has_user', 'group_id', 'user_id');
    }
}
