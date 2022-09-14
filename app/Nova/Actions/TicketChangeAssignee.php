<?php

namespace App\Nova\Actions;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Fields\Select;

class TicketChangeAssignee extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $showOnTableRow = true;

    public $name = 'Change Assignee';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->assigned_to = $fields->assigned_to;
            $model->save();
        }

        return Action::message('Assignee was changed successfully');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        $users = User::where('access_level', '!=', 'Shop Staff')->get();
        $assignee = [];
        foreach ($users as $user) {
            $assignee[$user->id] = $user->name;
        }

        return [
            Select::make('Assigned To', 'assigned_to')->options($assignee)->rules('required'),
        ];
    }
}
