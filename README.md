# Case
integration case

# Pipedrive Lead Integration

## Introduksjon
Dette prosjektet er et PHP-script som integrerer leads fra Strøm.no inn i kundens Pipedrive-konto. Scriptet oppretter en organisasjon, en person og en lead, og benytter egendefinerte felter for å tilpasse dataene.

## Oppsett
1. Klon repoet eller pakk ut ZIP-filen.
2. Installer PHP og cURL om det ikke allerede er installert.
3. Oppdater `apiKey` og `apiDomain` i `pipedrive_lead_integration.php` med kundens detaljer.

## Kjøre instruksjoner
1. Legg til testdata i `$testData` i `pipedrive_lead_integration.php`.
2. Kjør scriptet:
   ```bash
   php src/pipedrive_lead_integration.php
