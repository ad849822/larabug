<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\FilterTicketRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TicketPriorityResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketTypeResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProjectTicketController extends Controller
{
    public function index(FilterTicketRequest $request, Project $project)
    {
        Gate::authorize('view-all-project-ticket', [$project]);

        return inertia('Projects/Tickets/Index', [
            'totalTicketCount' => fn () => $project->tickets()->count(),

            'openOrReopendedTicketCount' => fn () =>  $project->tickets()
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Reopended])
                ->count(),

            'unassignedTicketCount' => fn () => $project->tickets()
                ->whereDoesntHave('assignee')
                ->count(),

            'closedTicketCount' => fn () =>  $project->tickets()
                ->where('status', TicketStatus::Closed)
                ->count(),

            'tickets' => fn () => TicketResource::collection(
                $project->tickets()
                    ->with([
                        'priority' => fn ($q) => $q->select('id', 'name', 'color'),
                        'type' => fn ($q) => $q->select('id', 'name', 'color'),
                        'creator' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                        'assignee' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                        'project' => fn ($q1) => $q1->with(
                            ['devs' => fn ($q2) => $q2->with(
                                ['profile' => fn ($q3) => $q3->with('media')->select('id', 'name')]
                            )
                                ->select('users.id')
                                ->withForeignKeys()]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                    ])
                    ->when(
                        $request->get('searchQuery'),
                        function ($query, $searchQuery) use ($request)
                        {
                            $request->get('sortBy') ?
                                $query->search($searchQuery) :
                                $query->searchAndOrder($searchQuery);
                        }
                    )
                    ->when(
                        collect($request->get('filters'))->except('projects')->all(),
                        fn ($query, $filters) => $query->filter($filters)
                    )
                    ->when(
                        $request->get('sortBy'),
                        fn ($query, $sortBy) => $query->sort($sortBy),
                        fn ($query) => $query->orderBy('id', 'desc')
                    )
                    ->addSelect([
                        'can-view' => Ticket::from('tickets', 't')
                            ->canBeViewedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-edit' =>  Ticket::from('tickets', 't')
                            ->canBeModifiedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-delete' =>  Ticket::from('tickets', 't')
                            ->canBeDeletedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-assign' =>  Ticket::from('tickets', 't')
                            ->canBeAssignedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                    ])
                    ->withCasts([
                        'can-view' => 'boolean',
                        'can-edit' => 'boolean',
                        'can-delete' => 'boolean',
                        'can-assign' => 'boolean',
                    ])
                    ->paginate()
            ),

            'devs' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->whereHas(
                        'roles',
                        fn ($q) => $q->where('name', UserRole::Dev)
                    )
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'submitters' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'priorities' => fn () => TicketPriorityResource::collection(TicketPriority::all(['id', 'name'])),

            'types' => fn () => TicketTypeResource::collection(TicketType::all(['id', 'name'])),

            'projects' => fn () => ProjectResource::collection([$project]),

            'canAssignTicket' => fn () => User::canAssignTicketsForOwnProject()->whereKey($request->user()->id)->exists(),
        ]);
    }
}
