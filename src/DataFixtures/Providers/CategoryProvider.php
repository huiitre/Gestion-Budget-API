<?php

namespace App\DataFixtures\Providers;

class CategoryProvider
{
    private $categories = [
        'Achats & Shopping' => [
            'Achats & Shopping - Autres',
            'Cadeaux',
            'Articles de sport',
            'Films & DVDs',
            'High Tech',
            'Licences',
            'Livres',
            'Musique',
            'Vêtements / Chaussures',
        ],
        'Alimentation' => [
            'Supermarché / Epicerie',
            'Fast foods',
            'Restaurants',
            'Alimentation - Autres',
            'Café',
        ],
        'Divers' => [
            'Tabac',
            'Assurance',
            'A catégoriser',
            'Divers - Autres',
            'Dons',
            'Pressing',
        ],
        'Dépenses pro' => [
            'Services en ligne',
            'Comptabilité',
            'Conseils',
            'Cotisations Sociales',
            'Dépenses pro - Autres',
            'Fournitures de bureau',
            'Frais d\'expéditions',
            'Frais d\'impressions',
            'Frais de recrutement',
            'Frais juridique',
            'Maintenance bureaux',
            'Marketing',
            'Notes de frais',
            'Prévoyance',
            'Publicité',
            'Rémunérations dirigeants',
            'Salaires',
            'Sous-traitance',
            'Taxe d\'apprentissage',
        ],
        'Auto & Transports' => [
            'Carburant',
            'Assurance véhicule',
            'Péage',
            'Stationnement',
            'Auto & Transports - Autres',
            'Billets d\'avion',
            'Billets de train',
            'Entretien véhicule',
            'Location de véhicule',
            'Transports en commun',
        ],
        'Abonnements' => [
            'Téléphone mobile',
            'Internet',
            'Abonnements - Autres',
            'Câble / Satellite',
            'Téléphonie fixe',
        ],
        'Logement' => [
            'Electricité',
            'Assurance habitation',
            'Entretien',
            'Charges diverses',
            'Décoration',
            'Eau',
            'Extérieur et jardin',
            'Gaz',
            'Logement - Autres',
            'Loyer',
        ],
        'Loisirs & Sorties' => [
            'Loisirs & Sorties - Autres',
            'Sortie au restaurant',
            'Bars / Clubs',
            'Divertissements',
            'Frais Animaux',
            'Hobbies',
            'Hôtels',
            'Sorties culturelles',
            'Sport',
            'Sport d\'hiver',
            'Voyages / Vacances',
        ],
        'Santé' => [
            'Pharmacie',
            'Dentiste',
            'Médecin',
            'Mutuelle',
            'Opticien / Ophtalmo.',
            'Santé - Autres',
        ],
        'Banque' => [
            'Banque - Autres',
            'Débit mensuel carte',
            'Epargne',
            'Frais bancaires',
            'Hypothèque',
            'Incidents de paiement',
            'Remboursement emprunt',
            'Services Bancaires',
        ],
        'Esthétique & Soins' => [
            'Coiffeur',
            'Cosmétique',
            'Esthétique & Soins - Autres',
            'Spa & Massage',
            'Esthétique',
        ],
        'Impôts & Taxes' => [
            'Amendes',
            'Impôts & Taxes - Autres',
            'Impôts fonciers',
            'Impôts sur le revenu',
            'Taxes',
            'TVA',
        ],
        'Retraits, Chèques & Virements' => [
            'Chèques',
            'Retraits',
            'Virements',
            'Virements internes',
        ],
        'Scolarité & Enfants' => [
            'Baby-sitters & Crèches',
            'Ecole',
            'Fournitures scolaires',
            'Jouets',
            'Logement étudiant',
            'Pensions',
            'Prêt étudiant',
            'Scolarité & Enfants - Autres',
        ],
        'Revenus' => [
            'Salaires',
            'Autres rentrées',
            'Remboursements',
            'Ventes',
            'Allocations et pensions',
            'Dépôt d\'argent',
            'Economies',
            'Emprunt',
            'Extra',
            'Intérêts',
            'Loyers reçus',
            'Retraite',
            'Services',
            'Subventions',
            'Virements internes',
        ]
    ];

    /**
     * Get the value of Categories
     */ 
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the value of Categories
     *
     * @return  self
     */ 
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }
}
