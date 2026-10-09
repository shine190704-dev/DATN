<?php

namespace App\Models;

class ChiTietDonDatHang extends LegacyModel
{
    protected $table = 'chitietdondathang';

    protected $primaryKey = 'ChiTietDonDatHangID';

    protected $fillable = [
        'MauSac',
        'KichThuoc',
        'SoLuongDat',
        'SoLuongDaNhan',
        'GiaNhap',
        'ThanhTien',
        'GhiChu',
        'DonDatHangID',
        'SanPhamID',
    ];
}
