# trierPhotos

Script PHP qui permet de scanner les fichiers de type video et photo du dossier courant.
Le fichier est déplacé dans le sous-dossier correspondant à la date de la prise de vue au format 'Y/m'.
Si le fichier existe déjà (comparaison par hash du fichier complet), alors il est supprimé.

Fichiers LOG générés dans le dossier courant avec le nom de format 'trierPhotos_YmdHis'

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

