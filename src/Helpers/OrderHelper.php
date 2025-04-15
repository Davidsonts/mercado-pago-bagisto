<?php

namespace Davidsonts\MercadoPago\Helpers;

use Webkul\Sales\Models\Order;

class OrderHelper
{
    public function updateOrderWithMercadoPagoData(Order $order, $paymentResponse)
    {
        // Verifica se a resposta contém dados válidos
        if (!isset($paymentResponse->status)) {
            throw new \Exception("Dados de pagamento inválidos."); // [[4]]
        }

        // Mapeia o status do Mercado Pago para o sistema interno
        $status = $this->mapPaymentStatus($paymentResponse->status);

        // Atualiza o pedido
        $order->update([
            'status' => $status,
            'transaction_id' => $paymentResponse->id ?? null, // ID da transação no Mercado Pago
            'payment_method' => $paymentResponse->payment_method_id ?? null,
            'payment_data' => json_encode($paymentResponse), // Armazena toda a resposta [[4]]
        ]);
    }

    protected function mapPaymentStatus($mpStatus)
    {
        switch ($mpStatus) {
            case 'approved':
                return 'completed'; // Pagamento aprovado [[4]]
            case 'pending':
                return 'pending'; // Pagamento pendente [[4]]
            case 'rejected':
                return 'failed'; // Pagamento rejeitado [[4]]
            default:
                return 'unknown'; // Status desconhecido
        }
    }
}