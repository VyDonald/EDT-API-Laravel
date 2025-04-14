<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class RoleController extends Controller
{
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $roles = [];
        $snapshot = $this->firestore->collection('roles')->documents();
        foreach ($snapshot as $doc) {
            $roles[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($roles);
    }
}
