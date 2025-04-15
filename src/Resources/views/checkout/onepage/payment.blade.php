<!-- Botão para abrir o modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mercadopagoModal">
  Pagar com Mercado Pago
</button>

<img src="{{ asset('vendor/mercadopago/images/mercadopago-logo.png') }}" alt="Mercado Pago" />
<!-- Modal -->
<div class="modal fade" id="mercadopagoModal" tabindex="-1" aria-labelledby="mercadopagoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mercadopagoModalLabel">Pagamento via Mercado Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulário ou redirecionamento -->
        <form id="mercadopagoForm">
          <!-- Campos de pagamento (opcional para checkout transparente) -->
          <div class="mb-3">
            <label for="cardNumber">Número do Cartão</label>
            <input type="text" class="form-control" id="cardNumber" required>
          </div>
          <div class="mb-3">
            <label for="expiryDate">Data de Expiração</label>
            <input type="text" class="form-control" id="expiryDate" placeholder="MM/AA" required>
          </div>
          <div class="mb-3">
            <label for="cvv">CVV</label>
            <input type="text" class="form-control" id="cvv" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="submitPayment()">Confirmar Pagamento</button>
      </div>
    </div>
  </div>
</div>

<!-- Adicione no final do arquivo payment.blade.php -->
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
  const mp = new MercadoPago('{{ config('services.mercadopago.public_key') }}');

  function submitPayment() {
    mp.createToken({
      cardNumber: document.getElementById('cardNumber').value,
      expiryDate: document.getElementById('expiryDate').value,
      cvv: document.getElementById('cvv').value,
    }).then(token => {
      // Enviar o token para o servidor
      fetch('/mercadopago/process', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token: token.id })
      }).then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = '/checkout/success';
          } else {
            alert('Erro no pagamento');
          }
        });
    });
  }
</script>