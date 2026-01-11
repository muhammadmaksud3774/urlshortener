<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ShortUrlController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('invitations/accept/{token}', [SuperAdminController::class, 'acceptInvitation'])->name('invitations.accept');
Route::post('invitations/accept/{token}', [SuperAdminController::class, 'registerFromInvitation'])->name('invitations.register');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	Route::get('invitations', [SuperAdminController::class, 'invitationsView'])->name('superadmin.invitations');
    Route::post('invitations', [SuperAdminController::class, 'sendInvitation'])->name('superadmin.invitations.send');
	
	Route::get('users', [SuperAdminController::class, 'usersView'])->name('superadmin.users');
    Route::post('users', [SuperAdminController::class, 'createUser'])->name('superadmin.users.store');
    Route::delete('users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.users.delete');
});

Route::middleware('auth')->get('/superadmin/invitations',[SuperAdminController::class, 'invitationsView'])->name('superadmin.invitations');

Route::middleware('auth')->post('/invitations',[SuperAdminController::class, 'sendInvitation'])->name('invitations.send');

Route::get('/invitations/accept/{token}',[SuperAdminController::class, 'acceptInvitation']);

Route::post('/invitations/register/{token}',[SuperAdminController::class, 'registerFromInvitation'])->name('invitations.register');


Route::middleware(['auth'])->prefix('superadmin')->group(function() {
    
    Route::get('companies', [SuperAdminController::class, 'companiesView'])->name('superadmin.companies');
    Route::post('companies', [SuperAdminController::class, 'createCompany'])->name('superadmin.companies.store');
    Route::delete('companies/{company}', [SuperAdminController::class, 'deleteCompany'])->name('superadmin.companies.delete');

    Route::get('users', [SuperAdminController::class, 'usersView'])->name('superadmin.users');
    Route::post('users', [SuperAdminController::class, 'createUser'])->name('superadmin.users.store');
    Route::delete('users/{user}', [SuperAdminController::class, 'deleteUser'])->name('superadmin.users.delete');
	
	Route::get('invitations', [SuperAdminController::class, 'invitationsView'])->name('superadmin.invitations');
    Route::post('invitations', [SuperAdminController::class, 'sendInvitation'])->name('superadmin.invitations.send');
});

Route::middleware('auth')->group(function () {
    Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('shorturls.index');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('shorturls.store');
});

Route::get('/s/{code}', [ShortUrlController::class, 'redirect'])->name('shorturls.redirect');
	
require __DIR__.'/auth.php';

