(function () {

    // =========================
    // HỦY ĐƠN
    // =========================

    const cancelDialog = document.getElementById('cancelDialog');
    const cancelForm = document.getElementById('cancelForm');
    const cancelClose = document.getElementById('cancelClose');

    if (cancelDialog && cancelForm && cancelClose) {

        document.querySelectorAll('[data-cancel-url]').forEach(function (btn) {

            btn.addEventListener('click', function () {

                cancelForm.action = btn.dataset.cancelUrl;

                cancelDialog.showModal();

            });

        });


        cancelClose.addEventListener('click', function () {

            cancelDialog.close();

        });


        // Bấm ra ngoài hộp thoại để đóng
        cancelDialog.addEventListener('click', function (e) {

            if (e.target === cancelDialog) {

                cancelDialog.close();

            }

        });

    }


    // =========================
    // ĐÃ NHẬN HÀNG
    // =========================

    const receiveDialog = document.getElementById('receiveDialog');
    const receiveForm = document.getElementById('receiveForm');
    const receiveClose = document.getElementById('receiveClose');

    if (receiveDialog && receiveForm && receiveClose) {

        document.querySelectorAll('[data-receive-url]').forEach(function (btn) {

            btn.addEventListener('click', function () {

                receiveForm.action = btn.dataset.receiveUrl;

                receiveDialog.showModal();

            });

        });


        receiveClose.addEventListener('click', function () {

            receiveDialog.close();

        });


        // Bấm ra ngoài hộp thoại để đóng
        receiveDialog.addEventListener('click', function (e) {

            if (e.target === receiveDialog) {

                receiveDialog.close();

            }

        });

    }

    // =========================
// POPUP CHI TIẾT ĐƠN HÀNG
// =========================

document.querySelectorAll('[data-detail-id]').forEach(function (btn) {

    btn.addEventListener('click', function () {

        const orderId = btn.dataset.detailId;

        const dialog = document.getElementById(
            'orderDetail' + orderId
        );

        if (dialog) {
            dialog.showModal();
        }

    });

});


document.querySelectorAll('[data-detail-close]').forEach(function (btn) {

    btn.addEventListener('click', function () {

        const orderId = btn.dataset.detailClose;

        const dialog = document.getElementById(
            'orderDetail' + orderId
        );

        if (dialog) {
            dialog.close();
        }

    });

});

})();