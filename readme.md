om te checken of de gebruiker ingelogd is:
1- php bin/console make:auth
2- in de security folder de functine onAuthenticationSuccess nieuwe route aanmaken.
3- in de templates folder, met een if statement checken of de gebruiker ingelogd is met is_granted('ROLE_USER').