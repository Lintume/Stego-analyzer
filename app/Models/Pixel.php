<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    private $r;
    private $g;
    private $b;

    public function __construct($r, $g, $b)
    {
        $this->r = ($r > 255) ? 255 : (($r < 0) ? 0 : (int)($r));
        $this->g = ($g > 255) ? 255 : (($g < 0) ? 0 : (int)($g));
        $this->b = ($b > 255) ? 255 : (($b < 0) ? 0 : (int)($b));
    }
}
