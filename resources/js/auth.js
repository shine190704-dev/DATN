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
            const today = new Date()
                .toISOString()
                .split('T')[0];

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


    // =========================
    // ĐẶT LẠI MẬT KHẨU
    // =========================

    const resetForm = document.querySelector(
        '.forgot-password-page form'
    );

    if (resetForm) {

        const passwordInput = document.getElementById('password');

        const passwordConfirmationInput =
            document.getElementById('password_confirmation');


        resetForm.addEventListener('submit', function (event) {

            // Kiểm tra mật khẩu mới
            if (
                passwordInput &&
                passwordInput.value.trim() === ''
            ) {

                event.preventDefault();

                alert('Vui lòng nhập mật khẩu mới.');

                passwordInput.focus();

                return;
            }


            // Kiểm tra độ dài mật khẩu
            if (
                passwordInput &&
                passwordInput.value.length < 8
            ) {

                event.preventDefault();

                alert('Mật khẩu mới phải có ít nhất 8 ký tự.');

                passwordInput.focus();

                return;
            }


            // Kiểm tra nhập lại mật khẩu
            if (
                passwordConfirmationInput &&
                passwordConfirmationInput.value.trim() === ''
            ) {

                event.preventDefault();

                alert('Vui lòng nhập lại mật khẩu.');

                passwordConfirmationInput.focus();

                return;
            }


            // Kiểm tra 2 mật khẩu có giống nhau không
            if (
                passwordInput &&
                passwordConfirmationInput &&
                passwordInput.value !==
                passwordConfirmationInput.value
            ) {

                event.preventDefault();

                alert('Mật khẩu nhập lại không khớp.');

                passwordConfirmationInput.focus();

                return;
            }

        });
    }

});