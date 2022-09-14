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

class TicketChangePriority extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $showOnTableRow = true;

    public $name = 'Change Priority';

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
            $model->priority = $fields->priority;
            $model->save();
        }

        return Action::message('Priority was updated successfully');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make('Priority')->options([
                'P1' => 'P1',
                'P2' => 'P2',
                'P3' => 'P3',
            ])->rules('required')->sortable(),
        ];
    }
}
