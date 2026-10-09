<?php

namespace App\Models;

class DanhGia extends LegacyModel
{
    protected $table = 'danhgia';

    protected $primaryKey = 'DanhGiaID';

    protected $fillable = [
        'DiemDanhGia',
        'BinhLuan',
        'TrangThai',
        'NgayTao',
        'NgayCapNhat',
        'NguoiDungID',
        'SanPhamID',
        'DonHangID',
    ];
}
