document.addEventListener('DOMContentLoaded', function () {

    const favoriteButtons = document.querySelectorAll('.product-favorite');

    favoriteButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            button.classList.toggle('active');

        });

    });

});