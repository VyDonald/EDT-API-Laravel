<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
class SeanceController extends Controller
{
    protected $firestore;
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $seances = [];
        $snapshot = $this->firestore->collection('seances')->documents();
        foreach ($snapshot as $doc) {
            $seances[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($seances);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'emploiDuTempsId' => 'required|string',
            'coursId' => 'required|string',
            'enseignantId' => 'required|string',
            'creneauId' => 'required|string',
            'date' => 'required|date',
            'heureDebut' => 'required|string',
            'heureFin' => 'required|string',
            'salleID' => 'required|string',
        ]);

        $docRef = $this->firestore->collection('seances')->newDocument();
        $docRef->set($data);

        // Envoyer une notification push aux étudiants de la filière
        $edt = $this->firestore->collection('emplois_du_temps')->document($data['emploiDuTempsId'])->snapshot();
        $filiereId = $edt->data()['filiereId'];
        $etudiants = $this->firestore->collection('utilisateurs')
            ->where('roleId', '=', 'étudiant')
            ->where('filiereId', '=', $filiereId)
            ->documents();

        foreach ($etudiants as $etudiant) {
            if (isset($etudiant->data()['deviceToken'])) {
                $this->firebaseService->sendNotification(
                    $etudiant->data()['deviceToken'],
                    'Nouveau cours programmé',
                    "Un cours a été ajouté le {$data['date']} de {$data['heureDebut']} à {$data['heureFin']}."
                );
            }
        }

        return response()->json(['id' => $docRef->id(), 'message' => 'Séance créée']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('seances')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Séance introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'emploiDuTempsId' => 'string',
            'coursId' => 'string',
            'enseignantId' => 'string',
            'creneauId' => 'string',
            'date' => 'date',
            'heureDebut' => 'string',
            'heureFin' => 'string',
        ]);

        $this->firestore->collection('seances')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Séance mise à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('seances')->document($id)->delete();
        return response()->json(['message' => 'Séance supprimée']);
    }

    public function annuler(Request $request, $id)
    {
        $seance = $this->firestore->collection('seances')->document($id)->snapshot();
        if (!$seance->exists()) {
            return response()->json(['error' => 'Séance introuvable'], 404);
        }

        $coursId = $seance->data()['coursId'];
        $this->firestore->collection('cours')->document($coursId)->set(['statutId' => 'annulé'], ['merge' => true]);

        // Envoyer une notification à l'admin
        $admins = $this->firestore->collection('utilisateurs')
            ->where('roleId', '=', 'admin')
            ->documents();
        foreach ($admins as $admin) {
            $notifData = [
                'titre' => 'Demande d’annulation',
                'message' => "L’enseignant a annulé la séance $id.",
                'dateEnvoi' => now()->toIso8601String(),
                'lue' => false,
                'destinataireId' => $admin->id(),
            ];
            $this->firestore->collection('notifications')->newDocument()->set($notifData);

            if (isset($admin->data()['deviceToken'])) {
                $this->firebaseService->sendNotification(
                    $admin->data()['deviceToken'],
                    'Demande d’annulation',
                    "L’enseignant a annulé la séance $id."
                );
            }
        }

        return response()->json(['message' => 'Séance annulée, notification envoyée à l’admin']);
    }

    public function marquerFait($id)
    {
        $seance = $this->firestore->collection('seances')->document($id)->snapshot();
        if (!$seance->exists()) {
            return response()->json(['error' => 'Séance introuvable'], 404);
        }

        $coursId = $seance->data()['coursId'];
        $this->firestore->collection('cours')->document($coursId)->set(['statutId' => 'cours_fait'], ['merge' => true]);

        return response()->json(['message' => 'Séance marquée comme faite']);
    }

    public function remplacerEnseignant(Request $request, $id)
    {
        $seance = $this->firestore->collection('seances')->document($id)->snapshot();
        if (!$seance->exists()) {
            return response()->json(['error' => 'Séance introuvable'], 404);
        }

        $data = $request->validate([
            'enseignantId' => 'required|string',
        ]);

        $this->firestore->collection('seances')->document($id)->set(['enseignantId' => $data['enseignantId']], ['merge' => true]);

        // Notifier les étudiants
        $edtId = $seance->data()['emploiDuTempsId'];
        $edt = $this->firestore->collection('emplois_du_temps')->document($edtId)->snapshot();
        $filiereId = $edt->data()['filiereId'];
        $etudiants = $this->firestore->collection('utilisateurs')
            ->where('roleId', '=', 'étudiant')
            ->where('filiereId', '=', $filiereId)
            ->documents();

        foreach ($etudiants as $etudiant) {
            if (isset($etudiant->data()['deviceToken'])) {
                $this->firebaseService->sendNotification(
                    $etudiant->data()['deviceToken'],
                    'Changement d’enseignant',
                    "La séance $id a un nouvel enseignant."
                );
            }
        }

        return response()->json(['message' => 'Enseignant remplacé']);
    }
}
