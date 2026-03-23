
(function () {
    window.__adminCustomJsLoaded = true;
    console.log('admin custom.js loaded');
    function ensureToastContainer() {
        var container = document.getElementById('toast-container');
        if (container) return container;

        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
        return container;
    }

    function showToast(message) {
        if (!message) return;
        if (!(window.bootstrap && window.bootstrap.Toast)) return;

        var container = ensureToastContainer();

        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-bg-dark border-0';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        toastEl.innerHTML =
            '<div class="d-flex">' +
                '<div class="toast-body">' +
                    message +
                '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>';

        container.appendChild(toastEl);
        var toast = new window.bootstrap.Toast(toastEl, { delay: 2200 });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    }

    window.adminShowToast = showToast;

    function warnProceedTop(title, text) {
        if (typeof window.swal === 'function') {
            window.swal(title, text, 'warning');
        } else if (window.adminShowToast) {
            window.adminShowToast(text);
        } else {
            window.alert(text);
        }
    }

    function parseProceedJsonTop(raw) {
        if (raw == null || raw === '') return null;
        if (typeof raw === 'object') return raw;
        var s = String(raw).trim().replace(/^\uFEFF/, '');
        if (!s) return null;
        return JSON.parse(s);
    }

    function handleProceedResponseTop(res, ctx) {
        ctx = ctx || {};
        var phone = ctx.phone != null ? String(ctx.phone).trim() : '';
        if (!res || typeof res.status === 'undefined') {
            warnProceedTop('Error', 'Invalid response from server.');
            return;
        }
        var code = Number(res.status);
        if (code === 200) {
            window.location.href = 'order-summary.php';
            return;
        }
        if (code === 404) {
            if (typeof window.swal === 'function') {
                window.swal('Customer not found', res.message || 'Customer not found', 'warning');
            } else {
                warnProceedTop('Customer not found', res.message || 'Customer not found');
            }
            return;
        }
        if (typeof window.swal === 'function') {
            window.swal(res.message || 'Error', res.message || '', res.status_type || 'error');
        } else {
            warnProceedTop(res.message || 'Error', res.message || 'Something went wrong.');
        }
    }

    if (typeof window.jQuery === 'function') {
        window.jQuery(document).on('click', '.proceedToPlace', function () {
            console.log('proceedToPlace click (jQuery handler)');
            var paymentMode = window.jQuery('#paymentMethod').val();
            if (!paymentMode || String(paymentMode).trim() === '') {
                warnProceedTop('Select payment method', 'Please choose a payment method before continuing.');
                return false;
            }

            var phoneRaw = window.jQuery('#customerPhone').val();
            var phone = phoneRaw != null ? String(phoneRaw).trim() : '';
            if (phone === '') {
                warnProceedTop('Enter phone number', 'Please enter the customer phone number before continuing.');
                return false;
            }

            window.jQuery.ajax({
                type: 'POST',
                url: 'orders-code.php',
                data: {
                    proceedToPlace: 1,
                    customerPhone: phone,
                    paymentMode: paymentMode
                },
                dataType: 'text',
                success: function (text) {
                    var res;
                    try {
                        res = parseProceedJsonTop(text);
                    } catch (e) {
                        warnProceedTop('Error', 'Could not read server response.');
                        return;
                    }
                    handleProceedResponseTop(res, { phone: phone });
                },
                error: function (xhr) {
                    var raw = xhr && xhr.responseText ? xhr.responseText : '';
                    try {
                        var res = parseProceedJsonTop(raw);
                        if (res) {
                            handleProceedResponseTop(res, { phone: phone });
                            return;
                        }
                    } catch (e2) {
                    }
                    warnProceedTop('Request failed', 'Could not reach the server. Please try again.');
                }
            });
        });
    }

    // Native fallback (in case jQuery event handlers are not firing)
    document.addEventListener('click', function (e) {
        var target = e.target;
        var btn = target && target.closest ? target.closest('.proceedToPlace') : null;
        if (!btn) return;

        console.log('proceedToPlace click (native handler)');

        if (typeof window.jQuery !== 'function') {
            warnProceedTop('Error', 'jQuery is not available.');
            return;
        }

        e.preventDefault();

        var paymentMode = window.jQuery('#paymentMethod').val();
        if (!paymentMode || String(paymentMode).trim() === '') {
            warnProceedTop('Select payment method', 'Please choose a payment method before continuing.');
            return;
        }

        var phoneRaw = window.jQuery('#customerPhone').val();
        var phone = phoneRaw != null ? String(phoneRaw).trim() : '';
        if (phone === '') {
            warnProceedTop('Enter phone number', 'Please enter the customer phone number before continuing.');
            return;
        }

        window.jQuery.ajax({
            type: 'POST',
            url: 'orders-code.php',
            data: {
                proceedToPlace: 1,
                customerPhone: phone,
                paymentMode: paymentMode
            },
            dataType: 'text',
            success: function (text) {
                var res;
                try {
                    res = parseProceedJsonTop(text);
                } catch (e2) {
                    warnProceedTop('Error', 'Could not read server response.');
                    return;
                }
                handleProceedResponseTop(res, { phone: phone });
            },
            error: function () {
                warnProceedTop('Request failed', 'Could not reach the server. Please try again.');
            }
        });
    }, true);

    function migrateFirstAlertToToast(options) {
        options = options || {};
        var shouldToast = options.toast !== false;

        var host = document.querySelector('.container-fluid.px-4');
        if (!host) host = document.querySelector('main');
        if (!host) host = document.body;

        var alertEl = host.querySelector('.alert.alert-dismissible');
        if (!alertEl) return;

        var text = (alertEl.textContent || '').replace(/\s+/g, ' ').trim();
        if (!text) return;

        alertEl.remove();
        if (shouldToast) {
            showToast(text);
        }
    }

    function getCurrentQtyFromForm(form) {
        if (!form) return null;
        var qtyInput = form.querySelector('.cart-qty-input, .quantityInput');
        if (!qtyInput) return null;
        var currentQty = parseInt((qtyInput.value || '').toString(), 10);
        if (Number.isNaN(currentQty)) return null;
        return currentQty;
    }

    function getMaxQtyFromForm(form) {
        if (!form) return null;
        var raw = form.getAttribute('data-max-qty');
        if (raw === null || raw === undefined || raw === '') return null;
        var maxQty = parseInt(raw.toString(), 10);
        if (Number.isNaN(maxQty)) return null;
        return maxQty;
    }

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.js-increaseQty, .js-decreaseQty') : null;
        if (!btn || !btn.classList) return;

        var isInc = btn.classList.contains('js-increaseQty');
        var isDec = btn.classList.contains('js-decreaseQty');
        if (!isInc && !isDec) return;

        var form = btn.closest('form');
        var qty = getCurrentQtyFromForm(form);
        if (qty === null) return;

        var maxQty = getMaxQtyFromForm(form);

        if (isInc && maxQty !== null && qty >= maxQty) {
            return;
        }

        if (form && form.classList.contains('js-cartQtyAjax')) {
            return;
        }

        var msg;
        if (isInc) {
            msg = 'Quantity increased to ' + (qty + 1) + '.';
        } else {
            if (qty <= 1) {
                msg = 'Item removed from cart.';
            } else {
                msg = 'Quantity decreased to ' + (qty - 1) + '.';
            }
        }

        try {
            localStorage.setItem('cartQtyToast', msg);
        } catch (err) {
        }
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        var msg = null;
        try {
            msg = localStorage.getItem('cartQtyToast');
            localStorage.removeItem('cartQtyToast');
        } catch (err) {
        }

        migrateFirstAlertToToast({ toast: !msg });

        if (msg) {
            showToast(msg);
        }

        var categorySelect = document.getElementById('categorySelect');
        var productSelect = document.getElementById('productSelect');
        var previewImg = document.getElementById('productPreviewImg');
        if (productSelect && previewImg) {
            var allProductOptions = Array.prototype.slice.call(productSelect.options)
                .filter(function (opt) { return opt.value !== ''; })
                .map(function (opt) {
                    return {
                        value: opt.value,
                        text: opt.text,
                        image: opt.getAttribute('data-image') || '',
                        categoryId: opt.getAttribute('data-category-id') || ''
                    };
                });

            var rebuildProducts = function () {
                if (!categorySelect) return;
                var selectedCategory = categorySelect.value || '';
                var currentProduct = productSelect.value || '';

                productSelect.innerHTML = '';
                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Select Product';
                productSelect.appendChild(placeholder);

                allProductOptions.forEach(function (optData) {
                    if (selectedCategory && String(optData.categoryId) !== String(selectedCategory)) {
                        return;
                    }
                    var opt = document.createElement('option');
                    opt.value = optData.value;
                    opt.textContent = optData.text;
                    opt.setAttribute('data-image', optData.image);
                    opt.setAttribute('data-category-id', optData.categoryId);
                    if (String(optData.value) === String(currentProduct)) {
                        opt.selected = true;
                    }
                    productSelect.appendChild(opt);
                });

            };

            var updatePreview = function () {
                var opt = productSelect.options[productSelect.selectedIndex];
                if (!opt) return;

                var img = opt.getAttribute('data-image') || '';
                img = img.toString().trim();

                if (!img) {
                    previewImg.classList.add('d-none');
                    previewImg.removeAttribute('src');
                    previewImg.removeAttribute('alt');
                    return;
                }

                var src = img;
                if (!/^https?:\/\//i.test(src) && src.charAt(0) !== '/') {
                    src = '../' + src;
                }

                previewImg.src = src;
                previewImg.alt = opt.textContent || 'Selected product';
                previewImg.classList.remove('d-none');
            };

            productSelect.addEventListener('change', updatePreview);

            if (categorySelect) {
                categorySelect.addEventListener('change', function () {
                    rebuildProducts();
                    updatePreview();
                });
            }

            rebuildProducts();
            updatePreview();
        }

        function warnProceed(title, text) {
            if (typeof window.swal === 'function') {
                window.swal(title, text, 'warning');
            } else if (window.adminShowToast) {
                window.adminShowToast(text);
            } else {
                window.alert(text);
            }
        }

        function parseProceedJson(raw) {
            if (raw == null || raw === '') return null;
            if (typeof raw === 'object') return raw;
            var s = String(raw).trim().replace(/^\uFEFF/, '');
            if (!s) return null;
            return JSON.parse(s);
        }

        function showCustomerNotFoundModal(phone, message) {
            var modalEl = document.getElementById('customerNotFoundModal');
            var phoneEl = modalEl ? modalEl.querySelector('.js-customer-not-found-phone') : null;
            var addLink = modalEl ? modalEl.querySelector('.js-add-customer-from-order') : null;
            var titleEl = modalEl ? modalEl.querySelector('#customerNotFoundModalLabel') : null;

            if (modalEl && phoneEl && addLink && window.bootstrap && window.bootstrap.Modal) {
                var cartModal = document.getElementById('cartModal');
                if (cartModal) {
                    var cartInst = window.bootstrap.Modal.getInstance(cartModal);
                    if (cartInst) {
                        cartInst.hide();
                    }
                }
                phoneEl.textContent = phone || '';
                addLink.href =
                    'customer-create.php?phone=' + encodeURIComponent(phone != null ? String(phone) : '');
                if (titleEl && message) {
                    titleEl.textContent = message;
                }
                var instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.show();
                return;
            }

            if (
                window.confirm(
                    (message || 'Customer not found.') +
                        ' Open Add Customer with this number filled in?'
                )
            ) {
                window.location.href =
                    'customer-create.php?phone=' + encodeURIComponent(phone != null ? String(phone) : '');
            }
        }

        function handleProceedResponse(res, ctx) {
            ctx = ctx || {};
            var phone = ctx.phone != null ? String(ctx.phone).trim() : '';

            if (!res || typeof res.status === 'undefined') {
                warnProceed('Error', 'Invalid response from server.');
                return;
            }
            var code = Number(res.status);
            if (code === 200) {
                window.location.href = 'order-summary.php';
                return;
            }
            if (code === 404) {
                showCustomerNotFoundModal(phone, res.message || 'Customer not found');
                return;
            }
            if (typeof window.swal === 'function') {
                try {
                    window.swal(
                        res.message || 'Error',
                        res.message || '',
                        res.status_type || 'error'
                    );
                } catch (err2) {
                    warnProceed(res.message || 'Error', res.message || 'Something went wrong.');
                }
            } else {
                warnProceed(res.message || 'Error', res.message || 'Something went wrong.');
            }
        }

        $(document).on('click', '.proceedToPlace', function () {
            var paymentMode = $('#paymentMethod').val();
            if (!paymentMode || String(paymentMode).trim() === '') {
                warnProceed('Select payment method', 'Please choose a payment method before continuing.');
                return false;
            }

            var phoneRaw = $('#customerPhone').val();
            var phone = phoneRaw != null ? String(phoneRaw).trim() : '';
            if (phone === '') {
                warnProceed('Enter phone number', 'Please enter the customer phone number before continuing.');
                return false;
            }

            var data = {
                proceedToPlace: 1,
                customerPhone: phone,
                paymentMode: paymentMode
            };

            $.ajax({
                type: 'POST',
                url: 'orders-code.php',
                data: data,
                dataType: 'text',
                success: function (text) {
                    var res;
                    try {
                        res = parseProceedJson(text);
                    } catch (e) {
                        warnProceed('Error', 'Could not read server response.');
                        return;
                    }
                    handleProceedResponse(res, { phone: phone });
                },
                error: function (xhr) {
                    var raw = xhr.responseText || '';
                    try {
                        var res = parseProceedJson(raw);
                        if (res) {
                            handleProceedResponse(res, { phone: phone });
                            return;
                        }
                    } catch (e2) {
                    }
                    warnProceed('Request failed', 'Could not reach the server. Please try again.');
                }
            });
        });

    });

    $(document).on('click', '#saveOrder', function () {
        console.log('saveOrder button clicked');
        var customerPhone = typeof window !== 'undefined' && window.sessionStorage ? window.sessionStorage.getItem('customerPhone') || '' : '';
        var paymentMode = typeof window !== 'undefined' && window.sessionStorage ? window.sessionStorage.getItem('paymentMode') || '' : '';
        var invoice_no = typeof window !== 'undefined' && window.sessionStorage ? window.sessionStorage.getItem('invoice_no') || '' : '';
        console.log('saveOrder data:', { customerPhone, paymentMode, invoice_no });
        $.ajax({
            type: "POST",
            url: "orders-code.php",
            data: {
                'saveOrder' : true,
                'customerPhone': customerPhone,
                'paymentMode': paymentMode,
                'invoice_no': invoice_no
            },
            success: function(response) {
                console.log('saveOrder response:', response);
                var res = JSON.parse(response);
                if(res.status == 200){
                    swal(res.message,res.message,res.status_type)
                        .then((value) => {
                            window.location.href = 'orders.php';
                        });
                }else{
                    swal(res.message,res.message,res.status_type);
                }
            },
            error: function(xhr, status, error) {
                console.log('saveOrder error:', { xhr, status, error });
                swal('Error', 'Could not save order. Please try again.', 'error');
            }
        });
    });

    // Save order handling (moved from order-summary.php)
    (function () {
        // Store session data in sessionStorage if not already set
        if (typeof sessionStorage !== 'undefined') {
            if (!sessionStorage.getItem('customerPhone') && typeof window !== 'undefined' && window.orderSessionData) {
                sessionStorage.setItem('customerPhone', window.orderSessionData.customerPhone || '');
                sessionStorage.setItem('paymentMode', window.orderSessionData.paymentMode || '');
                sessionStorage.setItem('invoice_no', window.orderSessionData.invoice_no || '');
                console.log('sessionStorage set for saveOrder:', {
                    customerPhone: sessionStorage.getItem('customerPhone'),
                    paymentMode: sessionStorage.getItem('paymentMode'),
                    invoice_no: sessionStorage.getItem('invoice_no')
                });
            }
        } else {
            console.error('sessionStorage not available');
        }

        // Native fallback for #saveOrder click
        var saveBtn = document.getElementById('saveOrder');
        if (saveBtn) {
            saveBtn.addEventListener('click', function (e) {
                console.log('saveOrder button clicked (native)');
                var customerPhone = sessionStorage.getItem('customerPhone') || '';
                var paymentMode = sessionStorage.getItem('paymentMode') || '';
                var invoice_no = sessionStorage.getItem('invoice_no') || '';
                console.log('saveOrder data (native):', { customerPhone, paymentMode, invoice_no });

                var formData = new FormData();
                formData.append('saveOrder', 'true');
                formData.append('customerPhone', customerPhone);
                formData.append('paymentMode', paymentMode);
                formData.append('invoice_no', invoice_no);

                fetch('orders-code.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (res) {
                        console.log('saveOrder fetch status:', res.status);
                        return res.text();
                    })
                    .then(function (text) {
                        console.log('saveOrder response (native):', text);
                        // If response starts with < or is not JSON, show a readable error
                        if (text.trim().charAt(0) === '<' || text.indexOf('Fatal error') !== -1) {
                            console.error('Server returned non-JSON (likely PHP error):', text);
                            if (typeof swal === 'function') {
                                swal('Server Error', 'A server error occurred. Please check the error logs or contact support.', 'error');
                            } else {
                                alert('A server error occurred. Please check the error logs or contact support.');
                            }
                            return;
                        }
                        var res;
                        try {
                            res = JSON.parse(text);
                        } catch (e) {
                            console.error('saveOrder JSON parse error:', e);
                            if (typeof swal === 'function') {
                                swal('Error', 'Invalid server response.', 'error');
                            } else {
                                alert('Invalid server response.');
                            }
                            return;
                        }
                        if (res.status == 200) {
                            if (typeof swal === 'function') {
                                swal(res.message, res.message, res.status_type)
                                    .then(function () {
                                        window.location.href = 'orders.php';
                                    });
                            } else {
                                alert(res.message);
                                window.location.href = 'orders.php';
                            }
                        } else {
                            if (typeof swal === 'function') {
                                swal(res.message, res.message, res.status_type);
                            } else {
                                alert(res.message);
                            }
                        }
                    })
                    .catch(function (err) {
                        console.error('saveOrder fetch error:', err);
                        if (typeof swal === 'function') {
                            swal('Error', 'Could not save order. Please try again.', 'error');
                        } else {
                            alert('Could not save order. Please try again.');
                        }
                    });
            });
        }
    })();

});
