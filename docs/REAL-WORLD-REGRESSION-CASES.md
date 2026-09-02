# Cas de régression issus d'une installation SPIP réelle

Ce document transforme des observations d'un vrai site SPIP en cas de test génériques. Il ne contient ni contenu client, ni identifiant, ni secret.

## 1. Formulaire local de contact

Cas observé : formulaire SPIP local collectant une adresse e-mail, un sujet et un message.

Attendu PTM :

- détecter une collecte de données ;
- identifier au minimum `email` et `message` dans les champs rendus ;
- ne pas conclure à un traceur ;
- ne pas conclure à un transfert tiers si l'action du formulaire reste locale.

## 2. Anti-spam local

Cas observé : champ piège local utilisé comme protection anti-spam.

Attendu PTM :

- ne pas détecter reCAPTCHA, hCaptcha ou Turnstile sans signature technique correspondante ;
- ne pas transformer une protection locale en service tiers.

## 3. Plugins locaux connus

Profils rencontrés : `crayons`, `image_typo`, `squelettes_par_rubrique`.

Attendu PTM :

- les reconnaître comme profils connus ;
- statut `local_only` ;
- aucune alerte de tracking du seul fait de leur présence ;
- continuer à scanner le HTML rendu, car un plugin local peut modifier le rendu sans être lui-même un traceur.

## 4. Injection via le rendu SPIP

Cas observé : usage de `#INSERT_HEAD` et de squelettes personnalisés.

Attendu PTM :

- privilégier l'analyse du HTML final ;
- ne pas considérer l'absence d'une signature dans les fichiers de squelette comme preuve d'absence sur le site public.

## 5. URLs externes dans les commentaires de JavaScript

Cas observé : anciens scripts locaux contenant des URL de documentation/licence dans leurs commentaires.

Attendu PTM pour un futur scanner statique :

- une URL trouvée dans un commentaire ne constitue pas une preuve de requête réseau ;
- retirer ou ignorer les commentaires avant d'appliquer les signatures de services au code statique ;
- confirmer les services via HTML rendu ou observation réseau lorsque possible.

## 6. Plugin installé vs actif vs service observé

Attendu PTM : maintenir trois niveaux distincts.

1. **Présent dans les fichiers** : information de contexte uniquement.
2. **Actif selon SPIP** : capacité ou traitement potentiel selon le profil.
3. **Service observé dans le rendu/réseau** : preuve technique active.

Ce jeu de cas doit servir de référence à chaque évolution du scanner afin de limiter les régressions et les faux positifs.
