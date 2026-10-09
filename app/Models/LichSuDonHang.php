<?php

namespace App\Models;

class LichSuDonHang extends LegacyModel
{
    protected $table = 'lichsudonhang';

    protected $primaryKey = 'LichSuDonHangID';

    protected $fillable = [
        'TrangThaiCu',
        'TrangThaiMoi',
        'GhiChu',
        'NgayCapNhat',
        'DonHangID',
        'NguoiThayDoiID',
    ];
}
