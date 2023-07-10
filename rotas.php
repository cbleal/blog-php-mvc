<?php

use Pecee\SimpleRouter\SimpleRouter;
use sistema\Controlador\Admin\AdminDashboard;
use sistema\Nucleo\Helpers;

try {

    SimpleRouter::setDefaultNamespace('sistema\Controlador');

    SimpleRouter::get(URL_SITE, 'SiteControlador@index');
    SimpleRouter::get(URL_SITE.'blog/', 'SiteControlador@blog');
    SimpleRouter::match(['get', 'post'], URL_SITE . 'contato', 'SiteControlador@contato');
    SimpleRouter::get(URL_SITE.'sobre/', 'SiteControlador@sobre');
    SimpleRouter::get(URL_SITE.'404/', 'SiteControlador@erro404');

    SimpleRouter::get(URL_SITE.'post/{id}', 'SiteControlador@post');
    SimpleRouter::get(URL_SITE.'categoria/{id}', 'SiteControlador@categoria');
    
    SimpleRouter::post(URL_SITE.'buscar', 'SiteControlador@buscar');

    SimpleRouter::group(['namespace' => 'Admin'], function () {

        // admin login
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'login', 'AdminLogin@login');

        // admin dashboard
        SimpleRouter::get(URL_ADMIN.'dashboard', 'AdminDashboard@dashboard');
        SimpleRouter::get(URL_ADMIN.'sair', 'AdminDashboard@sair');

        // admin usuarios
        SimpleRouter::get(URL_ADMIN.'usuarios/index', 'AdminUsuarios@index');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'usuarios/cadastrar', 'AdminUsuarios@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'usuarios/editar/{id}', 'AdminUsuarios@editar');
        SimpleRouter::get(URL_ADMIN.'usuarios/deletar/{id}', 'AdminUsuarios@deletar');

        // admin posts
        SimpleRouter::get(URL_ADMIN.'posts/index', 'AdminPosts@index');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'posts/cadastrar', 'AdminPosts@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'posts/editar/{id}', 'AdminPosts@editar');
        SimpleRouter::get(URL_ADMIN.'posts/deletar/{id}', 'AdminPosts@deletar');
        SimpleRouter::post(URL_ADMIN.'posts/datatable', 'AdminPosts@datatable');

        // admin categorias
        SimpleRouter::get(URL_ADMIN.'categorias/index', 'AdminCategorias@index');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'categorias/cadastrar', 'AdminCategorias@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN.'categorias/editar/{id}', 'AdminCategorias@editar');
        SimpleRouter::get(URL_ADMIN.'categorias/deletar/{id}', 'AdminCategorias@deletar');
    });

    SimpleRouter::start();

} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException $e) {
    if (Helpers::localhost()):
        echo $e->getMessage();
    else:
        echo Helpers::renderizar('404.html');
    endif;
}
