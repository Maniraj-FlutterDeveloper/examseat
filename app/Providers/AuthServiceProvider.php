<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SeatingPlan;
use App\Policies\SeatingPlanPolicy;
use App\Models\SeatingRule;
use App\Policies\SeatingRulePolicy;
use App\Models\StudentPriority;
use App\Policies\StudentPriorityPolicy;
use App\Models\SeatingOverride;
use App\Policies\SeatingOverridePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SeatingPlan::class => SeatingPlanPolicy::class,
        SeatingRule::class => SeatingRulePolicy::class,
        StudentPriority::class => StudentPriorityPolicy::class,
        SeatingOverride::class => SeatingOverridePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define a super-admin role that can do everything
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });

        // Custom gates for seating plan module
        Gate::define('manage-seating-plans', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-seating-plans', function ($user) {
            return $user->hasAnyRole(['admin', 'invigilator', 'staff']);
        });

        Gate::define('manage-seating-rules', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-student-priorities', function ($user) {
            return $user->hasAnyRole(['admin', 'staff']);
        });

        Gate::define('manage-seating-overrides', function ($user) {
            return $user->hasAnyRole(['admin', 'invigilator']);
        });
    }
}

