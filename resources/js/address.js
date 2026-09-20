// =========================
// POPUP ĐỔI ĐỊA CHỈ MẶC ĐỊNH
// =========================

(function () {

    const dialog = document.getElementById('addressConfirmDialog');
    const confirmForm = document.getElementById('addressConfirmForm');
    const noButton = document.getElementById('addressConfirmNo');
    const yesButton = document.getElementById('addressConfirmYes');


    // Nếu trang không có popup thì không làm gì
    if (!dialog || !confirmForm || !noButton || !yesButton) {
        return;
    }


    // =========================
    // NÚT "ĐẶT LÀM MẶC ĐỊNH"
    // =========================

    document.querySelectorAll('[data-default-button]').forEach(function (button) {

        button.addEventListener('click', function () {

            // Lấy form chứa nút
            const form = button.closest('[data-default-form]');

            if (!form) {
                return;
            }

            // Gán URL cho form popup
            confirmForm.action = form.action;

            // Mở popup
            dialog.showModal();

        });

    });


    // =========================
    // NÚT "KHÔNG"
    // =========================

    noButton.addEventListener('click', function () {

        dialog.close();

    });


    // =========================
    // NÚT "CÓ"
    // =========================

    yesButton.addEventListener('click', function () {

        confirmForm.submit();

    });


    // =========================
    // BẤM RA NGOÀI POPUP
    // =========================

    dialog.addEventListener('click', function (event) {

        if (event.target === dialog) {

            dialog.close();

        }

    });

})();