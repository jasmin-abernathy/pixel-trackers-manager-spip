# Audit d'un site SPIP avant portage réel

Quand une copie représentative du site sera disponible, analyser en priorité :

## Fichiers utiles

- version exacte de SPIP ;
- `plugins/` et liste des plugins actifs ;
- `plugins-dist/` uniquement si nécessaire pour comprendre les versions présentes ;
- `squelettes/` et tout dossier de squelettes personnalisé ;
- `config/mes_options.php` et `config/mes_fonctions.php` si présents, après retrait des secrets ;
- modèles, formulaires CVT, JS/CSS spécifiques et inclusions externes ;
- exemples de pages publiques qui embarquent vidéos, cartes, formulaires, analytics ou polices externes.

## Optionnel ensuite

Un dump SQL anonymisé pourra être utile pour comprendre les réglages, contenus et usages réels. Il n'est pas nécessaire pour la toute première analyse de structure.

## Ne pas fournir

- `config/connect.php` réel ;
- mots de passe BDD ;
- secrets SMTP/API ;
- cookies/sessions ;
- logs contenant des données personnelles ;
- sauvegarde brute d'un site client non anonymisée.

## Questions auxquelles l'audit doit répondre

1. Où sont injectés les scripts et iframes tiers ?
2. Quels plugins produisent des contenus externes ?
3. Quels squelettes sont réellement actifs ?
4. Comment recenser toutes les URLs publiques sans crawler des zones privées ?
5. Où stocker scans et historique sans alourdir SPIP ?
6. Peut-on neutraliser/réactiver les services facultatifs sans casser le cache ?
7. Comment représenter les pages légales du site concerné ?
8. Quelles fonctions peuvent rester dans un moteur commun avec PTM WordPress ?
