# Roadmap PTM SPIP

## Phase 0 — socle

- [x] dépôt privé séparé ;
- [x] manifeste `paquet.xml` ;
- [x] page espace privé selon la structure SPIP actuelle ;
- [x] autorisations ;
- [x] catalogue de signatures ;
- [x] analyseur HTML minimal ;
- [x] matrice de portage ;
- [x] checklist d'audit ;
- [x] audit d'une copie réelle : Vues Imprenables.

## Phase 1 — scan réel et couverture de l'écosystème

- [x] cartographier plugins, squelettes et modèles de la copie ;
- [x] confirmer que le scan doit analyser le HTML public rendu ;
- [x] valider `insert_head`, `affichage_final` et `taches_generales_cron` comme points d'intégration ;
- [x] construire le recensement des URLs publiques et le scan progressif ;
- [x] créer les tables de scans, URLs et observations ;
- [x] concevoir le scan sans transmission de la session administrateur ;
- [x] créer PTM Rules commun et synchroniser le catalogue SPIP ;
- [x] inventorier les grandes familles RGPD de l'écosystème SPIP 4.4 ;
- [x] créer 42 profils de plugins SPIP ;
- [x] inventorier tous les plugins actifs via l'API native SPIP ;
- [x] afficher les plugins inconnus et signaler les inconnus potentiellement pertinents ;
- [x] distinguer tracker, intégration, traitement local, collecte, service externe et CMP ;
- [x] détecter les formulaires rendus susceptibles de collecter des données personnelles ;
- [x] détecter les formulaires envoyant vers un domaine externe ;
- [x] étendre les signatures aux médias, cartes, CAPTCHA, avatars et paiements courants ;
- [ ] confirmer la version exacte du cœur SPIP sur une installation exécutable ;
- [ ] tester le scan de bout en bout sur une copie exécutable de Vues Imprenables ;
- [ ] mesurer et corriger faux positifs / faux négatifs ;
- [ ] ajouter un test navigateur pour les requêtes créées dynamiquement par JavaScript ;
- [ ] compléter le recensement si le site expose d'autres pages publiques pertinentes.

## Phase 2 — documentation et recommandations

- [ ] journal des observations ;
- [ ] diagnostic des pages légales gérées comme contenu, squelette ou page externe ;
- [ ] recommandations cliquables ;
- [ ] création en brouillon uniquement lorsque le type de contenu le permet ;
- [ ] ne jamais réécrire automatiquement un fichier de squelette légal ;
- [ ] score technique séparé des vérifications juridiques humaines.

## Phase 3 — consentement

- [x] inventorier les points d'injection frontend sur Vues Imprenables ;
- [ ] prototype de blocage de scripts/iframes via HTML final ;
- [ ] activation côté navigateur selon catégories consenties ;
- [ ] accepter/refuser de poids égal ;
- [ ] fermeture ≠ consentement ;
- [ ] contrôle permanent pour modifier son choix ;
- [ ] interopérer avec Tarteaucitron/autres CMP quand ils sont déjà présents ;
- [ ] ignorer les ressources internes SPIP, Crayons, Mediabox et scripts locaux ;
- [ ] tests cache, squelettes par rubrique, modèles et plugins tiers.

## Phase 4 — qualité / distribution

- [ ] tests SPIP 4.3/4.4, plage à réviser après confirmation du cœur ;
- [ ] `paquet.xml` validé sur une vraie installation ;
- [ ] audit sécurité ;
- [ ] documentation utilisateur ;
- [ ] ZIP installable ;
- [ ] préparation éventuelle à Plugins SPIP / SPIP-Zone.
