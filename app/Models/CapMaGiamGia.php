<?php

namespace App\Models;

class CapMaGiamGia extends LegacyModel
{
    protected $table = 'capmagiamgia';

    protected $primaryKey = 'CapMaGiamGiaID';

    protected $fillable = [
        'DaSuDung',
        'NgayCap',
        'NgaySuDung',
        'MaGiamGiaID',
        'NguoiDungID',
    ];
}
