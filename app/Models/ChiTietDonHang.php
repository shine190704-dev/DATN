<?php

namespace App\Models;

class ChiTietDonHang extends LegacyModel
{
    protected $table = 'chitietdonhang';

    protected $primaryKey = 'ChiTietDonHangID';

    protected $fillable = [
        'TenSanPham',
        'MauSac',
        'HinhAnh',
        'KichThuoc',
        'SoLuong',
        'GiaTaiThoiDiemMua',
        'DonHangID',
        'SanPhamID',
    ];
}
