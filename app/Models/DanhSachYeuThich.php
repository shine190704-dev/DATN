<?php

namespace App\Models;

class DanhSachYeuThich extends LegacyModel
{
    protected $table = 'danhsachyeuthich';

    protected $primaryKey = 'DanhSachYeuThichID';

    protected $fillable = [
        'NgayTao',
        'NgayCapNhat',
        'NguoiDungID',
        'SanPhamID',
    ];
}
