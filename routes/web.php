<?php

use Illuminate\Support\Facades\Route;
use App\Exports\ExportFund;
use Maatwebsite\Excel\Facades\Excel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('auth/login');
// });

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

// perfiles
Route::get('admin/profile/profiles/GetInfo/{id}','ProfilesController@GetInfo')->name('profiles.GetInfo');
Route::resource('admin/profile/profiles', 'ProfilesController');

// prueba
Route::get('admin/pruebas/prueba/GetInfo/{id}','PruebasController@GetInfo')->name('prueba.GetInfo');
Route::resource('admin/pruebas/prueba', 'PruebasController');
Route::post('admin/pruebas/prueba/import','PruebasController@import');

// usuarios
Route::resource('admin/users/user', 'UsersController');
Route::get('admin/users/user/GetInfo/{id}','UsersController@GetInfo')->name('user.GetInfo');

//Tipo de solicitud
Route::resource('admin/applications/application', 'ApplicationsController');
Route::get('admin/applications/application/GetInfo/{id}','ApplicationsController@GetInfo')->name('application.GetInfo');

// Formas de pago
Route::resource('admin/payment_forms/payment_form', 'PaymentFormsController');
Route::get('admin/payment_forms/payment_form/GetInfo/{id}','PaymentFormsController@GetInfo')->name('payment_form.GetInfo');

// aseguradoras
Route::resource('admin/insurance/insurances', 'InsuranceController');
Route::get('admin/insurance/insurances/GetInfo/{id}','InsuranceController@GetInfo')->name('insurance.GetInfo');

// moneda
Route::resource('admin/currency/currencies', 'CurrencyController');
Route::get('admin/currency/currencies/GetInfo/{id}','CurrencyController@GetInfo')->name('currencies.GetInfo');

// clientes
Route::resource('admin/client/client', 'ClientsController');
Route::get('admin/client/client/GetInfo/{id}','ClientsController@GetInfo')->name('client.GetInfo');
Route::post('admin/client/client/SaveNuc','ClientsController@SaveNuc')->name('client.SaveNuc');
Route::post('admin/client/client/SaveNucSixMonth','ClientsController@SaveNucSixMonth')->name('client.SaveNucSixMonth');

// permisos
Route::resource('admin/permission/permissions', 'PermissionsController');
Route::get('admin/permission/permissions/{id}/{id_seccion?}/{btn?}/{reference?}',[ 'uses' => 'PermissionsController@update_store', 'as' => 'admin.permission.update_store']);
Route::post('admin/permission/permissions/update_store','PermissionsController@update_store')->name('permissions.update_store');

// ------------------------------------------ Procesos de fondos --------------------------------------------------
// fondo mensual
Route::resource('funds/monthfund/monthfunds', 'MonthFundsController');
Route::get('funds/monthfund/monthfunds/GetInfo/{id}','MonthFundsController@GetInfo')->name('monthfunds.GetInfo');
Route::get('funds/monthfund/monthfunds/GetInfoLast/{id}','MonthFundsController@GetInfoLast')->name('monthfunds.GetInfoLast');
Route::post('funds/monthfund/monthfunds/updateStatus', 'MonthFundsController@updateStatus')->name('monthfunds.updateStatus');
Route::post('funds/monthfund/monthfunds/updateAuth', 'MonthFundsController@updateAuth')->name('monthfunds.updateAuth');
Route::get('funds/monthfund/monthfunds/GetNuc/{id}','MonthFundsController@GetNuc')->name('monthfunds.GetNuc');
Route::get('funds/monthfund/monthfunds/ExportFunds/{id}','MonthFundsController@ExportFunds');
Route::post('funds/monthfund/monthfunds/import','MonthFundsController@import');
Route::post('funds/monthfund/monthfunds/updateFund','MonthFundsController@updateFund');
// Route::get('funds/monthfund/monthfunds/GetNuc/{id}','MonthFundsController@GetNuc')->name('monthfunds.GetNuc');

// -------------------------------------------------- Asignación de clientes--------------------------------------------

Route::resource('admin/assiment/assigment', 'AssigmentController');
Route::get('admin/assiment/assigment/Viewclients/{id}','AssigmentController@Viewclients')->name('assigment.Viewclients');
Route::post('admin/assiment/assigment/updateClient', 'AssigmentController@updateClient')->name('assigment.updateClient');

//---------------------------------------------------Comisiones fondo mensual--------------------------------------------

Route::resource('funds/monthlycomission/monthcomission', 'MonthComissionController');
Route::get('funds/monthlycomission/monthcomission/GetInfo/{id}','MonthComissionController@GetInfo')->name('monthcomission.GetInfo');
// Route::get('funds/monthlycomission/monthcomission/GetInfoMonth/{id}/{month}/{year}','MonthComissionController@GetInfoMonth')->name('monthcomission.GetInfoMonth');
// Route::get('funds/monthlycomission/monthcomission/GetInfoLast/{id}/{month}/{year}','MonthComissionController@GetInfoLast')->name('monthcomission.GetInfoLast');
Route::get('funds/monthlycomission/monthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}','MonthComissionController@ExportPDF');
Route::post('funds/monthlycomission/monthcomission/GetInfoComition','MonthComissionController@GetInfoComition')->name('monthcomission.GetInfoComition');

//---------------------------------------------------Primera comision fondo mensual--------------------------------------------
Route::resource('funds/fstmonthcomission/fstmonthcomission', 'FstMonthComissionController');
Route::get('funds/fstmonthcomission/fstmonthcomission/GetInfo/{id}','FstMonthComissionController@GetInfo')->name('fstmonthcomission.GetInfo');
Route::get('funds/fstmonthcomission/fstmonthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}','FstMonthComissionController@ExportPDF');
Route::get('funds/fstmonthcomission/fstmonthcomission/ExportPDFAll/{id}/{tc}','FstMonthComissionController@ExportPDFAll');
Route::post('funds/fstmonthcomission/fstmonthcomission/GetInfoComition','FstMonthComissionController@GetInfoComition')->name('fstmonthcomission.GetInfoComition');
Route::get('funds/fstmonthcomission/fstmonthcomission/GetInfoAugments/{id}','FstMonthComissionController@GetInfoAugments')->name('fstmonthcomission.GetInfoAugments');

//---------------------------------------------------Fondo de 6 meses ----------------------------------------------------------
Route::resource('funds/sixmonthfund/sixmonthfunds', 'SixMonthFundController');
Route::get('funds/sixmonthfund/sixmonthfunds/GetInfo/{id}','SixMonthFundController@GetInfo')->name('sixmonthfunds.GetInfo');
Route::post('funds/sixmonthfund/sixmonthfunds/import','SixMonthFundController@import');

//---------------------------------------------------Comisiones fondo 6 meses--------------------------------------------
Route::resource('funds/sixmonthlycomission/sixmonthcomission', 'SixMonthComissionController');
Route::get('funds/sixmonthlycomission/sixmonthcomission/GetInfo/{id}','SixMonthComissionController@GetInfo')->name('sixmonthcomission.GetInfo');
Route::get('funds/sixmonthlycomission/sixmonthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}','SixMonthComissionController@ExportPDF');
Route::post('funds/sixmonthlycomission/sixmonthcomission/GetInfoComition','SixMonthComissionController@GetInfoComition')->name('sixmonthcomission.GetInfoComition');
Route::post('funds/sixmonthlycomission/sixmonthcomission/SetPayment','SixMonthComissionController@SetPayment')->name('sixmonthcomission.SetPayment');

// --------------------------------------------------------Aperturas----------------------------------------------------------
Route::resource('process/opening/opening', 'OpeningController');
