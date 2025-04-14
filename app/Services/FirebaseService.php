<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class FirebaseService
{
    protected $firestore;
    protected $messaging;
    protected $firestoreClient = null;

    protected function getFactory()
    {
        return (new Factory)->withServiceAccount(config('services.firebase.credentials'));
    }

public function getFirestore()
{
    if ($this->firestoreClient === null) {
        $this->firestoreClient = new \Google\Cloud\Firestore\FirestoreClient([
            'projectId' => 'emploi-api',
            'keyFilePath' => config('services.firebase.credentials'),
            'transport' => 'rest',
        ]);
    }
    return $this->firestoreClient;
}

    public function getMessaging()
    {
        if (!$this->messaging) {
            $this->messaging = $this->getFactory()->createMessaging();
        }
        return $this->messaging;
    }

    public function sendNotification($deviceToken, $title, $body)
    {
        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        $this->getMessaging()->send($message);
    }

    public function seedFirestoreDirectly(array $data, string $collection, string $documentId = null)
    {
        $credentials = json_decode(file_get_contents(config('services.firebase.credentials')), true);
        $client = new Client();
        
        // Utilise le projectId correct
        $projectId = $credentials['project_id'];
        var_dump($projectId); // Ajoute ceci pour déboguer
        $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}";
        
        if ($documentId) {
            $url .= "/{$documentId}";
        }
        
        $firestoreData = $this->convertToFirestoreFormat($data);
        
        $response = $client->patch($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken($credentials),
                'Content-Type' => 'application/json',
            ],
            'json' => $firestoreData,
        ]);
        
        return json_decode($response->getBody(), true);
    }

// Méthode auxiliaire pour obtenir un token d'accès
private function getAccessToken($credentials)
{
    $client = new Client();
    $jwt = $this->generateJwt($credentials);
    try {
        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);
        $data = json_decode($response->getBody(), true);
       // var_dump($data); // Ajoute ceci pour déboguer
        return $data['access_token'];
    } catch (\Exception $e) {
        throw new \Exception('Erreur lors de la génération du jeton d’accès : ' . $e->getMessage());
    }
}

private function generateJwt($credentials)
{
    $iat = time();
    $exp = $iat + 3600; // Token valide pour 1 heure
    $payload = [
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $iat,
        'exp' => $exp,
    ];
    return \Firebase\JWT\JWT::encode($payload, $credentials['private_key'], 'RS256');
}

// Méthode pour convertir le format des données
private function convertToFirestoreFormat($data)
{
    $result = ['fields' => []];
    
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $result['fields'][$key] = ['stringValue' => $value];
        } elseif (is_int($value)) {
            $result['fields'][$key] = ['integerValue' => $value];
        }
        // Ajouter d'autres types selon vos besoins
    }
    
    return $result;
}
 
public function authenticateUtilisateur($email, $mot_de_passe)
{
    $firestore = $this->getFirestore();
    
    $utilisateur = null;
    $snapshot = $firestore->collection('utilisateurs')
        ->where('email', '=', $email)
        ->documents();
    
    foreach ($snapshot as $doc) {
        if ($doc->exists()) {
            $utilisateur = array_merge(['id' => $doc->id()], $doc->data());
            break;
        }
    }
    
    if (!$utilisateur || !Hash::check($mot_de_passe, $utilisateur['mot_de_passe'])) {
        return null;
    }
    
    return $utilisateur;
}
}
