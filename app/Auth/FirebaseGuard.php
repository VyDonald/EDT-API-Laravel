<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class FirebaseGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $firebaseService;
    protected $inputKey;
    protected $user;

    public function __construct(FirebaseService $firebaseService, Request $request, $inputKey = 'token')
    {
        $this->request = $request;
        $this->firebaseService = $firebaseService;
        $this->inputKey = $inputKey;
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        if (!$token) {
            return null;
        }

        // Extraire l'ID utilisateur du token (format: "userId|randomString")
        $parts = explode('|', $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $userId = $parts[0];
        
        // Récupérer l'utilisateur depuis Firebase
        $firestore = $this->firebaseService->getFirestore();
        $userDoc = $firestore->collection('utilisateurs')->document($userId)->snapshot();
        
        if (!$userDoc->exists()) {
            return null;
        }

        // Convertir en objet utilisateur
        $userData = $userDoc->data();
        $this->user = (object) array_merge(['id' => $userId], $userData);

        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        // Déjà géré dans le contrôleur
        return false;
    }

    protected function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->header($this->inputKey);
        }

        return $token;
    }
}