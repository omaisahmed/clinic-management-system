<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Authentication\Enums\Role;
use Modules\Authentication\Http\Requests\StoreUserRequest;
use Modules\Authentication\Http\Requests\UpdateUserRequest;
use Modules\Authentication\Models\User;
use Modules\Authentication\Services\UserService;
use Modules\Audit\Facades\Audit;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users)
    {
    }

    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('roles')
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->paginate(15);

        return view('authentication::users.index', [
            'users' => $users,
            'roles' => Role::choices(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('authentication::users.create', [
            'roles' => Role::choices(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->users->create($request->validated());

        Audit::record('User Created', 'authentication', $user, [
            'user' => $user->email,
            'role' => $user->roles->first()?->name,
        ]);

        return redirect()
            ->route('authentication.users.index')
            ->with('toast', [['type' => 'success', 'message' => "User {$user->name} created."]]);
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('authentication::users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::choices(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->validated());

        Audit::record('User Updated', 'authentication', $user, [
            'user' => $user->email,
        ]);

        return redirect()
            ->route('authentication.users.index')
            ->with('toast', [['type' => 'success', 'message' => "User {$user->name} updated."]]);
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        Audit::record('User Deleted', 'authentication', $user, [
            'user' => $user->email,
        ]);

        return redirect()
            ->route('authentication.users.index')
            ->with('toast', [['type' => 'success', 'message' => 'User deleted.']]);
    }
}
