<?php

namespace App\Http\Controllers;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $filieres = [];
        $snapshot = $this->firestore->collection('filieres')->documents();
        foreach ($snapshot as $doc) {
            $filieres[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($filieres);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
        ]);

        $docRef = $this->firestore->collection('filieres')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Filière créée']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('filieres')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Filière introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'string',
        ]);

        $this->firestore->collection('filieres')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Filière mise à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('filieres')->document($id)->delete();
        return response()->json(['message' => 'Filière supprimée']);
    }
}
