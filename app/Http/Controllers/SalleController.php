<?php

namespace App\Http\Controllers;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $salles = [];
        $snapshot = $this->firestore->collection('salles')->documents();
        foreach ($snapshot as $doc) {
            $salles[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($salles);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'capacite' => 'required|integer',
        ]);

        $docRef = $this->firestore->collection('salles')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Salle créée']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('salles')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Salle introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'string',
            'capacite' => 'integer',
        ]);

        $this->firestore->collection('salles')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Salle mise à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('salles')->document($id)->delete();
        return response()->json(['message' => 'Salle supprimée']);
    }
}
