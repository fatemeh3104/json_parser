<?php

namespace ProcessMaker\Package\Parssconfig\Models;

use ProcessMaker\Models\ProcessMakerModel;

class Sample extends ProcessMakerModel
{
    protected $table = 'sample_skeleton';

    protected $fillable = [
        'id', 'name', 'status',
    ];
}
