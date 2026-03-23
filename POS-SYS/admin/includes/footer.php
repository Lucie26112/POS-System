
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; A4 Group 4's Website</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="assets/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="assets/js/custom.js?v=20260321_1"></script>
        <script>
            console.log('custom.js executed:', window.__adminCustomJsLoaded);
        </script>
        <script>
            // Inline fallback for proceedToPlace click handling
            document.addEventListener('click', function (e) {
                var target = e.target;
                var btn = target && target.closest ? target.closest('.proceedToPlace') : null;
                if (!btn) return;

                console.log('proceedToPlace click (inline handler)');
                e.preventDefault();

                var paymentMode = (typeof jQuery !== 'undefined') ? jQuery('#paymentMethod').val() : null;
                if (!paymentMode || String(paymentMode).trim() === '') {
                    if (typeof swal === 'function') {
                        swal('Select payment method', 'Please choose a payment method before continuing.', 'warning');
                    } else {
                        alert('Please choose a payment method before continuing.');
                    }
                    return;
                }

                var phoneRaw = (typeof jQuery !== 'undefined') ? jQuery('#customerPhone').val() : null;
                var phone = phoneRaw != null ? String(phoneRaw).trim() : '';
                if (phone === '') {
                    if (typeof swal === 'function') {
                        swal('Enter phone number', 'Please enter the customer phone number before continuing.', 'warning');
                    } else {
                        alert('Please enter the customer phone number before continuing.');
                    }
                    return;
                }

                if (typeof jQuery === 'undefined') {
                    alert('jQuery is not available.');
                    return;
                }

                jQuery.ajax({
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
                            res = JSON.parse(text);
                        } catch (e) {
                            if (typeof swal === 'function') {
                                swal('Error', 'Could not read server response.', 'error');
                            } else {
                                alert('Could not read server response.');
                            }
                            return;
                        }

                        if (!res || typeof res.status === 'undefined') {
                            if (typeof swal === 'function') {
                                swal('Error', 'Invalid response from server.', 'error');
                            } else {
                                alert('Invalid response from server.');
                            }
                            return;
                        }

                        var code = Number(res.status);
                        if (code === 200) {
                            window.location.href = 'order-summary.php';
                            return;
                        }

                        if (code === 404) {
                            var modalEl = document.getElementById('customerNotFoundModal');
                            var phoneEl = modalEl ? modalEl.querySelector('.js-customer-not-found-phone') : null;
                            var addLink = modalEl ? modalEl.querySelector('.js-add-customer-from-order') : null;
                            var titleEl = modalEl ? modalEl.querySelector('#customerNotFoundModalLabel') : null;
                            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                                var cartModal = document.getElementById('cartModal');
                                if (cartModal) {
                                    var cartInst = window.bootstrap.Modal.getInstance(cartModal);
                                    if (cartInst) {
                                        cartInst.hide();
                                    }
                                }
                                if (phoneEl) {
                                    phoneEl.textContent = phone || '';
                                }
                                if (addLink) {
                                    addLink.href = 'customer-create.php?phone=' + encodeURIComponent(phone != null ? String(phone) : '');
                                }
                                if (titleEl) {
                                    titleEl.textContent = res.message || 'Customer not found';
                                }
                                var instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                                instance.show();
                            } else if (typeof swal === 'function') {
                                swal('Customer not found', res.message || 'Customer not found', 'warning');
                            } else {
                                alert(res.message || 'Customer not found');
                            }
                            return;
                        }

                        if (typeof swal === 'function') {
                            swal(res.message || 'Error', res.message || '', res.status_type || 'error');
                        } else {
                            alert(res.message || 'Something went wrong.');
                        }
                    },
                    error: function () {
                        if (typeof swal === 'function') {
                            swal('Request failed', 'Could not reach the server. Please try again.', 'error');
                        } else {
                            alert('Could not reach the server. Please try again.');
                        }
                    }
                });
            }, true);
        </script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="assets/js/datatables-simple-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.mySelect2').select2();
            });
        </script>

        <script>
            (function () {
                if (!(window.bootstrap && window.bootstrap.Toast)) return;
                var host = document.querySelector('.container-fluid.px-4') || document.querySelector('main') || document.body;
                if (!host) return;
                var alertEl = host.querySelector('.alert.alert-dismissible');
                if (!alertEl) return;
                var text = (alertEl.textContent || '').replace(/\s+/g, ' ').trim();
                if (!text) return;

                alertEl.remove();

                var container = document.getElementById('toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container';
                    container.className = 'toast-container position-fixed top-0 end-0 p-3';
                    container.style.zIndex = '1080';
                    document.body.appendChild(container);
                }

                var toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center text-bg-dark border-0';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML =
                    '<div class="d-flex">' +
                        '<div class="toast-body">' +
                            text +
                        '</div>' +
                        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>';
                container.appendChild(toastEl);
                var toast = new window.bootstrap.Toast(toastEl, { delay: 2200 });
                toast.show();
                toastEl.addEventListener('hidden.bs.toast', function () {
                    toastEl.remove();
                });
            })();
        </script>

    </body>
</html>
