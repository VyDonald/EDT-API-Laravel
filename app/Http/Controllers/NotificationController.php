<?php

namespace App\Http\Controllers;
use App\Services\FirebaseService;

use Illuminate\Http\Request;

class NotificationController extends Controller
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
        $notifications = [];
        $snapshot = $this->firestore->collection('notifications')->documents();
        foreach ($snapshot as $doc) {
            $notifications[] = array_merge(['id' => $doc->id()], $doc->data());
        }
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string',
            'message' => 'required|string',
            'destinataireId' => 'required|string',
        ]);

        $data['dateEnvoi'] = now()->toIso8601String();
        $data['lue'] = false;

        $docRef = $this->firestore->collection('notifications')->newDocument();
        $docRef->set($data);

        // Envoyer une notification push
        $destinataire = $this->firestore->collection('utilisateurs')->document($data['destinataireId'])->snapshot();
        if ($destinataire->exists() && isset($destinataire->data()['deviceToken'])) {
            $this->firebaseService->sendNotification(
                $destinataire->data()['deviceToken'],
                $data['titre'],
                $data['message']
            );
        }

        return response()->json(['id' => $docRef->id(), 'message' => 'Notification envoyée']);
    }

    public function show($id)
    {
        $doc = $this->firestore->collection('notifications')->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['error' => 'Notification introuvable'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'titre' => 'string',
            'message' => 'string',
            'destinataireId' => 'string',
        ]);

        $this->firestore->collection('notifications')->document($id)->set($data, ['merge' => true]);
        return response()->json(['message' => 'Notification mise à jour']);
    }

    public function destroy($id)
    {
        $this->firestore->collection('notifications')->document($id)->delete();
        return response()->json(['message' => 'Notification supprimée']);
    }

    public function marquerLue($id)
    {
        $this->firestore->collection('notifications')->document($id)->set(['lue' => true], ['merge' => true]);
        return response()->json(['message' => 'Notification marquée comme lue']);
    }
}
