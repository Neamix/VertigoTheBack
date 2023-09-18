<?php 

namespace App\Repository\Project;

use App\Models\Option;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Str;

class ProjectRepository extends BaseRepository {
    
    public function model()
    {
        return Project::class;
    }

    /*** Upsert instance*/
    public function upsertInstance($projectData)
    {
        // Create project
        $project = Project::updateOrCreate([
            'id' => $projectData['id'] ?? null
        ],[
            'name' => $projectData['name'],
            'slug' => Str::slug($projectData['name']),
            'company_id'  => Auth::user()->active_company_id,
            'description' => $projectData['description'] ?? null,
        ]);

        // Sync Members
        if ( isset($projectData['accessableMembers']) ) {
            $project->accessableMembers()->sync($projectData['accessableMembers']);
        }

        // Update/Create inputs
        foreach ($projectData['inputs'] as $inputData) {
            $input = $project->inputs()->updateOrCreate([
                'id'    => $inputData['id'] ?? null
            ],[
                'type'  => $inputData['type'],
                'label' => $inputData['label'],
                'searchable'   => $inputData['searchable'] ?? false,
                'view_latest'  => $inputData['view_latest'] ?? false,
                'record_total' => $inputData['record_total'] ?? false,
                'record_avg'   => $inputData['record_avg'] ?? false
            ]);

            // Update/Create inputs
            if ( isset($inputData['options']) ) {
                foreach($inputData['options'] as $option) {
                    $input->options()->updateOrCreate([
                        'id' => $option['id'] ?? null,
                    ],[
                        'value' => $option['value'] ?? null,
                        'sort'  => 1
                    ]);
                }
            }
        }

        return $project;
    }

    /** Filter Project */
    public function filter($args)
    {
        return Project::filter($args['input'])->paginate($args['first']);
    }
}
