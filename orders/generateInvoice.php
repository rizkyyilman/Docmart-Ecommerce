<?php
session_start();
require('../library/fpdf.php');

// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "docmartbeta");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Pastikan order_id ada dalam permintaan
if (!isset($_GET['order_id'])) {
    echo "Error: Order ID not provided.";
    exit();
}

$order_id = $_GET['order_id'];

// Ambil informasi order dari database
$order_sql = "SELECT total_price, payment_method, order_date FROM orders WHERE id = ?";
$order_stmt = $connection->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_stmt->bind_result($total_price, $payment_method, $order_date);
$order_stmt->fetch();
$order_stmt->close();

// Cek apakah order ditemukan
if (!$total_price) {
    echo "Error: Order not found.";
    exit();
}

// Ambil detail produk dari order_items
$items_sql = "SELECT products.name, order_items.price AS item_price, order_items.quantity AS item_quantity 
              FROM order_items 
              JOIN products ON order_items.product_id = products.id 
              WHERE order_items.order_id = ?";
$items_stmt = $connection->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

// Inisialisasi PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
// Tambahkan informasi pelanggan dan detail pesanan
$customer_name = isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Customer';
$pdf->Cell(0, 10, 'Customer Name: ' . $customer_name, 0, 1);
$pdf->Cell(0, 10, 'Invoice Number: ' . htmlspecialchars($order_id), 0, 1);
$pdf->Cell(0, 10, 'Date: ' . date('d-m-Y', strtotime($order_date)), 0, 1);
$pdf->Ln(10);

// Tambahkan rincian produk
$pdf->Cell(0, 10, 'Rincian Produk:', 0, 1);
$total_invoice_price = 0;

while ($item = $items_result->fetch_assoc()) {
    $item_name = htmlspecialchars($item['name']);
    $item_price = $item['item_price'];
    $item_quantity = $item['item_quantity'];
    $item_total_price = $item_price * $item_quantity;

    $pdf->Cell(0, 10,
        $item_name . ' - Rp ' . number_format($item_price, 2, ',', '.') . 
        ' x ' . htmlspecialchars($item_quantity) . 
        ' = Rp ' . number_format($item_total_price, 2, ',', '.'), 
        0, 1
    );

    $total_invoice_price += $item_total_price;
}

// Menampilkan total harga invoice 
$pdf->Ln(10); 
$pdf->Cell(0, 10, 'Total Harga Invoice: Rp ' . number_format($total_invoice_price, 2, ',', '.'), 0, 1);

// Output PDF 
$pdf_output_filename = 'Invoice' . str_pad($order_id, 5, '0', STR_PAD_LEFT) . '.pdf'; 
$pdf->Output('D', $pdf_output_filename);

// Menutup statement dan koneksi 
$items_stmt->close(); 
$connection->close(); 
?>