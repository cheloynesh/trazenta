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

// régimen
Route::resource('admin/regime/regime', 'RegimeController');
Route::get('admin/regime/regime/GetInfo/{id}','RegimeController@GetInfo')->name('regime.GetInfo');

// Cálculo de Cobro
Route::resource('admin/charge/charges', 'ChargeController');
Route::get('admin/charge/charges/GetInfo/{id}','ChargeController@GetInfo')->name('charges.GetInfo');

// Tipos de Servicios
Route::resource('admin/service_type/service_type', 'ServiceTypeController');
Route::get('admin/service_type/service_type/GetInfo/{id}','ServiceTypeController@GetInfo')->name('servicetype.GetInfo');

// Lideres
Route::resource('admin/leader/leader', 'LeaderController');
Route::get('admin/leader/leader/ViewNonAssigned/{id}','LeaderController@ViewNonAssigned')->name('leader.ViewNonAssigned');
Route::get('admin/leader/leader/ViewAssigned/{id}','LeaderController@ViewAssigned')->name('leader.ViewAssigned');
Route::get('admin/leader/leader/ViewNonLeader/{id}','LeaderController@ViewNonLeader')->name('leader.ViewNonLeader');
Route::get('admin/leader/leader/GetInfo/{id}','LeaderController@GetInfo')->name('leader.GetInfo');
Route::get('admin/leader/leader/GetInfoAll/{id}','LeaderController@GetInfoAll')->name('leader.GetInfoAll');
Route::post('admin/leader/leader/Dessign','LeaderController@Dessign')->name('leader.Dessign');
Route::post('admin/leader/leader/DeleteLeader','LeaderController@DeleteLeader')->name('leader.DeleteLeader');
Route::post('admin/leader/leader/Assign','LeaderController@Assign')->name('leader.Assign');
Route::post('admin/leader/leader/DeleteAgent','LeaderController@DeleteAgent')->name('leader.DeleteAgent');
Route::post('admin/leader/leader/GetInfoComition','LeaderController@GetInfoComition')->name('leader.GetInfoComition');
Route::get('admin/leader/leader/GetPDF/{id}/{year}/{month}/{TC}/{fst_yr}/{scnd_yr}', 'LeaderController@GetPDF')->name('leader.GetPDF');
Route::get('admin/leader/leader/GetSixMonth/{id}/{yr}','LeaderController@GetSixMonth')->name('leader.GetSixMonth');

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
Route::post('funds/monthfund/monthfunds/deleteFund','MonthFundsController@deleteFund');
Route::get('funds/monthfund/monthfunds/updateFundNet/{id}', 'MonthFundsController@updateFundNet')->name('monthfunds.updateFundNet');
Route::get('funds/monthfund/monthfunds/GetCP/{id}', 'MonthFundsController@GetCP')->name('monthfunds.GetCP');
Route::get('funds/monthfund/monthfunds/GetCharge/{id}', 'MonthFundsController@GetCharge')->name('monthfunds.GetCharge');
Route::post('funds/monthfund/monthfunds/updateCharge', 'MonthFundsController@updateCharge')->name('monthfunds.updateCharge');
// Route::get('funds/monthfund/monthfunds/GetNuc/{id}','MonthFundsController@GetNuc')->name('monthfunds.GetNuc');

// -------------------------------------------------- Asignación de clientes--------------------------------------------

Route::resource('admin/assiment/assigment', 'AssigmentController');
Route::get('admin/assiment/assigment/Viewclients/{id}','AssigmentController@Viewclients')->name('assigment.Viewclients');
Route::get('admin/assiment/assigment/ViewNonAssigned/{id}','AssigmentController@ViewNonAssigned')->name('assigment.ViewNonAssigned');
Route::post('admin/assiment/assigment/updateClient', 'AssigmentController@updateClient')->name('assigment.updateClient');

//---------------------------------------------------Comisiones fondo mensual--------------------------------------------

Route::resource('funds/monthlycomission/monthcomission', 'MonthComissionController');
Route::get('funds/monthlycomission/monthcomission/GetInfo/{id}','MonthComissionController@GetInfo')->name('monthcomission.GetInfo');
// Route::get('funds/monthlycomission/monthcomission/GetInfoMonth/{id}/{month}/{year}','MonthComissionController@GetInfoMonth')->name('monthcomission.GetInfoMonth');
// Route::get('funds/monthlycomission/monthcomission/GetInfoLast/{id}/{month}/{year}','MonthComissionController@GetInfoLast')->name('monthcomission.GetInfoLast');
Route::get('funds/monthlycomission/monthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}/{dlls}','MonthComissionController@ExportPDF');
Route::post('funds/monthlycomission/monthcomission/GetInfoComition','MonthComissionController@GetInfoComition')->name('monthcomission.GetInfoComition');
Route::post('funds/monthlycomission/monthcomission/updateDlls','MonthComissionController@updateDlls')->name('monthcomission.updateDlls');
Route::post('funds/monthlycomission/monthcomission/setStatDate','MonthComissionController@setStatDate')->name('monthcomission.setStatDate');
Route::post('funds/monthlycomission/monthcomission/setNullDate','MonthComissionController@setNullDate')->name('monthcomission.setNullDate');

