// FE/customer/assets/js/main.js
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    const plateInput = document.getElementById('license_plate_input');

    if (plateInput) {
        // Tự động viết hoa biển số khi gõ
        plateInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }

    if (searchForm && plateInput) {
        searchForm.addEventListener('submit', function (event) {
            // Loại bỏ khoảng trắng thừa đầu cuối
            plateInput.value = plateInput.value.trim();

            if (!searchForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // Hiển thị Spinner loading trên nút Tra cứu
                const btn = searchForm.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tra cứu...`;
                }
            }
            searchForm.classList.add('was-validated');
        }, false);
    }
});
