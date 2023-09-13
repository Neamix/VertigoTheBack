<?php

namespace App\GraphQL\Mutations;

use App\Repository\Project\ProjectRepository;

final class ProjectMutation
{
    protected $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function upsertInstance($_, array $args)
    {
        return $this->projectRepository->upsertInstance($args['input']);
    }
}
