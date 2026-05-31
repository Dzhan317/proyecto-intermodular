<?php
declare(strict_types=1);

/*
 * Define todas las rutas de la aplicación.
 */

// Públicas
$router->get('/',               'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/products',       'ProductController@index');
$router->get('/products/:slug', 'ProductController@show');
$router->get('/category/:slug', 'CategoryController@show');

// Páginas estáticas
$router->get('/faq',               'HomeController@faq');
$router->get('/shipping',            'HomeController@envios');
$router->get('/legal/privacy',  'HomeController@privacidad');
$router->get('/legal/cookies',  'HomeController@cookies');
$router->get('/legal/terms',    'HomeController@terminos');

// Autenticación
$router->get( '/login',                 'AuthController@loginForm');
$router->post('/auth/check-email',      'AuthController@checkEmail');
$router->get( '/login/password',        'AuthController@passwordForm');
$router->post('/login',                 'AuthController@login');
$router->get( '/register',              'AuthController@registerForm');
$router->post('/register',              'AuthController@register');
$router->get( '/logout',                'AuthController@logout');
$router->get( '/verify-2fa',            'AuthController@verify2faForm');
$router->post('/verify-2fa',            'AuthController@verify2fa');
$router->post('/verify-2fa/resend',     'AuthController@resend2fa');
$router->get( '/forgot-password',       'AuthController@forgotPasswordForm');
$router->post('/forgot-password',       'AuthController@forgotPassword');
$router->get( '/reset-password/:token', 'AuthController@resetPasswordForm');
$router->post('/reset-password/:token', 'AuthController@resetPassword');

// Perfil de usuario
$router->get( '/profile',            'ProfileController@index');
$router->post('/profile',            'ProfileController@update');
$router->get( '/profile/addresses',  'ProfileController@addresses');
$router->get( '/profile/security',   'ProfileController@security');
$router->post('/profile/password',   'ProfileController@changePassword');

// Pedidos — Fase 8
$router->get('/orders',     'OrderController@index');
$router->get('/orders/:id', 'OrderController@show');

// Carrito
$router->get( '/cart',        'CartController@index');
$router->post('/cart/add',    'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');

// Checkout
$router->get( '/checkout/shipping', 'CheckoutController@shipping');
$router->post('/checkout/shipping', 'CheckoutController@saveShipping');
$router->get( '/checkout/payment',  'CheckoutController@payment');
$router->post('/checkout/payment',  'CheckoutController@initiatePayment');
$router->get( '/checkout/success',  'CheckoutController@success');
$router->get( '/checkout/cancel',   'CheckoutController@cancel');

// Soporte — Fase 9
$router->get( '/support',                       'SupportController@index');
$router->post('/support',                       'SupportController@create');
$router->get( '/support/unread',                'SupportController@unreadCount');
$router->get( '/support/:id',                   'SupportController@show');
$router->get( '/support/:id/messages',          'SupportController@getMessages');
$router->post('/support/:id/message',           'SupportController@sendMessage');

// Admin — Panel de administración (Fase 7)
$router->get( '/admin',                       'AdminController@dashboard');

// Admin — Productos
$router->get( '/admin/products',              'AdminController@products');
$router->get( '/admin/products/create',       'AdminController@createProduct');
$router->post('/admin/products/create',       'AdminController@storeProduct');
$router->get( '/admin/products/:id/edit',     'AdminController@editProduct');
$router->post('/admin/products/:id/edit',     'AdminController@updateProduct');
$router->post('/admin/products/:id/delete',   'AdminController@deleteProduct');

// Admin — Pedidos
$router->get( '/admin/orders',                'AdminController@orders');
$router->get( '/admin/orders/:id',            'AdminController@showOrder');
$router->post('/admin/orders/:id',            'AdminController@updateOrderStatus');

// Admin — Usuarios
$router->get( '/admin/users',                 'AdminController@users');
$router->post('/admin/users/:id',             'AdminController@updateUserStatus');

// Admin — Categorías
$router->get( '/admin/categories',            'AdminController@categories');
$router->get( '/admin/categories/create',     'AdminController@createCategory');
$router->post('/admin/categories/create',     'AdminController@storeCategory');
$router->get( '/admin/categories/:id/edit',   'AdminController@editCategory');
$router->post('/admin/categories/:id/edit',   'AdminController@updateCategory');
$router->post('/admin/categories/:id/delete', 'AdminController@deleteCategory');

// Admin — Soporte
$router->get( '/admin/support',                    'AdminController@support');
$router->get( '/admin/support/unread',             'AdminController@getSupportUnread');
$router->get( '/admin/support/:id',                'AdminController@showSupportTicket');
$router->get( '/admin/support/:id/messages',       'AdminController@getSupportMessages');
$router->post('/admin/support/:id/message',        'AdminController@replySupport');
$router->post('/admin/support/:id/status',         'AdminController@updateSupportStatus');
