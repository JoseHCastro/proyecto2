<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $role = $request->input('role');
            if ($role === 'personal') {
                $query->role(['Propietario', 'Secretaria', 'Instructor']);
            } elseif ($role === 'cliente') {
                $query->role('Cliente');
            }
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'ci' => 'nullable|string|max:20|unique:users',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|string|in:activo,inactivo',
            'role' => 'required|string|exists:roles,name',
            'especialidades' => 'nullable|string',
            'biografia' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'],
            'estado' => $validated['estado'],
            'especialidades' => $validated['especialidades'],
            'biografia' => $validated['biografia'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');
        
        // Si el usuario es Cliente, cargar sus mediciones de progreso y asistencias
        $mediciones = [];
        $asistencias = [];
        if ($user->hasRole('Cliente')) {
            $mediciones = \App\Models\MedicionProgreso::where('socio_id', $user->id)
                ->orderBy('medido_en', 'desc')
                ->get();
            
            $asistencias = \App\Models\Asistencia::where('socio_id', $user->id)
                ->orderBy('asistio_en', 'desc')
                ->get();
        }
        
        return Inertia::render('Users/Show', [
            'user' => $user,
            'mediciones' => $mediciones,
            'asistencias' => $asistencias,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => Role::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'ci' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|string|in:activo,inactivo',
            'role' => 'required|string|exists:roles,name',
            'especialidades' => 'nullable|string',
            'biografia' => 'nullable|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'],
            'estado' => $validated['estado'],
            'especialidades' => $validated['especialidades'],
            'biografia' => $validated['biografia'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
