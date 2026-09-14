document.addEventListener('DOMContentLoaded', function () {
	const cartPage = document.querySelector('.cart-page');

	if (!cartPage) {
		return;
	}

	const updateUrl = cartPage.dataset.updateUrl;
	const removeUrl = cartPage.dataset.removeUrl;
	const csrfToken = cartPage.dataset.csrfToken;
	const toast = document.querySelector('.cart-toast');
	let toastTimer;

	function showToast(message) {
		if (!toast) {
			return;
		}

		toast.textContent = message;
		toast.classList.add('show');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () {
			toast.classList.remove('show');
		}, 2500);
	}

	function formatPrice(value) {
		return Number(value).toLocaleString('vi-VN') + ' VND';
	}

	function updateCartBadge(count) {
		const cartIcon = document.querySelector('.header-cart-icon');

		if (!cartIcon) {
			return;
		}

		let badge = cartIcon.querySelector('.cart-count-badge');

		if (!badge) {
			badge = document.createElement('span');
			badge.className = 'cart-count-badge';
			cartIcon.appendChild(badge);
		}

		badge.textContent = count;
		badge.hidden = Number(count) < 1;
	}

	function updateCartTitle(count) {
		const title = cartPage.querySelector('[data-cart-title]');

		if (title) {
			title.textContent = 'Giỏ hàng của bạn (' + count + ')';
		}
	}

	async function sendCartRequest(url, payload) {
		const response = await fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken,
				'Accept': 'application/json'
			},
			body: JSON.stringify(payload)
		});

		const data = await response.json();

		if (!response.ok || !data.success) {
			throw new Error(data.message || 'Không thể cập nhật giỏ hàng.');
		}

		return data;
	}

	cartPage.addEventListener('click', async function (event) {
		const quantityButton = event.target.closest('.cart-quantity-btn');
		const removeButton = event.target.closest('.cart-remove-btn');

		if (!quantityButton && !removeButton) {
			return;
		}

		const button = quantityButton || removeButton;
		const item = button.closest('.cart-item');

		if (!item || button.disabled) {
			return;
		}

		const itemId = button.dataset.itemId;
		button.disabled = true;

		try {
			if (removeButton) {
				const data = await sendCartRequest(removeUrl, {
					ChiTietGioHangID: itemId
				});

				item.remove();
				updateCartBadge(data.cartCount);
				updateCartTitle(data.cartCount);
				showToast(data.message);

				if (Number(data.cartCount) === 0) {
					window.location.reload();
				}

				return;
			}

			const quantityElement = item.querySelector('.cart-quantity');
			const currentQuantity = Number(quantityElement.textContent.trim());
			const nextQuantity = quantityButton.dataset.action === 'increase'
				? currentQuantity + 1
				: currentQuantity - 1;

			if (nextQuantity < 1) {
				return;
			}

			const data = await sendCartRequest(updateUrl, {
				ChiTietGioHangID: itemId,
				SoLuong: nextQuantity
			});

			quantityElement.textContent = nextQuantity;

			const unitPrice = Number(item.dataset.unitPrice);
			item.querySelector('[data-item-total]').textContent =
				formatPrice(unitPrice * nextQuantity);

			const total = Array.from(cartPage.querySelectorAll('.cart-item'))
				.reduce(function (sum, cartItem) {
					const quantity = Number(
						cartItem.querySelector('.cart-quantity').textContent.trim()
					);
					return sum + Number(cartItem.dataset.unitPrice) * quantity;
				}, 0);

			cartPage.querySelector('#cartTotal').textContent = formatPrice(total);
			updateCartBadge(data.cartCount);
			updateCartTitle(data.cartCount);
		} catch (error) {
			showToast(error.message);
		} finally {
			button.disabled = false;
		}
	});
});
