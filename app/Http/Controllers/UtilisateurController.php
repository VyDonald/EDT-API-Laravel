<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UtilisateurController extends Controller
{
    protected $firestore;
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
        $this->firestore = $firebaseService->getFirestore();
    }

    public function index()
    {
        $utilisateurs = [];
        $snapshot = $this->firestore->collection('utilisateurs')->documents();
        foreach ($snapshot as $doc) {
            $utilisateurs[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($utilisateurs);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email',
            'telephone' => 'required|string',
            'roleId' => 'required|string',
            'filiereId' => 'nullable|string',
        ]);

        $docRef = $this->firestore->collection('utilisateurs')->newDocument();
        $docRef->set($data);

        return response()->json(['id' => $docRef->id(), 'message' => 'Utilisateur créé']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('utilisateurs')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Utilisateur introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'string',
            'prenom' => 'string',
            'email' => 'email',
            'telephone' => 'string',
            'roleId' => 'string',
            'filiereId' => 'nullable|string',
        ]);

        $this->firestore->collection('utilisateurs')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Utilisateur mis à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('utilisateurs')->document($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    public function modifierProfil(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'string',
            'prenom' => 'string',
            'telephone' => 'string',
        ]);

        $this->firestore->collection('utilisateurs')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Profil mis à jour']);
    }
    public function login(Request $request) {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'mot_de_passe' => 'required|string',
            ]);
            
            // Création directe d'une instance FirestoreClient, en contournant votre service
            $firestore = new \Google\Cloud\Firestore\FirestoreClient([
                'projectId' => 'emploi-api',
                'keyFilePath' => config('services.firebase.credentials'),
                'transport' => 'rest',
            ]);
            
            $utilisateur = null;
            $snapshot = $firestore->collection('utilisateurs')
                ->where('email', '=', $data['email'])
                ->documents();
            
            foreach ($snapshot as $doc) {
                if ($doc->exists()) {
                    $utilisateur = array_merge(['id' => $doc->id()], $doc->data());
                    break;
                }
            }
            
            if (!$utilisateur || !Hash::check($data['mot_de_passe'], $utilisateur['mot_de_passe'])) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }
            
            $token = $utilisateur['id'] . '|' . Str::random(60);
            
            return response()->json([
                'token' => $token,
                'utilisateur' => [
                    'id' => $utilisateur['id'],
                    'nom' => $utilisateur['nom'] ?? '',
                    'prenom' => $utilisateur['prenom'] ?? '',
                    'email' => $utilisateur['email'],
                    'roleId' => $utilisateur['roleId'] ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
