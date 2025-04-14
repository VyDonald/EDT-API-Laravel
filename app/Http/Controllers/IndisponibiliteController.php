<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
class IndisponibiliteController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $indisponibilites = [];
        $snapshot = $this->firestore->collection('indisponibilites')->documents();
        foreach ($snapshot as $doc) {
            $indisponibilites[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($indisponibilites);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'enseignantId' => 'required|string',
            'date' => 'required|date',
            'heureDebut' => 'required|string',
            'heureFin' => 'required|string',
        ]);

        $docRef = $this->firestore->collection('indisponibilites')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Indisponibilité ajoutée']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('indisponibilites')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Indisponibilité introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'enseignantId' => 'string',
            'date' => 'date',
            'heureDebut' => 'string',
            'heureFin' => 'string',
        ]);

        $this->firestore->collection('indisponibilites')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Indisponibilité mise à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('indisponibilites')->document($id)->delete();
        return response()->json(['message' => 'Indisponibilité supprimée']);
    }
}
