<?php

namespace App\Render;

use Dompdf\Dompdf;
use Dompdf\Options;
use Laravel\Cashier\Contracts\InvoiceRenderer;
use Laravel\Cashier\Invoice;

class CustomInvoiceRenderer implements InvoiceRenderer
{
    /**
     * {@inheritDoc}
     */
    public function render(Invoice $invoice, array $data = [], array $options = []): string
    {
        if (! defined('DOMPDF_ENABLE_AUTOLOAD')) {
            define('DOMPDF_ENABLE_AUTOLOAD', false);
        }

        $dompdfOptions = new Options;
        $dompdfOptions->setChroot(base_path());
        $dompdfOptions->setFontDir(storage_path('/fonts'));

        $dompdf = new Dompdf($dompdfOptions);
        $dompdf->setPaper($options['paper'] ?? 'letter');
        $dompdf->loadHtml($invoice->view($data)->render());
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
