<?php

namespace App\GraphQL\Queries;

use App\Repository\Project\ProjectRepository;

final class ProjectQuery
{
    protected $projectRepository;

    public function __construct(ProjectRepository $projectRepository) 
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    /*** Filter Project In Company */
    public function filterProject($_, array $args)
    {
        return $this->projectRepository->filter($args);
    }
}
