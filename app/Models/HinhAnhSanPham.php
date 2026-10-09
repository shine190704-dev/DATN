<?php

namespace App\Models;

class HinhAnhSanPham extends LegacyModel
{
    protected $table = 'hinhanhsanpham';

    protected $primaryKey = 'HinhAnhSanPhamID';

    protected $fillable = [
        'DuongDanAnh',
        'AnhDaiDien',
        'NgayTao',
        'SanPhamID',
    ];
}
