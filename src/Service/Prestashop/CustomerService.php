<?php

namespace App\Service\Prestashop;

use App\Service\Prestashop\ApiClient;

class CustomerService
{
    private array $requiredFields = [
        'email',
        'password',
        'firstname',
        'lastname'
    ];
    
    private array $allowedFields = [
        'id',
        'id_lang',
        'id_default_group',
        'lastname',
        'firstname',
        'email',
        'id_gender',
        'birthday',
        'company',
        'siret',
        'ape',
        'date_add',
        'date_upd',
    ];

    public function __construct(private ApiClient $apiClient) {}

    public function authenticate(string $email, string $password): ?array
    {
        $customer = $this->apiClient->getCustomerByEmail($email);

        if (!$customer) {
            return null;
        }

        $storedHash = $customer['passwd'] ?? null;
        if (!$storedHash || !password_verify($password, $storedHash)) {
            return null;
        }

        return array_intersect_key($customer, array_flip($this->allowedFields));
    }

    public function register($email, $password, $firstname, $lastname)
    {
        if (!$email || !$password || !$firstname || !$lastname) {
            return ['error' => 'Missing required fields'];
        }
            
        $customer = $this->apiClient->getCustomerByEmail($email);
        if ($customer) {
            return ['error' => 'Email already used'];
        }

        $payload = [
            'customer' => [
                'firstname'   => $firstname,
                'lastname'    => $lastname,
                'email'       => $email,
                'passwd'      => $password,
                'id_default_group' => 1,
                'active'      => 1,
                'id_lang'     => 1,
            ]
        ];

        try {
            $newCustomer = $this->apiClient->register($payload);
        } catch (\Exception $e) {
            return ['error' => 'Failed to create customer: ' . $e->getMessage()];
        }

        if (!$newCustomer) {
            return ['error' => 'Failed to create customer'];
        }

        return array_intersect_key($newCustomer, array_flip($this->allowedFields));

    }


}