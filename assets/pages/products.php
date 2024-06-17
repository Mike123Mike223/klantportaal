<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Producten</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</head>
<body>
    <?php
    session_start();
    require_once '../dbinclude/db.php';
    require_once '../templates/header.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        header('Location: /login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT o.order_id, p.product_id, p.naam, p.type, o.status, p.registratie_datum, p.verloop_datum, p.domeinnaam FROM `order` o JOIN product p ON o.product_id = p.product_id WHERE o.user_id = ?');
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query('SELECT product_id, naam, type FROM product');
    $available_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container">
        <h1>Mijn Producten</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Registratie Datum</th>
                    <th>Verloop Datum</th>
                    <th>Domeinnaam</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['naam']) ?></td>
                        <td><?= htmlspecialchars($product['type']) ?></td>
                        <td><?= htmlspecialchars($product['status']) ?></td>
                        <td><?= htmlspecialchars($product['registratie_datum']) ?></td>
                        <td><?= htmlspecialchars($product['verloop_datum']) ?></td>
                        <td><?= htmlspecialchars($product['domeinnaam']) ?></td>
                        <td>
                            <?php if ($product['status'] == 'Actief'): ?>
                                <form action="/pages/cancel_product.php" method="post">
                                    <input type="hidden" name="order_id" value="<?= $product['order_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Opzeggen</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary" data-toggle="modal" data-target="#requestProductModal">Product Aanvragen</button>
    </div>

    <!-- Product Aanvragen Modal -->
    <div class="modal fade" id="requestProductModal" tabindex="-1" aria-labelledby="requestProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestProductModalLabel">Product Aanvragen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="requestForm" action="/pages/request_product.php" method="post">
                        <div class="form-group">
                            <label for="product_id">Kies een product</label>
                            <select class="form-control" id="product_id" name="product_id" required>
                                <?php foreach ($available_products as $product): ?>
                                    <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['naam']) ?> (<?= htmlspecialchars($product['type']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_type">Betaalvoorkeur</label>
                            <select class="form-control" id="payment_type" name="payment_type" required>
                                <option value="Factuur">Factuur</option>
                                <option value="Automatisch Incasso">Automatisch Incasso</option>
                            </select>
                        </div>
                        <div id="mandate_fields" style="display: none;">
                            <div class="form-group">
                                <label for="iban">IBAN-nummer</label>
                                <input type="text" class="form-control" id="iban" name="iban">
                            </div>
                            <div class="form-group">
                                <label for="account_name">Naam (van de rekeninghouder)</label>
                                <input type="text" class="form-control" id="account_name" name="account_name">
                            </div>
                            <div class="form-group">
                                <label for="mandate_date">Datum</label>
                                <input type="date" class="form-control" id="mandate_date" name="mandate_date">
                            </div>
                            <div class="form-group">
                                <label for="signature">Handtekening</label>
                                <canvas id="signature-pad" class="signature-pad" width=400 height=200 style="border: 1px solid #000;"></canvas>
                                <button type="button" id="clear" class="btn btn-secondary">Clear</button>
                                <textarea id="signature" name="signature" style="display: none;"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Aanvragen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas);

        document.getElementById('payment_type').addEventListener('change', function() {
            if (this.value === 'Automatisch Incasso') {
                document.getElementById('mandate_fields').style.display = 'block';
            } else {
                document.getElementById('mandate_fields').style.display = 'none';
            }
        });

        document.getElementById('clear').addEventListener('click', function() {
            signaturePad.clear();
        });

        document.getElementById('requestForm').addEventListener('submit', function() {
            if (!signaturePad.isEmpty()) {
                var dataUrl = signaturePad.toDataURL();
                document.getElementById('signature').value = dataUrl;
            }
        });
    </script>

    <?php require_once '../templates/footer.php'; ?>
</body>
</html>
