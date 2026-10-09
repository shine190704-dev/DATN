<?php

namespace App\Models;

class ThanhToanNhapHang extends LegacyModel
{
    protected $table = 'thanhtoannhaphang';

    protected $primaryKey = 'ThanhToanNhapHangID';

    protected $fillable = [
        'SoTienThanhToan',
        'PhuongThucThanhToan',
        'NgayThanhToan',
        'GhiChu',
        'DonDatHangID',
    ];
}
