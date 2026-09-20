// =========================
// HIỆN / ẨN MẬT KHẨU
// =========================

document.addEventListener('DOMContentLoaded', function () {

    const showPasswords = document.getElementById('showPasswords');

    if (!showPasswords) {
        return;
    }

    showPasswords.addEventListener('change', function () {

        const passwordFields = [
            'current_password',
            'new_password',
            'new_password_confirmation'
        ];

        passwordFields.forEach(function (id) {

            const input = document.getElementById(id);

            if (input) {
                input.type = showPasswords.checked
                    ? 'text'
                    : 'password';
            }

        });

    });

});