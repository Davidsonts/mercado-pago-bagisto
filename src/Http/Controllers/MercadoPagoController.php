<?php

namespace Davidsonts\MercadoPago\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Models\Order; 
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Sales\Repositories\OrderRepository;
use Davidsonts\MercadoPago\Payment\MercadoPago;
use Davidsonts\MercadoPago\Helpers\OrderHelper;
use Illuminate\Support\Facades\Log;
use Exception;

class MercadoPagoController extends Controller
{
    protected $orderRepository;
    protected $mercadopago;
    protected $helper;

    public function __construct(OrderRepository $orderRepository, MercadoPago $mercadopago, OrderHelper $helper)
    {
        $this->orderRepository = $orderRepository;
        $this->mercadopago = $mercadopago;
        $this->helper = $helper; // Injeta o helper
    }

    public function success()
    {
        $cart = Cart::getCart();
       
        $data = (new OrderResource($cart))->jsonSerialize();
     
        $order = $this->orderRepository->create($data);

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    public function create(array $data)
    {
        // Validação dos dados recebidos
        if (empty($data)) {
            throw new \InvalidArgumentException("Os dados do pedido estão vazios.");
        }
    
        // Lógica para criar o pedido
        return Order::create([
            'customer_id' => $data['customer_id'],
            'grand_total' => $data['grand_total'],
            'items' => json_encode($data['items']), // Salva os itens como JSON
            'status' => 'pending',
        ]);
    }

    public function failure(Request $request)
    {
        return redirect('/checkout/failure')->with('error', 'Pagamento falhou.');
    }

    public function pending(Request $request)
    {
        return redirect('/checkout/pending')->with('info', 'Pagamento pendente.');
    }
 
    public function redirect()
    {     
        try {
    
            $preference = $this->mercadopago->createPreference($this->mercadopago->getToken()); // [[1]]
            
            // Verifica se a URL é válida
            if (!isset($preference->init_point) || !filter_var($preference->init_point, FILTER_VALIDATE_URL)) {
                throw new Exception("URL inválida do Mercado Pago.");
            }

            // Retorna a URL para a view
            return view('mercadopago::redirect', [
                'redirectUrl' => $preference->init_point,
            ]);
        } catch (Exception $e) {
            Log::error("Erro no redirecionamento: " . $e->getMessage());
            return redirect('/checkout/failure')->with('error', 'Erro ao processar pagamento.');
        }
    }

    public function processPayment(Request $request)
    {
        try {
            $token = $request->input('token');
            $paymentData = [
                'token' => $token,
                'amount' => Cart::getCart()->grand_total,
                'payment_method_id' => 'visa', // Obtido via API
            ];

            $payment = $this->mercadopago->createPayment($paymentData);

            if ($payment->status === 'approved') {
                // Finalizar pedido
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false]);
        } catch (Exception $e) {
            Log::error("Erro ao processar pagamento: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao processar pagamento.']);
        }
    }
}