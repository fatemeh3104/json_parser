<?php

namespace ProcessMaker\Package\Parssconfig\Models;

use ProcessMaker\Models\ProcessMakerModel;

class ItemsValidation extends ProcessMakerModel
{
    protected $table = 'item_validations';

    protected $fillable = [
        'id', 'name', 'status',
    ];
    protected $casts = [
        'validation' => 'array'
    ];
    public function screenItems()
    {
        return $this->hasOne(ScreenItems::class);

    }
}
