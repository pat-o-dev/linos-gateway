<?php

namespace App\Service\Prestashop;

use App\Service\Prestashop\ApiClient;

class CustomerService
{
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
}