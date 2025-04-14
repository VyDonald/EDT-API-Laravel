<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;

class SeedFirestoreAll extends Command
{
    protected $signature = 'firestore:seed-all';
    protected $description = 'Seed Firestore with all data (utilisateurs, filieres, emplois du temps, seances, cours, salles, indisponibilites, notifications)';

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    public function handle()
    {
        try {
            $this->info('Connexion à Firestore via FirebaseService réussie.');

            // Utilisateurs (conforme à UtilisateurController)
            $utilisateurs = [
                'utilisateur_1' => [
                    'nom' => 'Dupont',
                    'prenom' => 'Jean',
                    'email' => 'jean.dupont@example.com',
                    'telephone' => '123456789',
                    'roleId' => 'admin',
                    'filiereId' => null,
                    'deviceToken' => 'fake_device_token_admin',
                    'mot_de_passe' => Hash::make('password123'),
                ],
                'utilisateur_2' => [
                    'nom' => 'Curie',
                    'prenom' => 'Marie',
                    'email' => 'marie.curie@example.com',
                    'telephone' => '987654321',
                    'roleId' => 'enseignant',
                    'filiereId' => null,
                    'deviceToken' => 'fake_device_token_enseignant',
                    'mot_de_passe' => Hash::make('password123'),
                ],
                'utilisateur_3' => [
                    'nom' => 'Martin',
                    'prenom' => 'Paul',
                    'email' => 'paul.martin@example.com',
                    'telephone' => '456789123',
                    'roleId' => 'délégué',
                    'filiereId' => 'filiere_1',
                    'deviceToken' => 'fake_device_token_délégué',
                    'mot_de_passe' => Hash::make('password123'),
                ],
                'utilisateur_4' => [
                    'nom' => 'Durand',
                    'prenom' => 'Sophie',
                    'email' => 'sophie.durand@example.com',
                    'telephone' => '321654987',
                    'roleId' => 'étudiant',
                    'filiereId' => 'filiere_1',
                    'deviceToken' => 'fake_device_token_étudiant',
                    'mot_de_passe' => Hash::make('password123'),
                ],
                'utilisateur_5' => [
                    'nom' => 'Parent',
                    'prenom' => 'Lucie',
                    'email' => 'lucie.parent@example.com',
                    'telephone' => '654987321',
                    'roleId' => 'parent',
                    'filiereId' => null,
                    'deviceToken' => 'fake_device_token_parent',
                    'mot_de_passe' => Hash::make('password123'),
                ],
            ];

            $this->info('Ajout des utilisateurs...');
            foreach ($utilisateurs as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'utilisateurs', $id);
                $this->info("Utilisateur '$id' ajouté.");
            }

            // Filières (conforme à FiliereController)
            $filieres = [
                'filiere_1' => ['nom' => 'Informatique'],
                'filiere_2' => ['nom' => 'Mathématiques'],
            ];

            $this->info('Ajout des filières...');
            foreach ($filieres as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'filieres', $id);
                $this->info("Filière '$id' ajoutée.");
            }

            // Cours (conforme à CoursController)
            $cours = [
                'cours_1' => [
                    'nom' => 'Programmation PHP',
                    'capacite' => 30,
                    'description' => 'Cours de programmation PHP',
                    'enseignantId' => 'utilisateur_2',
                    'statutId' => 'en_attente',
                ],
                'cours_2' => [
                    'nom' => 'Algèbre Linéaire',
                    'capacite' => 40,
                    'description' => 'Cours d’algèbre linéaire',
                    'enseignantId' => 'utilisateur_2',
                    'statutId' => 'en_attente',
                ],
            ];

            $this->info('Ajout des cours...');
            foreach ($cours as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'cours', $id);
                $this->info("Cours '$id' ajouté.");
            }

            // Salles (conforme à SalleController)
            $salles = [
                'salle_1' => ['nom' => 'Salle A1', 'capacite' => 30],
                'salle_2' => ['nom' => 'Salle B2', 'capacite' => 40],
            ];

            $this->info('Ajout des salles...');
            foreach ($salles as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'salles', $id);
                $this->info("Salle '$id' ajoutée.");
            }

            // Emplois du temps (conforme à EmploiDuTempsController)
            $emploisDuTemps = [
                'emploi_1' => [
                    'filiereId' => 'filiere_1',
                    'dateDebut' => '2025-04-01',
                    'dateFin' => '2025-06-30',
                ],
                'emploi_2' => [
                    'filiereId' => 'filiere_2',
                    'dateDebut' => '2025-04-01',
                    'dateFin' => '2025-06-30',
                ],
            ];

            $this->info('Ajout des emplois du temps...');
            foreach ($emploisDuTemps as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'emplois-du-temps', $id);
                $this->info("Emploi du temps '$id' ajouté.");
            }

            // Séances (conforme à SeanceController)
            $seances = [
                'seance_1' => [
                    'emploiDuTempsId' => 'emploi_1',
                    'coursId' => 'cours_1',
                    'enseignantId' => 'utilisateur_2',
                    'creneauId' => 'matin_lundi',
                    'date' => '2025-04-14',
                    'heureDebut' => '08:00',
                    'heureFin' => '12:00',
                ],
                'seance_2' => [
                    'emploiDuTempsId' => 'emploi_2',
                    'coursId' => 'cours_2',
                    'enseignantId' => 'utilisateur_2',
                    'creneauId' => 'apresmidi_mardi',
                    'date' => '2025-04-15',
                    'heureDebut' => '14:00',
                    'heureFin' => '18:00',
                    'salleID' => 'salle_1'
                ],
            ];

            $this->info('Ajout des séances...');
            foreach ($seances as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'seances', $id);
                $this->info("Séance '$id' ajoutée.");
            }

            // Indisponibilités (conforme à IndisponibiliteController)
            $indisponibilites = [
                'indisponibilite_1' => [
                    'enseignantId' => 'utilisateur_2',
                    'date' => '2025-04-16',
                    'heureDebut' => '08:00',
                    'heureFin' => '12:00',
                ],
            ];

            $this->info('Ajout des indisponibilités...');
            foreach ($indisponibilites as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'indisponibilites', $id);
                $this->info("Indisponibilité '$id' ajoutée.");
            }

            // Notifications (conforme à NotificationController)
            $notifications = [
                'notification_1' => [
                    'titre' => 'Rappel de cours',
                    'message' => 'Votre cours de Programmation PHP commence dans 1 heure.',
                    'destinataireId' => 'utilisateur_4',
                    'dateEnvoi' => now()->toIso8601String(),
                    'lue' => false,
                ],
                'notification_2' => [
                    'titre' => 'Annulation de cours',
                    'message' => 'Le cours d’Algèbre Linéaire est annulé.',
                    'destinataireId' => 'utilisateur_2',
                    'dateEnvoi' => now()->toIso8601String(),
                    'lue' => false,
                ],
            ];

            $this->info('Ajout des notifications...');
            foreach ($notifications as $id => $data) {
                $this->firebaseService->seedFirestoreDirectly($data, 'notifications', $id);
                $this->info("Notification '$id' ajoutée.");
            }

            $this->info('Firestore seed all terminé avec succès !');

        } catch (\Exception $e) {
            $this->error('Erreur lors du seeding all via FirebaseService : ' . $e->getMessage());
        }
    }
}