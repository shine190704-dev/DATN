document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // ĐĂNG KÝ
    // =========================

    const registerForm = document.querySelector('.register-page form');

    if (registerForm) {

        // SỐ ĐIỆN THOẠI
        const phoneInput = document.getElementById('phone');

        if (phoneInput) {
            phoneInput.addEventListener('input', function () {

                // Chỉ cho phép nhập số và tối đa 10 số
                this.value = this.value
                    .replace(/\D/g, '')
                    .slice(0, 10);

            });
        }

        // NGÀY SINH
        const birthdayInput = document.getElementById('birthday');

        if (birthdayInput) {

            // Không cho chọn ngày trong tương lai
            const today = new Date().toISOString().split('T')[0];

            birthdayInput.max = today;
        }
    }


    // =========================
    // ĐĂNG NHẬP
    // =========================

    const loginForm = document.querySelector('.login-page form');

    if (loginForm) {

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        loginForm.addEventListener('submit', function (event) {

            if (emailInput.value.trim() === '') {

                event.preventDefault();

                alert('Vui lòng nhập Email.');

                emailInput.focus();

                return;
            }

            if (passwordInput.value.trim() === '') {

                event.preventDefault();

                alert('Vui lòng nhập mật khẩu.');

                passwordInput.focus();

                return;
            }

        });
    }

});