<?php

namespace App\GraphQL\Mutations;

use App\Models\Workspace;

final class WorkspaceMutator
{
    private $workspace;

    public function __construct(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    public function register($_, array $args)
    {
        return $this->workspace->register($args['input']);
    }
}
