<?php

return [

    'precio_especial' => [
        'crear' => [
            'destinatarios' => ['permiso:Precio_especial_aprobar'],
        ],
        'aprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'confirmar' => [
            'condiciones' => [
                'rol:Vendedor Comercial' => ['rol:Auxiliar de Venta'],
            ]
        ],
        'ejecutar' => [
            'condiciones' => [
                'rol:Vendedor Comercial' => ['creador'],
                'default' => [], // Si fue un vendedor normal, no se notifica a nadie
            ]
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
    ],

    'Anulacion de Venta' => [
        'crear' => [
            'destinatarios' => ['permiso:Anulacion_aprobar', 'encargado_almacen'],
        ],
        'crear_devolucion' => [
            'destinatarios' => ['permiso:Devolucion_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Anulacion_entrega', 'encargado_almacen'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'verificar_entrega' => [
            'condiciones' => [
                'default' => [
                    'permiso:Anulacion_entrega',
                    'no_permiso:Anulacion_ejecutar',
                ]
            ]
        ],
        'verificar_entrega_fisica' => [
            'condiciones' => [
                'default' => [
                    'permiso:Anulacion_entrega',
                    'permiso:Anulacion_ejecutar',
                ]
            ]
        ],
        'ejecutar_anulacion' => [
            'destinatarios' => ['creador'],
        ],
        'ejecutar_devolucion' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Muestra de Mercaderia' => [
        'crear' => [
            'condiciones' => [
                'rol:Vendedor Comercial' => ['permiso:Muestra_aprobar'],
                'default' => ['encargado_almacen'],
            ],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Muestra_ejecutar', 'encargado_almacen'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Sobregiro de Venta' => [
        'crear' => [
            'destinatarios' => ['permiso:Sobregiro_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Sobregiro_confirmar'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'confirmar' => [
            'destinatarios' => ['permiso:Sobregiro_ejecutar'],
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Devolucion de Venta' => [
        'crear' => [
            'destinatarios' => ['permiso:Devolucion_aprobar', 'encargado_almacen'],
        ],
        'crear_anulacion' => [
            'destinatarios' => ['permiso:Anulacion_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Devolucion_entrega', 'encargado_almacen'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'verificar_entrega' => [
            'condiciones' => [
                'default' => [
                    'permiso:Devolucion_entrega',
                    'no_permiso:Devolucion_ejecutar',
                ]
            ]
        ],
        'verificar_entrega_fisica' => [
            'condiciones' => [
                'default' => [
                    'permiso:Devolucion_entrega',
                    'permiso:Devolucion_ejecutar',
                ]
            ]
        ],
        'ejecutar_anulacion' => [
            'destinatarios' => ['creador'],
        ],
        'ejecutar_devolucion' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Baja de Mercaderia' => [
        'crear' => [
            'destinatarios' => ['permiso:Baja_confirmar', 'encargado_almacen'],
        ],
        'confirmar' => [
            'destinatarios' => ['permiso:Baja_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Baja_ejecutar'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Cambio fisico en Mercaderia' => [
        'crear' => [
            'destinatarios' => ['permiso:Cambio_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Cambio_ejecutar', 'encargado_almacen'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Vacacion' => [
        'crear' => [
            'destinatarios' => ['permiso:Vacaciones_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Vacaciones_ejecutar'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],

    'Extras' => [
        'crear' => [
            'destinatarios' => ['permiso:Extra_aprobar'],
        ],
        'aprobar' => [
            'destinatarios' => ['permiso:Extra_confirmar'],
        ],
        'confirmar' => [
            'destinatarios' => ['permiso:Extra_ejecutar'],
        ],
        'reprobar' => [
            'condiciones' => [
                'default' => ['creador'],
            ]
        ],
        'ejecutar' => [
            'destinatarios' => ['creador'],
        ],
    ],


    // Agregaremos más tipos aquí después
];
