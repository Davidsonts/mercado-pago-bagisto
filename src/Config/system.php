<?php

return [
    [
        'key'    => 'sales.payment_methods.mercadopago',
        'name'   => 'Mercado Pago',
        'sort'   => 1,
        'info'   => 'Para pagar online com o Mercado Pago, você pode usar o cartão de crédito ou boleto bancário à vista. Você também pode carregar saldo na sua conta e usá-lo para compras', // Informações adicionais (opcional)
        'fields' => [
            [
                'name' => 'title',
                'title' => 'Título',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'Descrição',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ],[
                'name' => 'token',
                'title' => 'Token de Integração',
                'type' => 'text',
                'validation' => 'required',
                'info' => 'Token gerado na sua conta MercadoPago, para descobrir como pegar seu Token acesse esse link: https://www.mercadopago.com.br/developers/'
            ], [
                'name'          => 'active',
                'title'         => 'status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
                // 'name' => 'active',
                // 'title' => 'status',
                // 'type' => 'select',
                // 'options' => [
                //     [
                //         'title' => 'Ativo',
                //         'value' => true
                //     ], [
                //         'title' => 'Inativo',
                //         'value' => false
                //     ]
                // ],
                // 'validation' => 'required'
            ]
        ]
    ]
];