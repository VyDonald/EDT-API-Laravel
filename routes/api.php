<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\StatutController;
use App\Http\Controllers\CreneauController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\IndisponibiliteController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
   // return $request->user();
//});
Route::post('login', [UtilisateurController::class, 'login']);

Route::middleware('auth:api')->group(function () {
     //Rôles (lecture seule, pré-remplis)
    Route::get('roles', [RoleController::class, 'index']);

     //Utilisateurs
    Route::apiResource('utilisateurs', UtilisateurController::class);
    Route::post('utilisateurs/{id}/modifier-profil', [UtilisateurController::class, 'modifierProfil']);

     //Filières
    Route::apiResource('filieres', FiliereController::class);

     //Emplois du temps
    Route::apiResource('emplois-du-temps', EmploiDuTempsController::class);
    Route::get('emplois-du-temps/{id}/visualiser', [EmploiDuTempsController::class, 'visualiser']);

     //Séances
    Route::apiResource('seances', SeanceController::class);
    Route::post('seances/{id}/annuler', [SeanceController::class, 'annuler'])->middleware('role:enseignant');
    Route::post('seances/{id}/marquer-fait', [SeanceController::class, 'marquerFait'])->middleware('role:délégué');
    Route::post('seances/{id}/remplacer-enseignant', [SeanceController::class, 'remplacerEnseignant'])->middleware('role:admin');

     //Cours (seul l'admin peut créer/modifier/supprimer)
    Route::apiResource('cours', CoursController::class)->middleware('role:admin');

    // Statuts (lecture seule, pré-remplis)
    Route::get('statuts', [StatutController::class, 'index']);

     //Créneaux (lecture seule, pré-remplis)
    Route::get('creneaux', [CreneauController::class, 'index']);

     //Salles
    Route::apiResource('salles', SalleController::class);

     //Indisponibilités
    Route::apiResource('indisponibilites', IndisponibiliteController::class);

     //Notifications
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/{id}/marquer-lue', [NotificationController::class, 'marquerLue']);
});