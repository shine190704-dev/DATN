{{-- SIDEBAR TÀI KHOẢN --}}

<aside class="account-sidebar">

    <div class="account-welcome">

        <img
            src="{{ asset('images/ICONS/profile_icon.png') }}"
            alt="Tài khoản"
        >

        <div class="account-welcome-text">

            <span>Xin chào bạn!</span>

            <strong>
                {{ trim(($user->Ho ?? '') . ' ' . ($user->Ten ?? '')) }}
            </strong>

        </div>

    </div>


    <nav class="account-menu">

        <a
            href="{{ route('order.index') }}"
            class="account-menu-item {{ request()->routeIs('order.*') ? 'active' : '' }}"
        >
            Đơn hàng của tôi
        </a>


        <a
            href="{{ route('tracking.index') }}"
            class="account-menu-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}"
        >
            Theo dõi đơn hàng
        </a>


        <a
            href="{{ route('address.index') }}"
            class="account-menu-item {{ request()->routeIs('address.*') ? 'active' : '' }}"
        >
            Sổ địa chỉ
        </a>


        <a
            href="{{ route('profile') }}"
            class="account-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}"
        >
            Thông tin của tôi
        </a>


        <a
            href="{{ route('refund.index') }}"
            class="account-menu-item {{ request()->routeIs('refund.*') ? 'active' : '' }}"
        >
            Yêu cầu hoàn tiền
        </a>


        <a
            href="{{ route('reviews.index') }}"
            class="account-menu-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}"
        >
            Đánh giá của tôi
        </a>


        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button
                type="submit"
                class="account-menu-item"
            >
                Đăng xuất
            </button>

        </form>

    </nav>

</aside>