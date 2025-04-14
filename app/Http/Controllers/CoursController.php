<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class CoursController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $cours = [];
        $snapshot = $this->firestore->collection('cours')->documents();
        foreach ($snapshot as $doc) {
            $cours[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($cours);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'capacite' => 'required|integer',
            'description' => 'required|string',
            'enseignantId' => 'required|string',
            'statutId' => 'required|string',
        ]);

        $docRef = $this->firestore->collection('cours')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Cours créé']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('cours')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Cours introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'string',
            'capacite' => 'integer',
            'description' => 'string',
            'enseignantId' => 'string',
            'statutId' => 'string',
        ]);

        $this->firestore->collection('cours')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Cours mis à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('cours')->document($id)->delete();
        return response()->json(['message' => 'Cours supprimé']);
    }
}
