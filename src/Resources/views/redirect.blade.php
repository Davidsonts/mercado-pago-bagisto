<!DOCTYPE html>
<html>
<head>
    <title>Redirecionando para o Mercado Pago...</title>
</head>
<body>
    <p>Você será redirecionado para o Mercado Pago em instantes...</p>

    <!-- Formulário para redirecionamento automático -->
    <form action="{{ $redirectUrl }}" method="POST" id="mercadopagoRedirectForm">
        @csrf <!-- Token CSRF para segurança -->
        <input type="submit" value="Clique aqui se não for redirecionado automaticamente.">
    </form>

    <script type="text/javascript">
        // Auto-submit do formulário
        document.getElementById('mercadopagoRedirectForm').submit();
    </script>
</body>
</html>