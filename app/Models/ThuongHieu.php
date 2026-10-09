<?php

namespace App\Models;

class ThuongHieu extends LegacyModel
{
    protected $table = 'thuonghieu';

    protected $primaryKey = 'ThuongHieuID';

    protected $fillable = [
        'TenThuongHieu',
        'MaThuongHieu',
        'MoTa',
        'TrangThai',
        'NgayTao',
        'NgayCapNhat',
    ];
}
