<?php

namespace Davidsonts\MercadoPago\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Log;


class MercadoPago extends Payment
{     
     
    protected $code = 'mercadopago';

    protected $logo = '/vendor/mercadopago/images/mercadopago.png'; 

    const TOKEN = 'sales.payment_methods.mercadopago.token';
 
    private $token;

    protected $redirectUrl;
 
    public function __construct()
    {
       $this->token = core()->getConfigData(self::TOKEN);
    }
    
    public function getToken()
    {
        return $this->token; // Retorna o token de integração
    }

    public function getImage()
    {
        return $this->logo; // Retorna o caminho da imagem do logo
    }

    public function getTitle()
    {
        return core()->getConfigData('sales.payment_methods.mercadopago.title'); // Retorna o título configurado
    }

    public function getRedirectUrl()
    {
        return route('mercadopago.redirect'); // Retorna a URL interna de redirecionamento
    }

    public function createPreference($accessToken)
    {
        $cart = $this->getCart(); // Obtém o carrinho via método da classe pai
       
        $data = [
            "items" => [
                [
                    "title" => "Pedido #" . $cart->id,
                    "quantity" => 1,
                    "unit_price" =>  (float)$cart->grand_total,
                    "currency_id" => "BRL",
                ]
            ],
            "back_urls" => [
                "success" => route('mercadopago.success', [], true),
                "failure" => route('mercadopago.failure', [], true),
                "pending" => route('mercadopago.pending', [], true),
            ],
            "auto_return" => "approved",
        ];
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/checkout/preferences");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $preference = json_decode($response);

        // Log para debug
        Log::debug("Resposta do Mercado Pago:", (array) $preference);
      
        return $preference;
    }

    public function createPayment($paymentData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->getToken(),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $payment = json_decode($response);

        // Log para debug
        Log::debug("Resposta do pagamento:", (array) $payment);

        return $payment;
    }
    
    public function getPaymentStatus($paymentId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/{$paymentId}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " .$this->getToken(),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

}