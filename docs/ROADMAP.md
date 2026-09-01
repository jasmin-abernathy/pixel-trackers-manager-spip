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

## Phase 1 — premier scan réel

- [x] cartographier plugins, squelettes et modèles de la copie ;
- [x] confirmer que le scan doit analyser le HTML public rendu ;
- [x] valider `insert_head` comme point d'injection de l'interface ;
- [x] valider `affichage_final` comme candidat au blocage léger pré-consentement ;
- [x] valider `taches_generales_cron` pour la planification ;
- [ ] confirmer la version exacte du cœur SPIP sur une installation exécutable ;
- [ ] construire le recensement des URLs publiques ;
- [ ] construire l'adaptateur de découverte SPIP ;
- [ ] rapprocher le catalogue de signatures du PTM WordPress courant ;
- [ ] créer les tables de scans/findings/journal ;
- [ ] afficher un premier vrai scan progressif dans l'espace privé ;
- [ ] tester le scan en contexte anonyme pour exclure les ajouts Crayons.

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
- [ ] ignorer les ressources internes SPIP, Crayons, Mediabox et scripts locaux ;
- [ ] tests cache, squelettes par rubrique, modèles et plugins tiers.

## Phase 4 — qualité / distribution

- [ ] tests SPIP 4.3/4.4, plage à réviser après confirmation du cœur ;
- [ ] `paquet.xml` validé sur une vraie installation ;
- [ ] audit sécurité ;
- [ ] documentation utilisateur ;
- [ ] ZIP installable ;
- [ ] préparation éventuelle à Plugins SPIP / SPIP-Zone.
