<?php

namespace App\Policies;

use App\Models\SeatingPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeatingPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Allow any authenticated user to view the list of seating plans
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SeatingPlan $seatingPlan)
    {
        // Allow any authenticated user to view a seating plan
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Only allow admin users to create seating plans
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to update seating plans
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to delete seating plans
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to restore seating plans
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to permanently delete seating plans
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can generate assignments for the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function generateAssignments(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to generate assignments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can save assignments for the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function saveAssignments(User $user, SeatingPlan $seatingPlan)
    {
        // Only allow admin users to save assignments
        return $user->hasRole('admin');
    }
}

