# PTM Rules commun à WordPress et SPIP

PTM SPIP utilise désormais le même catalogue d'identifiants et de signatures que PTM WordPress.

Le dépôt de référence est `jasmin-abernathy/ptm-rules`. Le plugin embarque cependant une copie locale dans `data/rules.json` : **aucun accès GitHub n'est nécessaire pendant un scan**.

Les champs spécifiques à WordPress (`wordpress_plugin_slugs`, `wp_consent_category`) sont simplement ignorés par l'adaptateur SPIP.

Cette séparation permet notamment :

- d'ajouter une signature une seule fois ;
- d'utiliser le même identifiant `google-analytics`, `youtube`, `meta-pixel`, etc. dans les rapports des deux CMS ;
- de conserver les décisions spécifiques au CMS dans les adaptateurs ;
- de faire évoluer plus tard PTM vers d'autres CMS sans recopier le catalogue.

La version embarquée actuellement est **PTM Rules 0.1.0**.