//---------------------------------------------------Primera comision fondo mensual--------------------------------------------
Route::resource('funds/fstmonthcomission/fstmonthcomission', 'FstMonthComissionController');
Route::get('funds/fstmonthcomission/fstmonthcomission/GetInfo/{id}','FstMonthComissionController@GetInfo')->name('fstmonthcomission.GetInfo');
Route::get('funds/fstmonthcomission/fstmonthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}','FstMonthComissionController@ExportPDF');
Route::get('funds/fstmonthcomission/fstmonthcomission/ExportPDFAll/{id}/{tc}/{month}/{year}','FstMonthComissionController@ExportPDFAll');
Route::post('funds/fstmonthcomission/fstmonthcomission/GetInfoComition','FstMonthComissionController@GetInfoComition')->name('fstmonthcomission.GetInfoComition');
Route::get('funds/fstmonthcomission/fstmonthcomission/GetInfoAugments/{id}','FstMonthComissionController@GetInfoAugments')->name('fstmonthcomission.GetInfoAugments');
Route::post('funds/fstmonthcomission/fstmonthcomission/setStatDate','FstMonthComissionController@setStatDate')->name('fstmonthcomission.setStatDate');
Route::post('funds/fstmonthcomission/fstmonthcomission/setNullDate','FstMonthComissionController@setNullDate')->name('fstmonthcomission.setNullDate');

//---------------------------------------------------Fondo de 6 meses ----------------------------------------------------------
Route::resource('funds/sixmonthfund/sixmonthfunds', 'SixMonthFundController');
Route::get('funds/sixmonthfund/sixmonthfunds/GetInfo/{id}','SixMonthFundController@GetInfo')->name('sixmonthfunds.GetInfo');
Route::get('funds/sixmonthfund/sixmonthfunds/import/{id}','SixMonthFundController@import')->name('sixmonthfunds.import');
Route::get('funds/sixmonthfund/sixmonthfunds/GetNuc/{id}','SixMonthFundController@GetNuc')->name('sixmonthfunds.GetNuc');
Route::get('funds/sixmonthfund/sixmonthfunds/GetLP/{id}', 'SixMonthFundController@GetLP')->name('sixmonthfunds.GetLP');

//---------------------------------------------------Comisiones fondo 6 meses--------------------------------------------
Route::resource('funds/sixmonthlycomission/sixmonthcomission', 'SixMonthComissionController');
Route::get('funds/sixmonthlycomission/sixmonthcomission/GetInfo/{id}','SixMonthComissionController@GetInfo')->name('sixmonthcomission.GetInfo');
Route::get('funds/sixmonthlycomission/sixmonthcomission/ExportPDF/{id}/{month}/{year}/{TC}/{regime}','SixMonthComissionController@ExportPDF');
Route::get('funds/sixmonthlycomission/sixmonthcomission/ExportPDFAll/{id}/{month}/{year}/{TC}/{regime}','SixMonthComissionController@ExportPDFAll');
Route::post('funds/sixmonthlycomission/sixmonthcomission/GetInfoComition','SixMonthComissionController@GetInfoComition')->name('sixmonthcomission.GetInfoComition');
Route::post('funds/sixmonthlycomission/sixmonthcomission/SetPayment','SixMonthComissionController@SetPayment')->name('sixmonthcomission.SetPayment');
Route::post('funds/sixmonthlycomission/sixmonthcomission/SetPaymentAll','SixMonthComissionController@SetPaymentAll')->name('sixmonthcomission.SetPaymentAll');
Route::get('funds/sixmonthlycomission/sixmonthcomission/GetInfoAgents/{id}','SixMonthComissionController@GetInfoAgents');
Route::get('funds/sixmonthlycomission/sixmonthcomission/GetComitions/{paid}','SixMonthComissionController@GetComitions');

// --------------------------------------------------------Aperturas----------------------------------------------------------
Route::resource('process/opening/opening', 'OpeningController');
Route::get('process/opening/opening/GetInfo/{id}','OpeningController@GetInfo')->name('opening.GetInfo');
Route::get('process/opening/opening/GetinfoStatus/{id}','OpeningController@GetinfoStatus')->name('opening.GetinfoStatus');
Route::get('process/opening/opening/GetinfoFund/{id}','OpeningController@GetinfoFund')->name('opening.GetinfoFund');
Route::post('process/opening/opening/updateStatus', 'OpeningController@updateStatus')->name('opening.updateStatus');

