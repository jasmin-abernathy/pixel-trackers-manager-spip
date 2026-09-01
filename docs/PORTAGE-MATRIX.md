# Matrice PTM WordPress → SPIP

| Fonction PTM | WordPress | Cible SPIP | État |
|---|---|---|---|
| Scan public | URLs/pages WP | contenus + URLs publiques SPIP | à auditer |
| Signatures services | moteur PTM | `data/rules.json` commun | amorcé |
| Admin | pages WP Admin | espace privé `prive/exec` | amorcé |
| Droits | `manage_options` | `autoriser()` | amorcé |
| Réglages | options WP | `spip_meta` ou table dédiée | à décider |
| Historique scans | options/données PTM | table dédiée probable | à concevoir |
| Shortcodes | shortcodes WP | modèles / balises SPIP | à concevoir |
| Builders | Divi/Elementor | squelettes/modèles/inclusions | à auditer |
| Cron | WP-Cron | génie / tâches SPIP | à concevoir |
| Pages légales | pages WP | articles/pages selon site | à auditer |
| Consentement | hooks frontend | pipelines + rendu squelettes | à auditer |
| Blocage avant choix | scripts/iframes WP | scripts/modèles/squelettes | critique, à tester |

Le but n'est pas de recopier l'implémentation WordPress mais de conserver les mêmes garanties produit avec les primitives natives de SPIP.
