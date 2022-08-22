<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'usagers';
    protected $returnType = 'App\Entities\User';
    protected $allowedFields = ['nom', 'courriel', 'telephone'];

    // Dates
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nom' => 'required|min_length[4]|max_length[120]',
        'courriel' => 'required|valid_email|is_unique[usagers.courriel]',
        'assurance_maladie' => 'required',
        'mot_de_passe' => 'required|min_length[3]',
        'confirm_mot_de_passe' => 'required_with[mot_de_passe]|matches[mot_de_passe]',
    ];
    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom est obligatoire',
        ],
        'courriel' => [
            'required' => 'Le courriel est obligatoire.',
            'is_unique' => 'Désolé, mais ce courriel existe déjà !',
        ],
        'assurance_maladie' => [
            'required' => 'Le numéro d\'assurance maladie est obligatoire.',
        ],

    ];

    //Evénéments callback
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['mot_de_passe'])) {
            $data['data']['password_hash'] = password_hash($data['data']['mot_de_passe'], PASSWORD_DEFAULT);
            unset($data['data']['mot_de_passe']);
            unset($data['data']['confirm_mot_de_passe']);
        }

        return $data;
    }

    public function recherche_usager($term)
    {
        if ($term === null) {
            return [];
        }

        return $this->select('id, nom')
            ->like('nom', $term)
            ->get()
            ->getResult();
    }

    public function pasDeValidationMotDePasse()
    {
        unset($this->validationRules['mot_de_passe']);
        unset($this->validationRules['confirm_mot_de_passe']);
    }
}
