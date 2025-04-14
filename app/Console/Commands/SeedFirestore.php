<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;

class SeedFirestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firestore:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Firestore with initial data';

    /**
     * Execute the console command.
     */
    protected $firebaseService; 
    public function __construct(FirebaseService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;    }

    public function handle()
    {
        try {
            $this->info('Connexion à Firestore via FirebaseService réussie.');
    
            // Rôles
            $roles = [
                'admin' => ['nom' => 'admin'],
                'enseignant' => ['nom' => 'enseignant'],
                'délégué' => ['nom' => 'délégué'],
                'étudiant' => ['nom' => 'étudiant'],
                'parent' => ['nom' => 'parent'],
            ];
    
            $this->info('Ajout des rôles...');
            foreach ($roles as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'roles', $id);
                $this->info("Rôle '$id' ajouté.");
            }
    
            // Statuts
            $statuts = [
                'cours_fait' => ['nom' => 'cours_fait'],
                'en_attente' => ['nom' => 'en_attente'],
                'annulé' => ['nom' => 'annulé'],
            ];
    
            $this->info('Ajout des statuts...');
            foreach ($statuts as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'statuts', $id);
                $this->info("Statut '$id' ajouté.");
            }
    
            // Créneaux
            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
            $creneaux = [];
            foreach ($jours as $jour) {
                $creneaux["matin_$jour"] = [
                    'jour' => $jour,
                    'heureDebut' => '08:00',
                    'heureFin' => '12:00',
                ];
                $creneaux["apresmidi_$jour"] = [
                    'jour' => $jour,
                    'heureDebut' => '14:00',
                    'heureFin' => '18:00',
                ];
            }
    
            $this->info('Ajout des créneaux...');
            foreach ($creneaux as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'creneaux', $id);
                $this->info("Créneau '$id' ajouté.");
            }
    
            $this->info('Firestore seed via FirebaseService terminé avec succès !');
    
        } catch (\Exception $e) {
            $this->error('Erreur lors du seeding via FirebaseService : ' . $e->getMessage());
        }
    }
}