// --------------------------------------------------------Servicios----------------------------------------------------------
Route::resource('process/services/services', 'ServicesController');
Route::get('process/services/services/GetInfo/{id}','ServicesController@GetInfo')->name('services.GetInfo');
Route::get('process/services/services/GetInfoStatus/{id}','ServicesController@GetInfoStatus')->name('services.GetInfoStatus');
Route::post('process/services/services/updateStatus', 'ServicesController@updateStatus')->name('services.updateStatus');
Route::post('process/services/services/updateStatusInt', 'ServicesController@updateStatusInt')->name('services.updateStatusInt');
Route::get('process/services/services/GetFunds/{type}','ServicesController@GetFunds')->name('services.GetFunds');

// --------------------------------------------------------Reportes------------------------------------------------------------
Route::resource('reports/moneyflow/moneyflow', 'MoneyFlowController');
Route::get('reports/moneyflow/moneyflow/GetInfo/{id}','MoneyFlowController@GetInfo')->name('moneyflow.GetInfo');
Route::get('reports/moneyflow/moneyflow/GetInfoFilters/{id}','MoneyFlowController@GetInfoFilters')->name('moneyflow.GetInfoFilters');
Route::get('reports/moneyflow/moneyflow/ExportBreakdown/{id}/{month}/{quarter}/{year}/{fund}/{type}','MoneyFlowController@ExportBreakdown');

// --------------------------------------------------------Comisiones------------------------------------------------------------
Route::resource('comitions/comition/comition', 'ComitionController');
Route::get('comitions/comition/comition/GetInfo/{id}/{invoice}/{contpp}/{contpa}/{lpnopay}','ComitionController@GetInfo')->name('comition.GetInfo');
Route::post('comitions/comition/comition/setStatDate','ComitionController@setStatDate')->name('comition.setStatDate');
Route::post('comitions/comition/comition/setNullDate','ComitionController@setNullDate')->name('comition.setNullDate');
Route::post('comitions/comition/comition/setStatDateMoves','ComitionController@setStatDateMoves')->name('comition.setStatDateMoves');
Route::post('comitions/comition/comition/setNullDateMoves','ComitionController@setNullDateMoves')->name('comition.setNullDateMoves');
Route::post('comitions/comition/comition/setStatDateLP','ComitionController@setStatDateLP')->name('comition.setStatDateLP');
Route::post('comitions/comition/comition/setNullDateLP','ComitionController@setNullDateLP')->name('comition.setNullDateLP');
Route::post('comitions/comition/comition/setStatDateMultLP','ComitionController@setStatDateMultLP')->name('comition.setStatDateMultLP');
Route::post('comitions/comition/comition/UpdateRegime','ComitionController@UpdateRegime')->name('comition.UpdateRegime');

// --------------------------------------------------------Comisiones Atrasadas------------------------------------------------------------
Route::resource('comitions/delayComition/delayComition', 'DelayComitionController');
Route::get('comitions/delayComition/delayComition/GetInfo/{id}/{invoice}/{contpp}/{contpa}/{lpnopay}','DelayComitionController@GetInfo')->name('delaycomition.GetInfo');
Route::post('comitions/delayComition/delayComition/setStatDateRec','DelayComitionController@setStatDateRec')->name('delaycomition.setStatDateRec');
Route::post('comitions/delayComition/delayComition/setNullDateRec','DelayComitionController@setNullDateRec')->name('delaycomition.setNullDateRec');
Route::post('comitions/delayComition/delayComition/setStatDate','DelayComitionController@setStatDate')->name('delaycomition.setStatDate');
Route::post('comitions/delayComition/delayComition/setNullDate','DelayComitionController@setNullDate')->name('delaycomition.setNullDate');
Route::post('comitions/delayComition/delayComition/setStatDateMoves','DelayComitionController@setStatDateMoves')->name('delaycomition.setStatDateMoves');
Route::post('comitions/delayComition/delayComition/setNullDateMoves','DelayComitionController@setNullDateMoves')->name('delaycomition.setNullDateMoves');
Route::post('comitions/delayComition/delayComition/setStatDateLP','DelayComitionController@setStatDateLP')->name('delaycomition.setStatDateLP');
Route::post('comitions/delayComition/delayComition/setNullDateLP','DelayComitionController@setNullDateLP')->name('delaycomition.setNullDateLP');
Route::post('comitions/delayComition/delayComition/setStatDateMultLP','DelayComitionController@setStatDateMultLP')->name('delaycomition.setStatDateMultLP');
Route::post('comitions/delayComition/delayComition/UpdateRegime','DelayComitionController@UpdateRegime')->name('delaycomition.UpdateRegime');
Route::get('comitions/delayComition/delayComition/GetPDFAuth/{id}/{type}/{authType}','DelayComitionController@GetPDFAuth')->name('delaycomition.GetPDFAuth');

// --------------------------------------------------------Historial Comisiones------------------------------------------------------------
Route::resource('comitions/history/history', 'PaymentHistoryController');
Route::get('comitions/history/history/GetInfo/{id}/{invoice}/{contpp}/{contpa}/{lpnopay}','PaymentHistoryController@GetInfo')->name('historycomition.GetInfo');
