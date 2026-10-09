<?php

namespace App\Models;

class GioHang extends LegacyModel
{
    protected $table = 'giohang';

    protected $primaryKey = 'GioHangID';

    protected $fillable = [
        'NgayTao',
        'NgayCapNhat',
        'NguoiDungID',
    ];
}
