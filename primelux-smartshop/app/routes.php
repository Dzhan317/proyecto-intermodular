<?php
declare(strict_types=1);

/*
 * Define todas las rutas de la aplicación.
 */

// Públicas
$router->get('/',               'HomeController@index');
$router->get('/sobre-nosotros', 'HomeController@about');
$router->get('/products',       'ProductController@index');
$router->get('/products/:slug', 'ProductController@show');
$router->get('/category/:slug', 'CategoryController@show');

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
$router->get( '/profile',          'ProfileController@index');
$router->post('/profile',          'ProfileController@update');
$router->get( '/profile/security', 'ProfileController@security');
$router->post('/profile/password', 'ProfileController@changePassword');

// Pedidos (Fase 7)
$router->get('/orders',      'OrderController@index');
$router->get('/orders/:id',  'OrderController@show');

// Carrito (Fase 5)
$router->get( '/cart',        'CartController@index');
$router->post('/cart/add',    'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');

// Checkout (Fase 6)
$router->get( '/checkout/shipping', 'CheckoutController@shipping');
$router->post('/checkout/shipping', 'CheckoutController@saveShipping');
$router->get( '/checkout/payment',  'CheckoutController@payment');
$router->get( '/checkout/success',  'CheckoutController@success');
$router->get( '/checkout/cancel',   'CheckoutController@cancel');

// Soporte (Fase 8)
$router->get( '/support',             'SupportController@index');
$router->post('/support',             'SupportController@create');
$router->get( '/support/:id',         'SupportController@show');
$router->post('/support/:id/message', 'SupportController@sendMessage');

// Admin (Fase 9)
$router->get( '/admin',                       'AdminController@dashboard');
$router->get( '/admin/products',              'AdminProductController@index');
$router->get( '/admin/products/create',       'AdminProductController@create');
$router->post('/admin/products/create',       'AdminProductController@store');
$router->get( '/admin/products/:id/edit',     'AdminProductController@edit');
$router->post('/admin/products/:id/edit',     'AdminProductController@update');
$router->post('/admin/products/:id/delete',   'AdminProductController@destroy');
$router->get( '/admin/categories',            'AdminCategoryController@index');
$router->get( '/admin/categories/create',     'AdminCategoryController@create');
$router->post('/admin/categories/create',     'AdminCategoryController@store');
$router->get( '/admin/categories/:id/edit',   'AdminCategoryController@edit');
$router->post('/admin/categories/:id/edit',   'AdminCategoryController@update');
$router->post('/admin/categories/:id/delete', 'AdminCategoryController@destroy');
$router->get( '/admin/users',                 'AdminUserController@index');
$router->get( '/admin/users/create',          'AdminUserController@create');
$router->post('/admin/users/create',          'AdminUserController@store');
$router->get( '/admin/users/:id/edit',        'AdminUserController@edit');
$router->post('/admin/users/:id/edit',        'AdminUserController@update');
$router->post('/admin/users/:id/delete',      'AdminUserController@destroy');
$router->get( '/admin/orders',                'AdminOrderController@index');
$router->get( '/admin/orders/:id',            'AdminOrderController@show');
$router->post('/admin/orders/:id/status',     'AdminOrderController@updateStatus');
$router->get( '/admin/support',               'AdminSupportController@index');
$router->get( '/admin/support/:id',           'AdminSupportController@show');
$router->post('/admin/support/:id/message',   'AdminSupportController@sendMessage');
$router->post('/admin/support/:id/close',     'AdminSupportController@close');
