<?php

namespace App\Models;

class ChiTietGioHang extends LegacyModel
{
    protected $table = 'chitietgiohang';

    protected $primaryKey = 'ChiTietGioHangID';

    protected $fillable = [
        'SoLuong',
        'GioHangID',
        'BienTheID',
    ];
}
