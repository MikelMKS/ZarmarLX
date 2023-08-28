<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedmineSession;
use App\Http\Controllers\PaginaWebController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminController;

Route::get('/',[PaginaWebController::class,'indexZar'])->name('indexZar');
Route::get('menu',[PaginaWebController::class,'menu'])->name('menu');
Route::get('distribucion',[PaginaWebController::class,'distribucion'])->name('distribucion');
Route::get('nueva',[PaginaWebController::class,'nueva'])->name('nueva');
Route::get('menuNueva',[PaginaWebController::class,'menuNueva'])->name('menuNueva');

Route::get('admonLvl',[SessionController::class,'admonLvl'])->name('admonLvl');
Route::post('valida',[SessionController::class,'valida'])->name('valida');

Route::middleware([RedmineSession::class])->group(function () {

    Route::get('closesesion',[SessionController::class,'closesesion'])->name('closesesion');

    Route::get('inicioAdmin',[AdminController::class,'inicioAdmin'])->name('inicioAdmin');

    Route::get('categorias',[AdminController::class,'categorias'])->name('categorias');
    Route::post('saveCategoria',[AdminController::class,'saveCategoria'])->name('saveCategoria');
    Route::get('modificarCategoria',[AdminController::class,'modificarCategoria'])->name('modificarCategoria');
    Route::post('updateCategoria',[AdminController::class,'updateCategoria'])->name('updateCategoria');
    Route::get('operacionesCategoria',[AdminController::class,'operacionesCategoria'])->name('operacionesCategoria');

    Route::get('seccionPrecios',[AdminController::class,'seccionPrecios'])->name('seccionPrecios');
    Route::get('seccionPreciosEdit',[AdminController::class,'seccionPreciosEdit'])->name('seccionPreciosEdit');
    Route::post('saveProducto',[AdminController::class,'saveProducto'])->name('saveProducto');
    Route::get('modificarProducto',[AdminController::class,'modificarProducto'])->name('modificarProducto');
    Route::post('updateProducto',[AdminController::class,'updateProducto'])->name('updateProducto');
    Route::get('operacionesProducto',[AdminController::class,'operacionesProducto'])->name('operacionesProducto');
    Route::get('showImagen',[AdminController::class,'showImagen'])->name('showImagen');

    Route::get('promociones',[AdminController::class,'promociones'])->name('promociones');
    Route::post('savePromociones',[AdminController::class,'savePromociones'])->name('savePromociones');
    Route::get('operacionesPromociones',[AdminController::class,'operacionesPromociones'])->name('operacionesPromociones');
});
