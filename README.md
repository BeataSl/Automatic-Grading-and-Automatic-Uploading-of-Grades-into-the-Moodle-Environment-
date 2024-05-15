# Bakalaureusetöö: “Andmeturve” kursuse näitel automaathindamise realiseerimine ja hinnete automaatne laadimine Moodle’i keskkonda


Käesolevas bakalaureusetöös töötati välja süsteem automaatseks hindamiseks kahe esimese praktilise töö näitel kursusel “LTAT.06.002 Andmeturve”, samuti süsteem hinnete automaatseks ülekandmiseks Moodle'i keskkonda.

## Failide Kirjeldus

- **Praktikum1** ja **Praktikum2** - Kaustad, mis sisaldavad näidisfaile üliõpilaste töödega. Need on lisatud testimaks skripte automaatseks hindamiseks.
- **hindaminePraktikum1.php** - Skript esimese praktikumi automaatseks hindamiseks. Genereerib faili `resultPraktikum1.csv`, mis sisaldab kõiki vajalikke andmeid hinnete automaatseks ülekandmiseks Moodle'isse.
- **hindaminePraktikum2.php** - Skript teise praktikumi automaatseks hindamiseks. Genereerib faili `resultPraktikum2.csv`, mis sisaldab kõiki vajalikke andmeid hinnete automaatseks ülekandmiseks Moodle'isse.
- **passwords.txt** - Täiendav fail teise praktikumi ülesannete kontrollimiseks.
- **hinneteÜlekandmine.php** - Skript hinnete ülekandmiseks Moodle'i keskkonda.


## Skriptide Käivitamise Juhend

### Automaathindamise skripti  käivitamine

Hindamisskriptide käivitamiseks tuleb määrata teed kaustadele, kus asuvad üliõpilaste tööd (näiteks `Praktikum1` või `Praktikum2` kaustad).

#### MacOS ja Linux
1. Ava terminal.
2. Navigeeri kausta, kus asub hindamisskript:
    ```sh
    cd /tee/sinu/skriptile/
    ```
3. Käivita skript:
    ```sh
    php hindaminePraktikum1.php
    php hindaminePraktikum2.php
    ```

#### Windows
1. Ava Command Prompt (CMD) või PowerShell.
2. Navigeeri kausta, kus asub hindamisskript:
    ```sh
    cd \tee\sinu\skriptile
    ```
3. Käivita skript:
    ```sh
    php hindaminePraktikum1.php
    php hindaminePraktikum2.php
    ```


### Hinnete automaatse ülekandmise põhiskripti käivitamine

Hinnete ülekandmise skripti käivitamiseks tuleb määrata teed CSV-failile, mis sisaldab vajalikke andmeid hinnete ülekandmiseks (näiteks `resultPraktikum1.csv` või `resultPraktikum2.csv`), kursuse indefikaatori muutujasse $courseId = 9, ja tõendi (token) muutujasse $token.

#### MacOS ja Linux
1. Ava terminal.
2. Navigeeri kausta, kus asub hinnete ülekandmise skript:
    ```sh
    cd /tee/sinu/skriptile
    ```
3. Käivita skript:
    ```sh
    php hinneteÜlekandmine.php
    ```

#### Windows
1. Ava Command Prompt (CMD) või PowerShell.
2. Navigeeri kausta, kus asub hinnete ülekandmise skript:
    ```sh
    cd \tee\sinu\skriptile
    ```
3. Käivita skript:
    ```sh
    php hinneteÜlekandmine.php
    ```

### PHP Paigaldamine

Kui PHP ei ole veel süsteemis paigaldatud, saab selle paigaldada järgnevate käskudega:

#### MacOS

```sh
brew install php
```

#### Linux (Ubuntu)

  **Debian/Ubuntu:**
        ```sh
        sudo apt update
        sudo apt install php
        ```
  
**CentOS/RHEL:**
        ```sh
        sudo yum install php
        ```
  
**Fedora:**
        ```sh
        sudo dnf install php
        ```

#### Windows

1. Laadi PHP alla siit: https://www.php.net/downloads
2. Järgi instruktsiooni, mis on saadaval siin: https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/

