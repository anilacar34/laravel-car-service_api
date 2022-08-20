<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class GeneralObserver
{
    /**
     * Listen to the User creating event.
     */
    public function creating(Model $model)
    {
        $model->created_by = auth()->user()->id ?? 1;
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(Model $model)
    {

    }

    /**
     * Listen to the User updating event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(Model $model)
    {
        $model->updated_by = auth()->user()->id ?? 1;
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(Model $model)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(Model $model)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(Model $model)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(Model $model)
    {
        //
    }
}
