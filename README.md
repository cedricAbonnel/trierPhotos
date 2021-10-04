# trierPhotos

Script qui permet de scanner les fichiers du dossier courant.
Si le fichier est de format 'video' ou 'photo', il est déplacer dans le sous-dossier correspondant à la date de la prise de vue.
Si le fichier existe déjà (comparaison par hash du fichier complet), alors il est supprimé.

Fichier LOG générés dans le dossier courant avec le nom de format 'trierPhotos_YmdHis'

## Pré requis

```
dnf install php-cli
```

## Installer

Déposer le fichier **trierPhotos.php** dans ''/usr/bin/''

## Execution

```
php /usr/bin/trierPhotos.php
```

