<?php 

namespace App\Repository\User;

use Prettus\Repository\Eloquent\BaseRepository;

class UserStatisticsRepository extends BaseRepository {
    public function model()
    {
        return User::class;
    }
}