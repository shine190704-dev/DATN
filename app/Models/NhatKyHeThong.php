<?php

namespace App\Models;

class NhatKyHeThong extends LegacyModel
{
    protected $table = 'nhatkyhethong';

    protected $primaryKey = 'NhatKyHeThongID';

    protected $fillable = [
        'PhanHe',
        'HanhDong',
        'DoiTuong',
        'MoTa',
        'NgayTao',
        'NguoiThaoTacID',
    ];
}
