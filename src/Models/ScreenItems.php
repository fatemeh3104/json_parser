<?php

namespace ProcessMaker\Package\Utils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use ProcessMaker\Models\ProcessMakerModel;

class ScreenItems extends ProcessMakerModel
{
    use HasFactory;
    protected $table = 'screen_items';

    protected $fillable = [
        'id', 'name', 'status',
    ];


    public function validations()
    {
        return $this->hasMany(ItemsValidation::class);
    }
}
