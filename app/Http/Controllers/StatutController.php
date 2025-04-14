<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
class StatutController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $statuts = [];
        $snapshot = $this->firestore->collection('statuts')->documents();
        foreach ($snapshot as $doc) {
            $statuts[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($statuts);
    }
}
