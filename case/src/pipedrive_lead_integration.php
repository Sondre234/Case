<?php

class pipedrive_lead_integration  {
    private $apiDomain = "https://nettbureauasdevelopmentteam.pipedrive.com";
    private $apiKey = "fb3d059dddbeec5d09988cc8669228b09df4be15 ";

    public function createLead($leadData) {
        try {
            // Opprett organisasjon
            $organizationId = $this->createOrganization("Default Organization");

            // Opprett person
            $personId = $this->createPerson([
                "name" => $leadData["name"],
                "email" => $leadData["email"],
                "phone" => $leadData["phone"],
                "org_id" => $organizationId,
                "contact_type" => $this->mapFieldValue($leadData["contact_type"], [
                    "Privat" => 30,
                    "Borettslag" => 31,
                    "Bedrift" => 32
                ])
            ]);

            // Opprett lead
            $this->createLeadEntry([
                "title" => "New Lead from Strøm.no",
                "person_id" => $personId,
                "organization_id" => $organizationId,
                "custom_fields" => [
                    "housing_type" => $this->mapFieldValue($leadData["housing_type"], [
                        "Enebolig" => 33,
                        "Leilighet" => 34,
                        "Tomannsbolig" => 35,
                        "Rekkehus" => 36,
                        "Hytte" => 37,
                        "Annet" => 38
                    ]),
                    "property_size" => isset($leadData["property_size"]) ? $leadData["property_size"] : null,
                    "deal_type" => $this->mapFieldValue($leadData["deal_type"], [
                        "Alle strømavtaler er aktuelle" => 39,
                        "Fastpris" => 40,
                        "Spotpris" => 41,
                        "Kraftforvaltning" => 42,
                        "Annen avtale/vet ikke" => 43
                    ]),
                    "comment" => isset($leadData["comment"]) ? $leadData["comment"] : ""
                ]
            ]);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }

    private function createOrganization($name) {
        $data = ["name" => $name];
        return $this->makeRequest("/v1/organizations", $data);
    }

    private function createPerson($data) {
        return $this->makeRequest("/v1/persons", $data);
    }

    private function createLeadEntry($data) {
        $mappedData = [
            "title" => $data["title"],
            "person_id" => $data["person_id"],
            "organization_id" => $data["organization_id"],
            "9cbbad3c5d83d6d258ef27db4d3784b50d5fd32" => $data["custom_fields"]["housing_type"],
            "7a275c324d7fbe5ab62c9f05bfbe87dad3acc3ba" => $data["custom_fields"]["property_size"],
            "cebe4ad7ce36c3508c3722b6e0072c6de5250586" => $data["custom_fields"]["deal_type"],
            "479370d7514958b2b4b4049c37be492f357fe7d8" => $data["custom_fields"]["comment"]
        ];
        return $this->makeRequest("/v1/leads", $mappedData);
    }

    private function makeRequest($endpoint, $data) {
        $url = $this->apiDomain . $endpoint . "?api_token=" . $this->apiKey;
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_RETURNTRANSFER => true
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            throw new Exception("API request failed with response: " . $response);
        }

        $result = json_decode($response, true);
        return isset($result["data"]["id"]) ? $result["data"]["id"] : null;
    }

    private function mapFieldValue($value, $mapping) {
        return isset($mapping[$value]) ? $mapping[$value] : null;
    }
}

// Test data
$testData = [
    "name" => "Sondre Myrmel",
    "phone" => "95130983",
    "email" => "Sondrefmyrmel@gmail.com",
    "housing_type" => "Enebolig",
    "property_size" => 160,
    "deal_type" => "Spotpris",
    "contact_type" => "Privat",
    "comment" => "Interessert i å skifte strømleverandør"
];

// Execute
try {
    $integration = new pipedrive_lead_integration();
    $integration->createLead($testData);
    echo "Lead created successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
