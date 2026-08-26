<?php
/**
 * Application Routes (Professional Router)
 */

$router->get('/', 'HomeController@index');
$router->get('/sobre', 'SobreController@index');
$router->get('/contato', 'ContatoController@index');
$router->post('/contato/enviar', 'ContatoController@enviar');
$router->get('/portfolio', 'PortfolioController@index');
$router->get('/servicos', 'ServicesController@index');
$router->get('/servicos/{slug}', 'ServicesController@show');
$router->get('/clientes', 'ClientesController@index');
$router->get('/faq', 'FaqController@index');
$router->get('/blog', 'BlogController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login', ['CsrfMiddleware']);
$router->get('/verificar-token', 'AuthController@showVerifyToken');
$router->post('/verificar-token', 'AuthController@verifyToken', ['CsrfMiddleware']);
$router->post('/reenviar-token', 'AuthController@resendToken', ['CsrfMiddleware']);
$router->get('/logout', 'AuthController@logout');

$router->get('/admin/dashboard', 'AdminController@dashboard', ['AuthMiddleware']);

// Blog Management
$router->get('/admin/blog', 'AdminController@blog', ['AuthMiddleware']);
$router->get('/admin/blog/novo', 'AdminController@createPost', ['AuthMiddleware']);
$router->post('/admin/blog/salvar', 'AdminController@storePost', ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/admin/blog/editar/{id}', 'AdminController@editPost', ['AuthMiddleware']);
$router->post('/admin/blog/atualizar', 'AdminController@updatePost', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/blog/deletar', 'AdminController@deletePost', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/blog/ia-gerar', 'AdminController@aiGeneratePost', ['AuthMiddleware']);

// Portfolio Management
$router->get('/admin/portfolio', 'AdminController@portfolio', ['AuthMiddleware']);
$router->get('/admin/portfolio/novo', 'AdminController@createProjeto', ['AuthMiddleware']);
$router->post('/admin/portfolio/salvar', 'AdminController@storeProjeto', ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/admin/portfolio/editar/{id}', 'AdminController@editProjeto', ['AuthMiddleware']);
$router->post('/admin/portfolio/atualizar', 'AdminController@updateProjeto', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/portfolio/deletar', 'AdminController@deleteProjeto', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/portfolio/deletar-imagem', 'AdminController@deleteImagemProjeto', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/portfolio/ia-gerar', 'AdminController@aiGeneratePortfolio', ['AuthMiddleware']);

// User Management (Admin Only)
$router->get('/admin/usuarios', 'AdminController@users', ['AuthMiddleware']);
$router->get('/admin/usuarios/novo', 'AdminController@createUser', ['AuthMiddleware']);
$router->post('/admin/usuarios/salvar', 'AdminController@storeUser', ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/admin/usuarios/verificar', 'AdminController@showVerify', ['AuthMiddleware']);
$router->post('/admin/usuarios/confirmar', 'AdminController@confirmUser', ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/admin/usuarios/editar/{id}', 'AdminController@editUser', ['AuthMiddleware']);
$router->post('/admin/usuarios/atualizar', 'AdminController@updateUser', ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/admin/usuarios/deletar', 'AdminController@deleteUser', ['AuthMiddleware', 'CsrfMiddleware']);

// Contact Messages Management
$router->get('/admin/contatos', 'AdminController@contatos', ['AuthMiddleware']);
$router->post('/admin/contatos/deletar', 'AdminController@deletarContato', ['AuthMiddleware', 'CsrfMiddleware']);


$router->get('/configurador', function() {
    require_once ROOT_PATH . '/pages/configurador.php';
});

$router->get('/fix-db', function() {
    require_once ROOT_PATH . '/check_db_tables.php';
});

$router->get('/blog/{slug}', 'BlogController@show');
$router->get('/sitemap-blog.xml', 'SitemapController@blog');
$router->get('/politicadeprivacidade', 'PageController@privacy');
$router->get('/termodeservico', 'PageController@terms');

// Authorized Emails Management (Master only)
$router->get('/admin/emails-autorizados', 'AdminController@emailsAutorizados', ['AuthMiddleware']);
$router->post('/admin/emails-autorizados/salvar', 'AdminController@salvarEmailsAutorizados', ['AuthMiddleware', 'CsrfMiddleware']);

// Registration removed - only master admin can create users via /admin/usuarios

// SEO Pages (rich content pages that redirect to main site)
$router->get('/seo/moveis-planejados-campo-grande', 'SeoPagesController@moveisPlanejados');
$router->get('/seo/marcenaria-campo-grande', 'SeoPagesController@marcenariaCampoGrande');
$router->get('/seo/melhor-marcenaria-campo-grande', 'SeoPagesController@melhorMarcenaria');
$router->get('/seo/cozinhas-planejadas', 'SeoPagesController@cozinhasPlanejadas');
$router->get('/seo/moveis-quarto-planejado', 'SeoPagesController@moveisQuarto');
$router->get('/seo/moveis-sala-estar', 'SeoPagesController@moveisSala');
$router->get('/seo/moveis-apartamentos', 'SeoPagesController@moveisApartamentos');
$router->get('/seo/mdf-vs-mdp', 'SeoPagesController@mdfVsMdp');
$router->get('/seo/home-office-planejado', 'SeoPagesController@homeOffice');
$router->get('/seo/moveis-sob-medida', 'SeoPagesController@moveisSobMedida');

// API Routes
$router->get('/api/contacts', 'ApiController@contacts', ['AuthMiddleware']);
$router->get('/api/users', 'ApiController@users', ['AuthMiddleware']);

// Configurator API Routes
$router->get('/api/configurador/catalog', 'ConfiguratorController@catalog');
$router->post('/api/configurador/projects', 'ConfiguratorController@saveProject');
$router->get('/api/configurador/projects/{id}', 'ConfiguratorController@loadProject');


