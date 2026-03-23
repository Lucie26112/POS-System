<?php
require_once '../config/function.php';
require_once 'authentication.php';
?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Create an Order</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Orders</li>
            <li class="breadcrumb-item active">Create Order</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Create Order
                    <a href="orders.php" class="btn btn-primary float-end ms-2">Back</a>
                    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#cartModal">
                        View Cart
                    </button>
                </h4>
            </div>
            <div class="card-body">
                <form action="orders-code.php" method = "POST">
                <input type="hidden" name="redirect_to" value="order-create.php">

                <div class="row g-3 align-items-start">
                    <div class="col-12 col-md-6 col-lg-5">
                        <label for="categorySelect">Select Category</label>
                        <select class="form-select mb-2" id="categorySelect">
                            <option value="">All Categories</option>
                            <?php
                                $categories = getAll('categories');
                                if($categories && mysqli_num_rows($categories) > 0) {
                                    while($category = mysqli_fetch_assoc($categories)) {
                                        echo '<option value="'.(int)$category['id'].'">'.htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                }
                            ?>
                        </select>

                        <label for="productSelect">Select Product</label>
                        <select name="product_id" class="form-select" id="productSelect">
                            <option value="">Select Product</option>
                            <?php
                                $products = getAll('products');
                                if($products && mysqli_num_rows($products) > 0) {
                                    while($product = mysqli_fetch_assoc($products)) {
                                        $img = isset($product['image']) ? $product['image'] : '';
                                        $categoryId = isset($product['category_id']) ? (int)$product['category_id'] : 0;
                                        echo '<option value="'.(int)$product['id'].'" data-image="'.htmlspecialchars($img, ENT_QUOTES, 'UTF-8').'" data-category-id="'.$categoryId.'">'.htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8').'</option>';
                                    }
                                }
                            ?>
                        </select>
                        <div class="product-preview mt-3 text-center">
                            <img id="productPreviewImg" class="product-preview-img d-none" alt="" style="width: 220px; height: 220px; max-width: 100%; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <label for="">Quantity</label>
                        <input type="number" required name="quantity" class="form-control">
                    </div>
                    <div class="col-auto ms-md-auto align-self-start">
                        <button type="submit" name="addItem" class="btn btn-primary px-4">Add Item</button>
                    </div>
                </div>
                    
                </form>
                
            </div>
        </div>

    </div>

    <!-- Cart popup -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $sessionProducts = (isset($_SESSION['cart_items']) && is_array($_SESSION['cart_items'])) ? $_SESSION['cart_items'] : [];
                    $cartHasItems = count($sessionProducts) > 0;
                    $cartGrandTotal = 0.0;
                    foreach ($sessionProducts as $ci) {
                        $p = isset($ci['price']) ? (float)$ci['price'] : 0.0;
                        $q = isset($ci['quantity']) ? (int)$ci['quantity'] : 0;
                        $cartGrandTotal += $p * $q;
                    }
                    ?>
                    <div id="cartModalFilled" class="<?= $cartHasItems ? '' : 'd-none'; ?>">
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessionProducts as $key => $item) :
                                    $availableQty = '';
                                    if (isset($item['product_id'])) {
                                        $pid = (int)$item['product_id'];
                                        $stockRes = mysqli_query($conn, "SELECT quantity FROM products WHERE id='$pid' LIMIT 1");
                                        if ($stockRes && mysqli_num_rows($stockRes) > 0) {
                                            $stockRow = mysqli_fetch_assoc($stockRes);
                                            $availableQty = (string)(int)$stockRow['quantity'];
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars((string)($item['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-center">
                                        <form action="orders-code.php" method="POST" class="js-cartQtyAjax" data-max-qty="<?= htmlspecialchars($availableQty, ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="input-group input-group-sm cart-qty-group">
                                                <input type="hidden" name="ajax_cart_qty" value="1">
                                                <input type="hidden" name="cart_key" value="<?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" name="decreaseQty" class="btn btn-light cart-qty-btn js-decreaseQty">-</button>
                                                <input type="text" class="form-control cart-qty-input" value="<?= (int)($item['quantity'] ?? 0); ?>" readonly>
                                                <button type="submit" name="increaseQty" class="btn btn-light cart-qty-btn js-increaseQty">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td><?= number_format((float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0), 2); ?></td>
                                    <td class="text-center"><a href="orders-code.php?removeItem=<?= urlencode((string)$key); ?>" class="btn btn-danger">Remove</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th id="cartGrandTotal"><?= number_format($cartGrandTotal, 2); ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr class="mt-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label for="paymentMethod" class="form-label mb-1">Payment method</label>
                            <select id="paymentMethod" class="form-select form-select-sm">
                                <option value="">Select method</option>
                                <option value="Cash">Cash</option>
                                <option value="Online">Online Payment</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label for="customerPhone" class="form-label mb-1">Enter Customer Phone Number</label>
                            <input type="tel" id="customerPhone" class="form-control form-control-sm" value="" inputmode="numeric" autocomplete="tel">
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <button type="button" class="btn btn-warning w-100 proceedToPlace">Proceed to Place Order</button>
                        </div>
                    </div>
                    </div>
                    <div id="cartModalEmpty" class="alert alert-warning mb-0 <?= $cartHasItems ? 'd-none' : ''; ?>">
                        <h4 class="mb-0">No items in cart</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerNotFoundModal" tabindex="-1" aria-labelledby="customerNotFoundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerNotFoundModalLabel">Customer not found</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">No customer matches this phone number:</p>
                    <p class="mb-2"><strong class="js-customer-not-found-phone text-break"></strong></p>
                    <p class="mb-0 text-muted small">You can add a new customer now—the phone field will be filled in automatically.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-primary js-add-customer-from-order">Add customer</a>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var params = new URLSearchParams(window.location.search);
    if (params.get('open_cart') === '1') {
        var el = document.getElementById('cartModal');
        if (el && window.bootstrap && window.bootstrap.Modal) {
            var modal = window.bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        }
        params.delete('open_cart');
        var qs = params.toString();
        var newUrl = window.location.pathname + (qs ? '?' + qs : '') + window.location.hash;
        window.history.replaceState({}, '', newUrl);
    }
});

function applyCartModalPayload(data) {
    if (!data) return;
    var filled = document.getElementById('cartModalFilled');
    var emptyEl = document.getElementById('cartModalEmpty');
    var grandEl = document.getElementById('cartGrandTotal');

    if (data.is_empty) {
        if (filled) filled.classList.add('d-none');
        if (emptyEl) emptyEl.classList.remove('d-none');
        return;
    }

    if (filled) filled.classList.remove('d-none');
    if (emptyEl) emptyEl.classList.add('d-none');

    var tbody = document.querySelector('#cartModal table tbody');
    if (!tbody) return;

    tbody.textContent = '';
    (data.items || []).forEach(function (it) {
        var tr = document.createElement('tr');

        var tdName = document.createElement('td');
        tdName.textContent = it.name != null ? String(it.name) : '';

        var tdPrice = document.createElement('td');
        tdPrice.textContent = it.price != null ? String(it.price) : '';

        var tdQty = document.createElement('td');
        tdQty.className = 'text-center';

        var form = document.createElement('form');
        form.action = 'orders-code.php';
        form.method = 'POST';
        form.className = 'js-cartQtyAjax';
        form.setAttribute('data-max-qty', it.max_qty != null && it.max_qty !== '' ? String(it.max_qty) : '');

        var group = document.createElement('div');
        group.className = 'input-group input-group-sm cart-qty-group';

        var hiddenAjax = document.createElement('input');
        hiddenAjax.type = 'hidden';
        hiddenAjax.name = 'ajax_cart_qty';
        hiddenAjax.value = '1';

        var hiddenKey = document.createElement('input');
        hiddenKey.type = 'hidden';
        hiddenKey.name = 'cart_key';
        hiddenKey.value = it.key != null ? String(it.key) : '';

        var btnDec = document.createElement('button');
        btnDec.type = 'submit';
        btnDec.name = 'decreaseQty';
        btnDec.className = 'btn btn-light cart-qty-btn js-decreaseQty';
        btnDec.textContent = '-';

        var inputQty = document.createElement('input');
        inputQty.type = 'text';
        inputQty.className = 'form-control cart-qty-input';
        inputQty.readOnly = true;
        inputQty.value = it.quantity != null ? String(it.quantity) : '';

        var btnInc = document.createElement('button');
        btnInc.type = 'submit';
        btnInc.name = 'increaseQty';
        btnInc.className = 'btn btn-light cart-qty-btn js-increaseQty';
        btnInc.textContent = '+';

        group.appendChild(hiddenAjax);
        group.appendChild(hiddenKey);
        group.appendChild(btnDec);
        group.appendChild(inputQty);
        group.appendChild(btnInc);
        form.appendChild(group);
        tdQty.appendChild(form);

        var tdLine = document.createElement('td');
        tdLine.textContent = it.line_total != null ? String(it.line_total) : '';

        var tdRem = document.createElement('td');
        tdRem.className = 'text-center';
        var a = document.createElement('a');
        a.href = 'orders-code.php?removeItem=' + encodeURIComponent(it.key != null ? String(it.key) : '');
        a.className = 'btn btn-danger';
        a.textContent = 'Remove';
        tdRem.appendChild(a);

        tr.appendChild(tdName);
        tr.appendChild(tdPrice);
        tr.appendChild(tdQty);
        tr.appendChild(tdLine);
        tr.appendChild(tdRem);
        tbody.appendChild(tr);
    });

    if (grandEl) grandEl.textContent = data.grand_total != null ? String(data.grand_total) : '0.00';
}

function showCartToast(message) {
    if (!message) return;
    if (window.adminShowToast) {
        window.adminShowToast(message);
        return;
    }
    if (!(window.bootstrap && window.bootstrap.Toast)) {
        return;
    }

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
                String(message) +
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

document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || !form.classList || !form.classList.contains('js-cartQtyAjax')) return;

    var submitter = e.submitter;
    var isDec = submitter && submitter.getAttribute('name') === 'decreaseQty';
    var qtyInput = form.querySelector('.cart-qty-input');
    var currentQty = qtyInput ? parseInt(qtyInput.value, 10) : NaN;

    if (isDec && !Number.isNaN(currentQty) && currentQty <= 1) {
        if (!window.confirm('Setting the quantity to 0 will remove this product from the cart. Continue?')) {
            e.preventDefault();
            return;
        }
    }

    e.preventDefault();

    var fd = new FormData(form);
    if (submitter && submitter.getAttribute('name')) {
        fd.append(submitter.getAttribute('name'), '');
    }

    fetch('orders-code.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(function (res) {
            return res.json();
        })
        .then(function (data) {
            if (!data || typeof data.success === 'undefined') {
                throw new Error('bad');
            }
            applyCartModalPayload(data);
            var msg = data.message;
            var isInc = submitter && submitter.getAttribute && submitter.getAttribute('name') === 'increaseQty';
            if (!data.success && isInc) {
                var maxRaw = form.getAttribute('data-max-qty');
                var maxQty = maxRaw != null && String(maxRaw).trim() !== '' ? parseInt(String(maxRaw), 10) : NaN;
                if (!Number.isNaN(maxQty)) {
                    msg = 'Cannot increase above available quantity (' + maxQty + ').';
                }
            }
            if (msg) {
                showCartToast(msg);
            }
        })

        .catch(function () {
            showCartToast('Could not update cart.');
        });
}, true);
</script>
<?php include 'includes/footer.php';?>