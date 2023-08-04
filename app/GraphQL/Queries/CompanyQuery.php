<?php

namespace App\GraphQL\Queries;

use App\Models\Workspace;

final class WorkspaceQuery
{
    public $workspace;

    public function __construct(Workspace $workspace)
    {
        return $this->workspace = $workspace;    
    }

    public function workspaceHours($_, array $args)
    {
        return $this->workspace->workspaceHoursReport();
    }
}
