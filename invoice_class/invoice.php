<?php

class Invoice {
    private $order_id;
    private $order_items;
    private $total_cost;
    private $shipping_fee;
    private $customer_details;

    // Constructor to initialize the order details, shipping fee, and customer details
    public function __construct($order_id, $order_items, $customer_details, $shipping_fee) {
        $this->order_id = $order_id;
        $this->order_items = $order_items;
        $this->customer_details = $customer_details;
        $this->shipping_fee = $shipping_fee; // Store the shipping fee
        $this->calculateTotal(); // Calculate total cost including shipping fee
    }

    // Calculate the total cost, including the shipping fee
    private function calculateTotal() {
        $this->total_cost = 0;
        foreach ($this->order_items as $item) {
            $this->total_cost += $item['price'] * $item['product_quantity']; 
        }
        // Add the shipping fee to the total cost
        $this->total_cost += $this->shipping_fee;
    }

    // Generate the PDF invoice
    public function generatePDF() {
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Invoice - Order #{$this->order_id}");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Header Information
        $html = '<h2 style="text-align:center;">Order Invoice</h2>';
        $html .= "<p><strong>Name:</strong> {$this->customer_details['full_name']}</p>";
        $html .= "<p><strong>Email:</strong> {$this->customer_details['email']}</p>";
        $html .= "<p><strong>Phone:</strong> {$this->customer_details['contact_number']}</p>";
        $html .= "<p><strong>Address:</strong> {$this->customer_details['address']}</p>";
        $html .= '<hr>';

        // Table Header
        $html .= '<table border="1" cellpadding="5" cellspacing="0">
                    <tr style="background-color:#D4AF37; color:white;">
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>';

        // Loop through the order items and add each row to the table
        foreach ($this->order_items as $index => $item) {
            $item_total = $item['price'] * $item['product_quantity'];
            $html .= "<tr>
                        <td>" . ($index + 1) . "</td>
                        <td>{$item['product_name']}</td>
                        <td>Rs. {$item['price']}</td>
                        <td>{$item['product_quantity']}</td>
                        <td>Rs. {$item_total}</td>
                      </tr>";
        }

        // Shipping Fee
        $html .= "<tr>
                    <td colspan='4' style='text-align:right;'><strong>Shipping Fee:</strong></td>
                    <td>Rs. {$this->shipping_fee}</td>
                  </tr>";

        // Total Cost (Including Shipping Fee)
        $html .= "<tr>
                    <td colspan='4' style='text-align:right;'><strong>Total Cost:</strong></td>
                    <td><strong>Rs. {$this->total_cost}</strong></td>
                  </tr>";
        $html .= '</table>';

        // Output the PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output("invoice_order_{$this->order_id}.pdf", "D"); 
    }
}
?>
