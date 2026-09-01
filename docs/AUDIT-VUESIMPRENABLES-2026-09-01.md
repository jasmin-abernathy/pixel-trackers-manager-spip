# Audit Vues Imprenables — 2026-09-01

Source analysée : copie de fichiers du site fournie pour préparer PTM SPIP.

## Limites de la copie

La copie contient principalement `config/`, `plugins/`, `plugins-dist/`, `squelettes-dist/` et `squelettes/`.
Elle ne contient pas le cœur complet `ecrire/` ni la base SQL : la version SPIP exacte et la liste certaine des plugins actifs ne peuvent donc pas être établies à partir de cette archive seule.

Des fichiers sensibles (`config/connect.php`, `config/cles.php`) étaient présents dans l'archive. Leur contenu n'est pas nécessaire à PTM et ne doit pas être inclus dans les futures copies de travail.

## Éléments structurants observés

### Squelettes maison

Le site repose sur des squelettes classiques et lisibles :

- `sommaire.html` ;
- `article.html` ;
- `rubrique.html` ;
- variantes `rubrique=5.html`, `rubrique=16.html`, `rubrique=38.html` ;
- `inc/head.html`, `inc/bann.html`, `inc/footer.html`, `inc/ariane.html` ;
- formulaire local `formulaires/ecrire_auteur.html` ;
- page légale/credits `mentions.html`.

Le plugin **Squelettes par Rubrique** est présent, ce qui confirme que PTM ne doit jamais supposer un squelette unique pour toutes les rubriques.

### Plugins additionnels présents dans `plugins/auto`

- Crayons 3.3.0 — compatible SPIP 4.1 à 4.x ;
- Image Typo 1.0.3 — compatible SPIP 4.3 à 5.x ;
- Squelettes par Rubrique 2.2.0 — compatible SPIP 4.1 à 4.x.

La présence d'Image Typo implique un environnement au moins compatible SPIP 4.3 si ce plugin est actif. La version exacte du cœur reste à confirmer sur l'installation elle-même.

## Confidentialité / traceurs observés dans les fichiers

Aucun tracker tiers évident n'est codé en dur dans les squelettes personnalisés analysés.

Les scripts particuliers de `rubrique=38.html` sont locaux (`jquery.easing.1.2.js`, `jquery.anythingslider.js`). Les polices utilisées par Image Typo sont locales. Aucun Google Fonts externe n'a été trouvé dans les squelettes.

Les liens HTTP présents dans `mentions.html` sont de simples liens sortants et ne constituent pas à eux seuls un traceur.

Le formulaire de contact est un formulaire SPIP local (`#FORMULAIRE_ECRIRE_AUTEUR`) avec champ anti-spam de type honeypot. Aucun reCAPTCHA tiers n'est présent dans cette copie.

## Où les services tiers peuvent réellement apparaître

`article.html` et `rubrique.html` rendent `#TEXTE`. Un service tiers peut donc être introduit par le contenu éditorial lui-même : iframe vidéo, carte, script, image distante, formulaire externe, etc.

Conclusion : **un scanner de fichiers ne suffit pas**. PTM SPIP doit analyser le HTML public rendu de façon anonyme.

## Architecture validée pour PTM SPIP

### 1. Injection de l'interface

`inc/head.html` contient `#INSERT_HEAD_CSS` et `#INSERT_HEAD`.

Le pipeline SPIP `insert_head` peut donc injecter les futurs CSS/JS PTM sans modifier les squelettes du site.

### 2. Blocage avant consentement

Le pipeline `affichage_final` est le meilleur candidat pour une transformation légère du HTML juste avant envoi au navigateur.

La stratégie proposée :

1. réponse serveur identique pour tous, services optionnels bloqués par défaut ;
2. PTM remplace les scripts/iframes reconnus par des placeholders ou éléments `data-*` ;
3. un JS local lit le choix du visiteur ;
4. seuls les services autorisés sont réactivés côté navigateur.

Cette approche évite de créer une variante de cache SPIP par utilisateur.

Le traitement `affichage_final` devra être extrêmement peu coûteux : test rapide des signatures, aucun crawl ni écriture SQL à chaque page.

### 3. Scan actif

Le scan complet ne doit pas être effectué dans `affichage_final`.

Il sera déclenché depuis l'espace privé :

- recenser les objets publiés et URLs publiques ;
- récupérer les pages en contexte anonyme ;
- analyser par petits lots ;
- reprendre le scan sans perdre les résultats si une URL échoue ;
- distinguer preuve active / intégration disponible / vérification humaine.

### 4. Scan planifié

Utiliser `taches_generales_cron` / le génie SPIP pour reprendre ou programmer des scans par petits lots.

### 5. Stockage

`spip_meta` peut convenir aux réglages légers, mais pas à un historique de scans complet.

Prévoir des tables dédiées pour :

- scans ;
- observations/findings ;
- journal d'actions.

## Compatibilités particulières à prévoir

### Crayons

Crayons peut ajouter des éléments et scripts pour les utilisateurs connectés. Le scan PTM doit donc récupérer les pages **sans session administrateur** pour éviter les faux positifs.

Les scripts Crayons sont locaux et ne doivent pas être pris pour des traceurs.

### Mediabox

Les galeries utilisent Mediabox et des documents locaux. Les assets Mediabox/SPIP doivent être identifiés comme ressources internes, pas comme services tiers.

### Squelettes par Rubrique

Le scan doit se baser sur les URLs/rendus réels, et non uniquement sur `rubrique.html`.

## Pages légales

Le lien footer vers `#URL_PAGE{mentions}` pointe vers un squelette `mentions.html` codé en fichier, pas vers un article éditorial classique.

PTM ne doit donc **jamais l'écraser automatiquement**.

Pour ce type de site, PTM devra distinguer :

- page légale gérée comme contenu SPIP éditable ;
- page légale gérée par squelette/fichier ;
- page externe.

Pour une page-fichier, PTM pourra analyser son contenu et proposer une recommandation ou une migration vers un contenu éditable, mais pas publier silencieusement une modification.

## Autre anomalie repérée

`rubrique=38.html` référence `squelettes/scripts/page.css`, absent de la copie fournie. Ce point est indépendant de PTM mais mérite une vérification sur le site.

## Décision après audit

Le portage SPIP est confirmé comme techniquement pertinent.

La stratégie retenue est plus propre que de modifier chaque squelette :

- moteur de règles commun ;
- adaptateur SPIP pour découverte/stockage ;
- `insert_head` pour l'interface ;
- `affichage_final` pour un futur blocage léger ;
- scan actif progressif depuis l'espace privé ;
- génie SPIP pour la planification.

Prochaine étape : installer le plugin sur une copie de test du site et implémenter le premier scan réel avant tout mécanisme de blocage.
