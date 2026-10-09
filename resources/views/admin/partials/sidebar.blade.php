<aside class="admin-sidebar">


            {{-- LOGO --}}

            <div class="admin-sidebar-logo">
                <img
                    src="{{ asset('images/ICONS/logo.png') }}"
                    alt="Dollie"
                    class="admin-logo-image"
                >
            </div>


            {{-- =================================================
                 MENU
            ================================================== --}}

            <nav class="admin-sidebar-menu">


                {{-- TỔNG QUAN --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        TỔNG QUAN
                    </div>

                    {{-- Thống kê -> trang tổng quan (trang chính sau đăng nhập) --}}
                    <a
                        href="{{ route('admin.overview') }}"
                        class="admin-menu-item {{ request()->routeIs('admin.overview') ? 'active' : '' }}"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>

                        <span>
                            Thống kê
                        </span>

                    </a>

                </div>


                {{-- BÁN HÀNG --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        BÁN HÀNG
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 8h14l1 13H4L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
                        </span>

                        <span>
                            Đơn hàng
                        </span>

                    </a>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h3"/><path d="m14 16 2 2 3-3"/></svg>
                        </span>

                        <span>
                            Yêu cầu hoàn tiền
                        </span>

                    </a>

                </div>


                {{-- SẢN PHẨM --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        SẢN PHẨM
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3Z"/><path d="m3 7.5 9 5 9-5M12 12.5V21"/></svg>
                        </span>

                        <span>
                            Sản phẩm
                        </span>

                    </a>


                   <a
                        href="{{ route('admin.category.index') }}"
                        class="admin-menu-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><path d="M4 14h16M4 18h16"/></svg>
                        </span>

                        <span>
                            Danh mục
                        </span>

                    </a>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3.5 2 1.4 2.4-.3.8 2.3 2.2 1.2-.7 2.4 1.1 2.1-1.8 1.8.2 2.4-2.4.5-1.4 2-2.4-1-2.3 1-1.5-2-2.4-.5.2-2.4-1.8-1.8 1.1-2.1-.7-2.4 2.2-1.2.8-2.3 2.4.3L12 3.5Z"/><circle cx="12" cy="12" r="2.4"/></svg>
                        </span>

                        <span>
                            Thương hiệu
                        </span>

                    </a>

                </div>


                {{-- KHO HÀNG --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        KHO HÀNG
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 9 4.5v9L12 21l-9-4.5v-9L12 3Z"/><path d="m3 7.5 9 5 9-5M12 12.5V21"/></svg>
                        </span>

                        <span>
                            Nhập hàng
                        </span>

                    </a>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="8" width="18" height="12" rx="1"/><path d="M8 8V5h8v3M3 12h18"/></svg>
                        </span>

                        <span>
                            Kho hàng/Tồn kho
                        </span>

                    </a>


                </div>


                {{-- KHÁCH HÀNG --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        KHÁCH HÀNG
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1H3Z"/><path d="M16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.6V20h-4"/></svg>
                        </span>

                        <span>
                            Người dùng
                        </span>

                    </a>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
                        </span>

                        <span>
                            Đánh giá
                        </span>

                    </a>

                </div>


                {{-- KHUYẾN MÃI --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        KHUYẾN MÃI
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m20 13-7 7-10-10V3h7l10 10Z"/><circle cx="7.5" cy="7.5" r="1"/><path d="m10 14 2-2 2 2-2 2-2-2Z"/></svg>
                        </span>

                        <span>
                            Mã giảm giá
                        </span>

                    </a>

                </div>


                {{-- HỆ THỐNG --}}

                <div class="admin-menu-section">

                    <div class="admin-menu-title">
                        HỆ THỐNG
                    </div>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="7" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                        </span>

                        <span>
                            Tài khoản nhân viên
                        </span>

                    </a>


                    <a
                        href="#"
                        class="admin-menu-item"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </span>

                        <span>
                            Lịch sử hệ thống
                        </span>

                    </a>

                </div>

            </nav>


            {{-- =================================================
                 THÔNG TIN TÀI KHOẢN
            ================================================== --}}

            <div class="admin-sidebar-account">

                <a
                    href="{{ url('/admin/profile') }}"
                    class="admin-account-icon"
                    aria-label="Trang cá nhân"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="9" r="3"/><path d="M6.5 19a6 6 0 0 1 11 0"/></svg>
                </a>


                <div class="admin-account-info">

                    <strong>

                        {{ session('AdminHo') }}
                        {{ session('AdminTen') }}

                    </strong>

                    <span>

                        {{ session('AdminEmail') }}

                    </span>

                </div>

            </div>


            {{-- =================================================
                 ĐĂNG XUẤT
            ================================================== --}}

            <div class="admin-sidebar-logout">

                <form
                    action="{{ route('admin.logout') }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="admin-logout-button"
                    >

                        <span class="admin-menu-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m14 17-5-5 5-5M9 12h12"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg>
                        </span>

                        <span>
                            Đăng xuất
                        </span>

                    </button>

                </form>

            </div>


        </aside>
