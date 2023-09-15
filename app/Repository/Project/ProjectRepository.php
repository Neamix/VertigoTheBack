<?php 

namespace App\Repository\Project;

use App\Models\Option;
use App\Models\Project;
use Prettus\Repository\Eloquent\BaseRepository;

class ProjectRepository extends BaseRepository {

    public function model()
    {
        return Project::class;
    }

    /**
     * Upsert instance
     * @param Project upsert project
     * @return array
    */
    public function upsertInstance($projectData)
    {
        // Create project
        $project = Project::updateOrCreate([
            'id' => $projectData['id'] ?? null
        ],[
            'name' => $projectData['name'],
            'description' => $projectData['description'] ?? null
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


    /** filter Project 
     *  @param args 
     *  @return Paginate
    */

    public function filter($args)
    {
        return Project::filter($args)->paginate($args['first']);
    }

}
