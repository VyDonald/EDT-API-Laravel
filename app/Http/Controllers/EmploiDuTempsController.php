<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class EmploiDuTempsController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $edts = [];
        $snapshot = $this->firestore->collection('emplois_du_temps')->documents();
        foreach ($snapshot as $doc) {
            $edts[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($edts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'filiereId' => 'required|string',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date',
        ]);

        $docRef = $this->firestore->collection('emplois_du_temps')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Emploi du temps créé']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('emplois_du_temps')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Emploi du temps introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'filiereId' => 'string',
            'dateDebut' => 'date',
            'dateFin' => 'date',
        ]);

        $this->firestore->collection('emplois_du_temps')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Emploi du temps mis à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('emplois_du_temps')->document($id)->delete();
        return response()->json(['message' => 'Emploi du temps supprimé']);
    }

    public function visualiser($id)
    {
        $edt = $this->firestore->collection('emplois_du_temps')->document($id)->snapshot();
        if (!$edt->exists()) {
            return response()->json(['error' => 'Emploi du temps introuvable'], 404);
        }

        $seances = [];
        $snapshot = $this->firestore->collection('seances')
            ->where('emploiDuTempsId', '=', $id)
            ->documents();
        foreach ($snapshot as $doc) {
            $seances[] = array_merge(['id' => $doc->id()], $doc->data());
        }

        return response()->json([
            'emploi_du_temps' => array_merge(['id' => $edt->id()], $edt->data()),
            'seances' => $seances,
        ]);
    }
}
