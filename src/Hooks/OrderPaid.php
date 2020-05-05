<?php

namespace Moloni\Hooks;

use Exception;
use Moloni\Log;
use Moloni\Plugin;
use Moloni\Start;
use Moloni\Controllers\Documents;

class OrderPaid
{

    public $parent;

    /**
     *
     * @param Plugin $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $status = defined('DOCUMENT_ORDER_STATUS') ? DOCUMENT_ORDER_STATUS : 'completed';
        add_action('woocommerce_order_status_' . $status, [$this, 'documentCreate']);
    }

    public function documentCreate($orderId)
    {
        try {
            if (Start::login() && defined("INVOICE_AUTO") && INVOICE_AUTO) {
                Log::setFileName('DocumentsAuto');
                Log::write("A gerar automaticamente o documento da encomenda " . $orderId);

                $document = new Documents($orderId);
                $newDocument = $document->createDocument();

                if ($newDocument->getError()) {
                    Log::write("Houve um erro ao gerar o documento: " . strip_tags($newDocument->getError()->getDecodedMessage()));
                }
            }
        } catch (Exception $ex) {
            Log::write("Falta error: " . $ex->getMessage());
        }
    }

}
