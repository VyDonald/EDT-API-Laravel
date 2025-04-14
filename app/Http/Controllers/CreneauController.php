<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class CreneauController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $creneaux = [];
        $snapshot = $this->firestore->collection('creneaux')->documents();
        foreach ($snapshot as $doc) {
            $creneaux[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($creneaux);
    }
}
