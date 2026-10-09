<?php

namespace App\Models;

class BienThe extends LegacyModel
{
    protected $table = 'bienthe';

    protected $primaryKey = 'BienTheID';

    protected $fillable = [
        'MaSKU',
        'MauSac',
        'KichThuoc',
        'GiaBienThe',
        'SoLuong',
        'SoLuongTamGiu',
        'SanPhamID',
    ];
}
