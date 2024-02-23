<?php

namespace ProcessMaker\Package\Parssconfig\Models;

use ProcessMaker\Models\ProcessMakerModel;

class ScreenItems extends ProcessMakerModel
{
    protected $table = 'screen_items';

    protected $fillable = [
        'id', 'name', 'status',
    ];


    public function validations()
    {
        return $this->hasMany(ItemsValidation::class);
    }
}
